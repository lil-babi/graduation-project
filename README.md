# 📚 BookPlatform

> *"Read today, lead tomorrow."*

**BookPlatform** is a modern, full-featured web application designed to empower avid readers, lifelong learners, and book communities. Built with Laravel and Tailwind CSS, the platform combines digital reading with habit-tracking tools—helping users cultivate consistent reading habits, manage curated libraries, highlight memorable quotes, and track reading velocity over time.

---

## 🎬 Platform Demo Video

A walkthrough demonstration video showcasing platform onboarding, navigation, and core user workflows is included directly in the repository:

- **File Path:** [`video/Screencast From 2026-10-31 15-14-11.webm`](video/Screencast%20From%202026-10-31%2015-14-11.webm)
- **Format:** WebM (1080p, ~5 minutes)
- **Walkthrough Highlights:**
  - **User Entry Flow:** Account registration, role-based authentication, and personalized dashboard overview.
  - **Book Discovery & Reading:** Searching and filtering by categories, book detail pages, and the in-browser PDF reader.
  - **Personal Library Management:** Shelving books as *Want to Read*, *Currently Reading*, and *Completed*.
  - **Habit Formation & Streaks:** Logging daily read pages, maintaining daily reading streaks, and setting reading goals.
  - **Community & Social Insights:** Submitting book reviews, rating books, and curating favorite quotes.
  - **Monetization & Analytics:** Simulated checkout for Premium subscriptions, unlocking reading velocity charts and customized reading plans.

---

## ✨ Key Features

### 📖 1. Discovery & In-Browser PDF Reader
- Categorized catalog of classic literature and non-fiction works across Self-Development, Philosophy, Psychology, Finance, Business, and History.
- Integrated, distraction-free in-browser PDF reader with automatic progress synchronization.

### 📚 2. Personal Library & Bookshelves
- Organize your reading journey with status categories: *Want to Read*, *Currently Reading*, and *Completed*.
- Direct quick-actions to log pages read or jump straight back into reading where you left off.

### 🔥 3. Reading Habits, Goals & Streaks
- Set daily reading targets (e.g. 20 pages/day).
- Daily reading streaks system that rewards consistent daily engagement and flags broken streaks when a day is skipped.
- Visual reading activity timeline and calendar tracking.

### 💬 4. Community Reviews & Quotes
- Write and publish detailed book reviews with star ratings.
- Collect, favorite, and share inspiring book quotes.
- Strict authorization policies ensuring users can only edit or remove their own content.

### 👑 5. Premium Tier & Advanced Analytics
- **Reading Analytics:** Track reading speed, pages consumed over time, and category breakdowns.
- **Custom Reading Plans:** Create structured multi-day reading roadmaps.
- **Simulated Payment Gateway:** Full checkout flow for subscriptions without requiring real credit cards.

### 🛡️ 6. Administration & Reporting Dashboard
- Dedicated administrative portal (`/admin`) restricted to admin operators.
- Complete CRUD operations for books and genre categories.
- User management and platform revenue reporting with clear free vs. premium subscriber segregation.

---

## 🛠️ Technology Stack

- **Backend:** [Laravel 12](https://laravel.com) (PHP 8.3+)
- **Database:** SQLite (default) / MySQL / PostgreSQL
- **Frontend:** Blade Templates, [Tailwind CSS v4](https://tailwindcss.com), [Vite](https://vite.dev)
- **Testing:** PHPUnit (acceptance test suite covering all core requirements)
- **PDF Generation & Storage:** Local / Public disk storage with dynamic PDF generation services

---

## 🚀 Getting Started

Follow these steps to set up BookPlatform locally on your system:

### Prerequisites
- **PHP** >= 8.3 with SQLite, PDO, and Mbstring extensions
- **Composer** >= 2.0
- **Node.js** >= 18.x & **npm**

### 1. Clone & Navigate to Repository
```bash
git clone https://github.com/lil-babi/graduation-project.git book-platform
cd book-platform
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup & Seeding
Prepare the database and seed initial categories, complete books, and demo accounts:
```bash
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
```

### 5. Build Frontend Assets
For local development:
```bash
npm run dev
```
Or build production assets:
```bash
npm run build
```

### 6. Start the Local Server
```bash
php artisan serve
```
Visit the application at: `http://127.0.0.1:8000`

---

## 👥 Seeded Demo Accounts

The database seeder pre-populates three test users with different roles and tiers (all using the password `password`):

| Role | Name | Email | Password | Access Level |
|---|---|---|---|---|
| **Administrator** | Admin Operator | `admin@bookplatform.et` | `********` | Full Admin Dashboard (`/admin`), Book & Category CRUD, Revenue Reports |


---

## 🧪 Testing

The platform features an automated acceptance test suite verifying authentication, access control boundaries, duplicate prevention, streak calculations, file uploads, and simulated billing:

```bash
php artisan test tests/Feature/BookPlatformTest.php
```

---

## 📄 License

The platform is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
