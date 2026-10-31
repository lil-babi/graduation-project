<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Quote;
use App\Models\ReadingLog;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected Book $book;

    protected User $user;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Philosophy',
            'slug' => 'philosophy',
            'description' => 'Stoic and classical thought.',
        ]);

        $this->book = Book::create([
            'category_id' => $this->category->id,
            'title' => 'Meditations',
            'author' => 'Marcus Aurelius',
            'page_count' => 200,
            'publication_year' => 180,
            'description' => 'Stoic philosophy notes.',
            'is_published' => true,
        ]);

        $this->user = User::create([
            'name' => 'Standard Reader',
            'email' => 'reader@test.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'daily_goal_pages' => 20,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Operator',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'daily_goal_pages' => 30,
        ]);
    }

    /**
     * AT-001: Register with valid details
     */
    public function test_at001_register_with_valid_details(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);

        $user = User::where('email', 'newuser@test.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * AT-002: Register with invalid or duplicate email
     */
    public function test_at002_register_with_duplicate_email(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Duplicate User',
            'email' => 'reader@test.com', // Already exists in setUp
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * AT-003: Login with valid credentials
     */
    public function test_at003_login_with_valid_credentials(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'reader@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * AT-004: Browse, search, and filter books
     */
    public function test_at004_browse_search_filter_books(): void
    {
        $response = $this->get(route('books.index', ['search' => 'Meditations']));
        $response->assertOk();
        $response->assertSee('Meditations');

        $responseEmpty = $this->get(route('books.index', ['search' => 'NonExistentBookXYZ']));
        $responseEmpty->assertOk();
        $responseEmpty->assertSee('No books found');
    }

    /**
     * AT-005: Add same book twice to personal library
     */
    public function test_at005_prevent_duplicate_library_entries(): void
    {
        $this->actingAs($this->user);

        // First addition succeeds
        $res1 = $this->post(route('library.store'), [
            'book_id' => $this->book->id,
            'status' => 'reading',
        ]);
        $res1->assertSessionHas('success');
        $this->assertDatabaseCount('user_books', 1);

        // Second addition is caught and prevented
        $res2 = $this->post(route('library.store'), [
            'book_id' => $this->book->id,
            'status' => 'reading',
        ]);
        $res2->assertSessionHas('info');
        $this->assertDatabaseCount('user_books', 1);
    }

    /**
     * AT-006: Record 5 pages when goal is 20
     */
    public function test_at006_record_pages_updates_progress_and_counts_as_reading_day(): void
    {
        $this->actingAs($this->user);

        $today = Carbon::today()->toDateString();
        $response = $this->post(route('progress.store'), [
            'pages_read' => 5,
            'reading_date' => $today,
            'book_id' => $this->book->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(5, $this->user->todayPagesRead());
        $this->assertEquals(1, $this->user->calculateCurrentStreak());
    }

    /**
     * AT-007: Record zero pages
     */
    public function test_at007_record_zero_pages_rejected(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('progress.store'), [
            'pages_read' => 0,
            'reading_date' => Carbon::today()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['pages_read']);
    }

    /**
     * AT-008: Miss a calendar day breaks consecutive streak
     */
    public function test_at008_miss_calendar_day_breaks_streak(): void
    {
        // Day before yesterday: 10 pages read
        ReadingLog::create([
            'user_id' => $this->user->id,
            'reading_date' => Carbon::today()->subDays(2)->toDateString(),
            'pages_read' => 10,
        ]);

        // Yesterday: 0 pages read (missed day)
        // Today: 0 pages read yet
        $this->assertEquals(0, $this->user->calculateCurrentStreak());
    }

    /**
     * AT-009: Edit another user's review or quote is denied
     */
    public function test_at009_edit_other_user_review_or_quote_denied(): void
    {
        $otherUser = User::create([
            'name' => 'Other Reader',
            'email' => 'other@test.com',
            'password' => Hash::make('password123'),
        ]);

        $review = Review::create([
            'user_id' => $otherUser->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'body' => 'Other user original review',
        ]);

        $quote = Quote::create([
            'user_id' => $otherUser->id,
            'text' => 'Other user original quote',
        ]);

        // Log in as standard user and attempt to edit/delete
        $this->actingAs($this->user);

        $resReview = $this->put(route('reviews.update', $review), [
            'rating' => 1,
            'body' => 'Hacked review',
        ]);
        $resReview->assertForbidden();

        $resQuote = $this->put(route('quotes.update', $quote), [
            'text' => 'Hacked quote',
        ]);
        $resQuote->assertForbidden();
    }

    /**
     * AT-010: Free user opens Premium endpoint directly
     */
    public function test_at010_free_user_opening_premium_endpoint_redirected(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('premium.analytics'));
        $response->assertRedirect(route('pricing'));
        $response->assertSessionHas('warning');
    }

    /**
     * AT-012: Complete simulated checkout
     */
    public function test_at012_complete_simulated_checkout(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('subscription.process-simulated'), [
            'confirm_demo' => '1',
        ]);

        $response->assertRedirect(route('premium.analytics'));

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_code' => 'premium',
            'status' => 'active',
            'payment_mode' => 'simulated',
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'payment_mode' => 'simulated',
            'amount' => 99.00,
        ]);

        $this->assertTrue($this->user->fresh()->isPremium());
    }

    /**
     * AT-013: Admin views revenue separating simulated from real
     */
    public function test_at013_admin_views_revenue_separation(): void
    {
        // Add a simulated transaction
        Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 99.00,
            'currency' => 'ETB',
            'status' => 'completed',
            'payment_mode' => 'simulated',
            'provider_reference' => 'SIM-12345',
            'paid_at' => now(),
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports.index'));
        $response->assertOk();
        $response->assertSee('99.00'); // Simulated total
        $response->assertSee('0.00'); // Real confirmed revenue is zero
    }

    /**
     * AT-014: Expired subscription denies Premium feature access
     */
    public function test_at014_expired_subscription_denies_premium_access(): void
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan_code' => 'premium',
            'status' => 'expired',
            'starts_at' => now()->subDays(60),
            'ends_at' => now()->subDays(1),
            'payment_mode' => 'simulated',
        ]);

        $this->actingAs($this->user);
        $this->assertFalse($this->user->fresh()->isPremium());

        $response = $this->get(route('premium.analytics'));
        $response->assertRedirect(route('pricing'));
    }

    /**
     * AT-015: Non-admin visits admin route
     */
    public function test_at015_non_admin_visits_admin_route_forbidden(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('admin.dashboard'));
        $response->assertForbidden();
    }

    /**
     * AT-016: Upload oversized or invalid file
     */
    public function test_at016_reject_invalid_file_upload(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $invalidFile = UploadedFile::fake()->create('malicious.exe', 100);

        $response = $this->post(route('admin.books.store'), [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $this->category->id,
            'page_count' => 100,
            'publication_year' => 2024,
            'description' => 'Test description',
            'pdf_file' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['pdf_file']);
    }

    /**
     * AT-017: Graceful handling of missing PDF
     */
    public function test_at017_missing_pdf_handled_gracefully(): void
    {
        $this->actingAs($this->user);

        // Book has no pdf_path set
        $response = $this->get(route('books.read', $this->book));
        $response->assertRedirect(route('books.show', $this->book));
        $response->assertSessionHas('warning');
    }
}
