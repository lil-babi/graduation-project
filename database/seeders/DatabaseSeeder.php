<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Quote;
use App\Models\ReadingLog;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBook;
use App\Services\BookContentProvider;
use App\Services\PdfBookGenerator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Categories (SRS FR-024)
        $categoriesData = [
            ['name' => 'Self Development', 'slug' => 'self-development', 'description' => 'Personal growth, habit formation, and character building.'],
            ['name' => 'Finance', 'slug' => 'finance', 'description' => 'Personal wealth, economics, investment, and financial wisdom.'],
            ['name' => 'Philosophy', 'slug' => 'philosophy', 'description' => 'Classical thought, stoicism, ethics, and contemplative inquiry.'],
            ['name' => 'Psychology', 'slug' => 'psychology', 'description' => 'Human behavior, cognitive insights, and mental models.'],
            ['name' => 'Productivity', 'slug' => 'productivity', 'description' => 'Deep work, time management, and execution mastery.'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Entrepreneurship, leadership, and organizational strategy.'],
            ['name' => 'History', 'slug' => 'history', 'description' => 'Civilizations, biography, and historical turning points.'],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'General literature, essays, and multidisciplinary works.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::create($c);
        }

        // 2. Users (Admin, Free Reader, Premium Reader)
        $admin = User::create([
            'name' => 'Admin Operator',
            'email' => 'admin@bookplatform.et',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'daily_goal_pages' => 30,
            'preferences' => ['favorite_category' => 'philosophy'],
        ]);

        $freeReader = User::create([
            'name' => 'Abebe Bikila',
            'email' => 'reader@bookplatform.et',
            'password' => Hash::make('password'),
            'role' => 'user',
            'daily_goal_pages' => 20,
            'preferences' => ['favorite_category' => 'self-development'],
        ]);

        Subscription::create([
            'user_id' => $freeReader->id,
            'plan_code' => 'free',
            'status' => 'active',
            'price_amount' => 0.00,
            'currency' => 'ETB',
            'starts_at' => now()->subDays(60),
            'payment_mode' => 'simulated',
        ]);

        $premiumReader = User::create([
            'name' => 'Sara Haile',
            'email' => 'premium@bookplatform.et',
            'password' => Hash::make('password'),
            'role' => 'user',
            'daily_goal_pages' => 25,
            'preferences' => ['favorite_category' => 'philosophy'],
        ]);

        $premSub = Subscription::create([
            'user_id' => $premiumReader->id,
            'plan_code' => 'premium',
            'status' => 'active',
            'price_amount' => 99.00,
            'currency' => 'ETB',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'payment_mode' => 'simulated',
        ]);

        Transaction::create([
            'user_id' => $premiumReader->id,
            'subscription_id' => $premSub->id,
            'amount' => 99.00,
            'currency' => 'ETB',
            'status' => 'completed',
            'payment_mode' => 'simulated',
            'provider_reference' => 'SIM-DEMO-001',
            'paid_at' => now()->subDays(10),
        ]);

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('books/pdfs');
        Storage::disk('public')->makeDirectory('covers');

        // Helper to generate readable multi-page PDF files with real chapters and text
        $generatePdf = function (string $title, string $author, string $categoryName, int $year, string $synopsis, string $coverSlug): string {
            $filename = 'books/pdfs/'.Str::slug($title).'.pdf';

            $generator = new PdfBookGenerator($title, $author, $categoryName, $year);
            $generator->addTitlePage($synopsis);

            $data = BookContentProvider::getBookData($coverSlug);
            $generator->addTableOfContents($data['chapters']);

            foreach ($data['content'] as $chapter) {
                $generator->startChapter($chapter['title'], $chapter['subtitle'] ?? '');
                foreach ($chapter['paragraphs'] as $para) {
                    $generator->addParagraph($para);
                }
                if (! empty($chapter['takeaway'])) {
                    $generator->addKeyTakeaway($chapter['takeaway']);
                }
            }

            Storage::disk('public')->put($filename, $generator->render());

            return $filename;
        };

        // 3. Books Catalog (Strictly Complete Unabridged Public Domain Classics)
        $booksData = [
            [
                'title' => 'The Principles of Psychology',
                'author' => 'William James',
                'category_slug' => 'psychology',
                'publication_year' => 1890,
                'page_count' => 689,
                'cover_slug' => 'principles-of-psychology',
                'description' => 'A monumental work introducing habit formation, consciousness, the stream of thought, and volition, creating the bedrock for modern behavioral science.',
            ],
            [
                'title' => 'The Republic',
                'author' => 'Plato',
                'category_slug' => 'philosophy',
                'publication_year' => 375,
                'page_count' => 633,
                'cover_slug' => 'the-republic',
                'description' => 'Socrates and his Athenian interlocutors discuss the meaning of justice, ideal state governance, philosopher-kings, and the nature of the virtuous human soul.',
            ],
            [
                'title' => 'Self-Reliance and Other Essays',
                'author' => 'Ralph Waldo Emerson',
                'category_slug' => 'self-development',
                'publication_year' => 1841,
                'page_count' => 246,
                'cover_slug' => 'self-reliance',
                'description' => "Emerson's foundational declaration of intellectual and spiritual independence. He urges individuals to trust their intuition and resist unthinking societal conformity.",
            ],
            [
                'title' => 'Autobiography of Benjamin Franklin',
                'author' => 'Benjamin Franklin',
                'category_slug' => 'history',
                'publication_year' => 1791,
                'page_count' => 175,
                'cover_slug' => 'autobiography-of-benjamin-franklin',
                'description' => 'Franklin recounts his rise from poverty to statesman, scientist, and philosopher, famously documenting his method of systematic daily virtue tracking.',
            ],
            [
                'title' => 'Meditations',
                'author' => 'Marcus Aurelius',
                'category_slug' => 'philosophy',
                'publication_year' => 180,
                'page_count' => 165,
                'cover_slug' => 'meditations',
                'description' => 'A series of personal writings by Roman Emperor Marcus Aurelius setting forth his private notes to himself and ideas on Stoic philosophy. It offers timeless wisdom on duty, resilience, emotional balance, and mortality.',
            ],
            [
                'title' => 'Beyond Good and Evil',
                'author' => 'Friedrich Nietzsche',
                'category_slug' => 'philosophy',
                'publication_year' => 1886,
                'page_count' => 156,
                'cover_slug' => 'beyond-good-and-evil',
                'description' => 'Nietzsche attacks past philosophers for their blind acceptance of moral dogmas and calls for an active, courageous re-evaluation of all human values.',
            ],
            [
                'title' => 'The Art of War',
                'author' => 'Sun Tzu',
                'category_slug' => 'business',
                'publication_year' => 1782,
                'page_count' => 127,
                'cover_slug' => 'the-art-of-war',
                'description' => 'An ancient military treatise providing profound strategic doctrines. Widely adapted by entrepreneurs and business leaders to master competition, positioning, and psychological resilience.',
            ],
            [
                'title' => 'The Science of Getting Rich',
                'author' => 'Wallace D. Wattles',
                'category_slug' => 'finance',
                'publication_year' => 1910,
                'page_count' => 78,
                'cover_slug' => 'the-science-of-getting-rich',
                'description' => 'A practical guide outlining how cultivating gratitude, purposeful action, and a mindset of abundance creates real material success.',
            ],
            [
                'title' => 'As a Man Thinketh',
                'author' => 'James Allen',
                'category_slug' => 'self-development',
                'publication_year' => 1903,
                'page_count' => 25,
                'cover_slug' => 'as-a-man-thinketh',
                'description' => "An essay on how character, circumstances, health, and achievements are direct reflections of our innermost habitual thoughts. Allen demonstrates how mastering one's mind transforms life.",
            ],
            [
                'title' => 'The Richest Man in Babylon',
                'author' => 'George S. Clason',
                'category_slug' => 'finance',
                'publication_year' => 1926,
                'page_count' => 11,
                'cover_slug' => 'the-richest-man-in-babylon',
                'description' => 'Financial parables set in ancient Babylon providing timeless principles on saving, investing, protecting capital, and building enduring wealth.',
            ],
        ];

        $books = [];
        foreach ($booksData as $bData) {
            $cat = $categories[$bData['category_slug']];
            $coverRelativePath = 'covers/'.$bData['cover_slug'].'.jpg';

            $pdfPath = $generatePdf(
                $bData['title'],
                $bData['author'],
                $cat->name,
                $bData['publication_year'],
                $bData['description'],
                $bData['cover_slug']
            );

            $book = Book::create([
                'category_id' => $cat->id,
                'title' => $bData['title'],
                'author' => $bData['author'],
                'publication_year' => $bData['publication_year'],
                'page_count' => $bData['page_count'],
                'description' => $bData['description'],
                'cover_path' => Storage::disk('public')->exists($coverRelativePath) ? $coverRelativePath : null,
                'pdf_path' => $pdfPath,
                'is_published' => true,
            ]);

            $books[] = $book;
        }

        // 4. Personal Library Items for Free Reader (Abebe)
        UserBook::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[4]->id, // Meditations
            'status' => 'reading',
            'current_page' => 45,
            'added_at' => now()->subDays(10),
        ]);

        UserBook::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[6]->id, // The Art of War
            'status' => 'reading',
            'current_page' => 35,
            'added_at' => now()->subDays(15),
        ]);

        UserBook::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[8]->id, // As a Man Thinketh
            'status' => 'completed',
            'current_page' => 25,
            'added_at' => now()->subDays(20),
            'completed_at' => now()->subDays(5),
        ]);

        UserBook::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[9]->id, // Richest Man in Babylon
            'status' => 'want_to_read',
            'current_page' => 0,
            'added_at' => now()->subDays(3),
        ]);

        // 5. Personal Library Items for Premium Reader (Sara)
        UserBook::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[1]->id, // The Republic
            'status' => 'reading',
            'current_page' => 120,
            'added_at' => now()->subDays(14),
        ]);

        UserBook::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[2]->id, // Self-Reliance
            'status' => 'completed',
            'current_page' => 246,
            'added_at' => now()->subDays(25),
            'completed_at' => now()->subDays(3),
        ]);

        UserBook::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[0]->id, // The Principles of Psychology
            'status' => 'reading',
            'current_page' => 80,
            'added_at' => now()->subDays(18),
        ]);

        UserBook::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[7]->id, // The Science of Getting Rich
            'status' => 'completed',
            'current_page' => 78,
            'added_at' => now()->subDays(30),
            'completed_at' => now()->subDays(12),
        ]);

        // 6. Reading Logs for Free Reader (Consecutive reading streak: 5 days)
        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            ReadingLog::create([
                'user_id' => $freeReader->id,
                'book_id' => $books[4]->id, // Meditations
                'reading_date' => $date,
                'pages_read' => 20 + ($i * 2),
                'minutes_read' => 30,
            ]);
        }

        // 7. Reading Logs for Premium Reader (Consecutive reading streak: 7 days + historical logs)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            ReadingLog::create([
                'user_id' => $premiumReader->id,
                'book_id' => $books[1]->id, // The Republic
                'reading_date' => $date,
                'pages_read' => 25 + ($i * 2),
                'minutes_read' => 40,
            ]);
        }

        // Additional past logs across previous months for premium velocity
        for ($m = 1; $m <= 4; $m++) {
            ReadingLog::create([
                'user_id' => $premiumReader->id,
                'book_id' => $books[0]->id, // Principles of Psychology
                'reading_date' => Carbon::today()->subMonths($m)->startOfMonth()->addDays(5)->toDateString(),
                'pages_read' => 60,
                'minutes_read' => 80,
            ]);
        }

        // 8. Reviews
        Review::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[4]->id, // Meditations
            'rating' => 5,
            'body' => "A cornerstone of Stoic thought. Reading Aurelius's private journal puts modern stress and distractions into profound perspective.",
        ]);

        Review::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[6]->id, // Art of War
            'rating' => 5,
            'body' => 'Concise, strategic doctrines on patience, preparation, and psychological positioning. Still highly relevant today.',
        ]);

        Review::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[8]->id, // As a Man Thinketh
            'rating' => 5,
            'body' => 'An essential guide on mind management and personal responsibility. One of the greatest essays ever written.',
        ]);

        Review::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[1]->id, // The Republic
            'rating' => 5,
            'body' => 'The foundational dialogue on justice, philosopher kings, and statecraft. Timeless questions that challenge every reader.',
        ]);

        Review::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[0]->id, // Principles of Psychology
            'rating' => 5,
            'body' => 'William James is the father of American psychology. His chapter on habit alone is worth weeks of deep reflection.',
        ]);

        Review::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[2]->id, // Self-Reliance
            'rating' => 4,
            'body' => 'Emersons call to non-conformity and intuition still sounds like an electrifying wake-up call.',
        ]);

        // 9. Quotes
        Quote::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[4]->id,
            'text' => 'You have power over your mind - not outside events. Realize this, and you will find strength.',
            'source' => 'Marcus Aurelius, Meditations',
            'is_favorite' => true,
        ]);

        Quote::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[6]->id,
            'text' => 'In the midst of chaos, there is also opportunity.',
            'source' => 'Sun Tzu, The Art of War',
            'is_favorite' => true,
        ]);

        Quote::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[1]->id,
            'text' => 'The beginning is the most important part of the work.',
            'source' => 'Plato, The Republic',
            'is_favorite' => true,
        ]);

        Quote::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[8]->id,
            'text' => 'A man is literally what he thinks, his character being the complete sum of all his thoughts.',
            'source' => 'James Allen, As a Man Thinketh',
            'is_favorite' => true,
        ]);

        Quote::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[2]->id,
            'text' => 'To be yourself in a world that is constantly trying to make you something else is the greatest accomplishment.',
            'source' => 'Ralph Waldo Emerson, Self-Reliance',
            'is_favorite' => true,
        ]);

        Quote::create([
            'user_id' => $freeReader->id,
            'book_id' => $books[9]->id,
            'text' => 'A part of all you earn is yours to keep.',
            'source' => 'George S. Clason, The Richest Man in Babylon',
            'is_favorite' => false,
        ]);

        // 10. Personalized Reading Plan for Premium Reader
        ReadingPlan::create([
            'user_id' => $premiumReader->id,
            'book_id' => $books[1]->id, // The Republic
            'target_completion_date' => Carbon::today()->addDays(14)->toDateString(),
            'daily_target_pages' => 25,
            'notes' => 'Read morning philosophy chapter before work',
            'status' => 'active',
        ]);

        // 11. Download / Sync Complete Public Domain Editions
        if ($this->command) {
            $this->command->call('books:download-public-domain');
        }
    }
}
