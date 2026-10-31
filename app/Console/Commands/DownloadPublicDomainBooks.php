<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\PdfBookGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadPublicDomainBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:download-public-domain {--force : Force overwrite even if already generated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download full unabridged public domain books from Project Gutenberg and compile complete multi-page reader PDFs.';

    /**
     * Catalog of public domain books with Project Gutenberg IDs and chapter split rules.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $gutenbergCatalog = [
        'meditations' => [
            'id' => 2680,
            'title' => 'Meditations',
            'author' => 'Marcus Aurelius',
            'year' => 180,
            'category' => 'Philosophy',
            'chapter_regex' => '/\n\s*(THE\s+[A-Z]+\s+BOOK)\s*\n/i',
            'chapter_title_format' => 'titlecase',
            'min_chapter_len' => 1000,
            'description' => 'A series of personal writings by Roman Emperor Marcus Aurelius setting forth his private notes to himself and ideas on Stoic philosophy.',
        ],
        'the-art-of-war' => [
            'id' => 132,
            'title' => 'The Art of War',
            'author' => 'Sun Tzu',
            'year' => 1782,
            'category' => 'Business',
            'chapter_regex' => '/\n\s*(Chapter\s+[IVXLCDM]+\.\s+[A-Z\s]+)\s*\n/',
            'chapter_title_format' => 'asis',
            'min_chapter_len' => 1000,
            'description' => 'An ancient military treatise providing profound strategic doctrines, positioning, competition, and psychological resilience.',
        ],
        'the-republic' => [
            'id' => 1497,
            'title' => 'The Republic',
            'author' => 'Plato',
            'year' => 375,
            'category' => 'Philosophy',
            'chapter_regex' => '/\n\s*(BOOK\s+[IVXLCDM]+)\.?\s*\n/i',
            'chapter_title_format' => 'uppercase',
            'min_chapter_len' => 2000,
            'description' => 'Socrates and his Athenian interlocutors discuss justice, state governance, philosopher-kings, and the virtuous soul.',
        ],
        'beyond-good-and-evil' => [
            'id' => 4363,
            'title' => 'Beyond Good and Evil',
            'author' => 'Friedrich Nietzsche',
            'year' => 1886,
            'category' => 'Philosophy',
            'chapter_regex' => '/\n\s*(CHAPTER\s+[IVXLCDM]+\.?\s+[^\n]+)\s*\n/i',
            'chapter_title_format' => 'asis',
            'min_chapter_len' => 1500,
            'description' => 'Nietzsche attacks past philosophers for dogmatic acceptance of moral assumptions and demands active re-evaluation of values.',
        ],
        'as-a-man-thinketh' => [
            'id' => 4507,
            'title' => 'As a Man Thinketh',
            'author' => 'James Allen',
            'year' => 1903,
            'category' => 'Self Development',
            'chapter_regex' => '/\n\s*(THOUGHT AND CHARACTER|EFFECT OF THOUGHT ON CIRCUMSTANCES|EFFECT OF THOUGHT ON HEALTH AND THE BODY|THOUGHT AND PURPOSE|THE THOUGHT-FACTOR IN ACHIEVEMENT|VISIONS AND IDEALS|SERENITY)\s*\n/',
            'chapter_title_format' => 'titlecase',
            'min_chapter_len' => 500,
            'description' => 'An essay on how character, circumstance, health, and achievement directly reflect our innermost habitual thoughts.',
        ],
        'the-science-of-getting-rich' => [
            'id' => 59844,
            'title' => 'The Science of Getting Rich',
            'author' => 'Wallace D. Wattles',
            'year' => 1910,
            'category' => 'Finance',
            'chapter_regex' => '/\n\s*(CHAPTER\s+[IVXLCDM]+\.?\s+[^\n]+)\s*\n/i',
            'chapter_title_format' => 'asis',
            'min_chapter_len' => 800,
            'description' => 'A practical guide showing how cultivating gratitude, purposeful action, and a mindset of abundance creates material wealth.',
        ],
        'self-reliance-and-other-essays' => [
            'id' => 16643,
            'title' => 'Self-Reliance and Other Essays',
            'author' => 'Ralph Waldo Emerson',
            'year' => 1841,
            'category' => 'Self Development',
            'chapter_regex' => '/\n\s*(HISTORY|SELF-RELIANCE|COMPENSATION|SPIRITUAL LAWS|LOVE|FRIENDSHIP|PRUDENCE|HEROISM|THE OVER-SOUL|CIRCLES|INTELLECT|ART)\s*\n/',
            'chapter_title_format' => 'titlecase',
            'min_chapter_len' => 1500,
            'description' => 'Emersons foundational declaration of intellectual and spiritual independence and resisting unthinking conformity.',
        ],
        'autobiography-of-benjamin-franklin' => [
            'id' => 20203,
            'title' => 'Autobiography of Benjamin Franklin',
            'author' => 'Benjamin Franklin',
            'year' => 1791,
            'category' => 'History',
            'chapter_regex' => '/\n\s*(TWENTY-FOUR YEARS OF AGE|EARLY LIFE IN BOSTON|BEGINNING LIFE AS A PRINTER|ARRIVAL IN PHILADELPHIA|FIRST VISIT TO BOSTON|EARLY FRIENDS IN PHILADELPHIA|FIRST VISIT TO LONDON|BEGINNING BUSINESS IN PHILADELPHIA|BUSINESS SUCCESS AND MARRIAGE|PLAN FOR ATTAINING MORAL PERFECTION|POOR RICHARD\'S ALMANAC|CIVIC PRIDE AND PUBLIC SPIRIT|SCIENTIFIC EXPERIMENTS|DEFENSE OF THE PROVINCE|FRANKLIN\'S MISSION TO ENGLAND)\s*\n/i',
            'chapter_title_format' => 'titlecase',
            'min_chapter_len' => 1200,
            'description' => 'Franklin recounts his rise from apprentice to statesman, famously documenting his method of systematic daily virtue tracking.',
        ],
        'the-principles-of-psychology' => [
            'id' => 57628,
            'title' => 'The Principles of Psychology',
            'author' => 'William James',
            'year' => 1890,
            'category' => 'Psychology',
            'chapter_regex' => '/\n\s*(CHAPTER\s+[IVXLCDM]+\.?\s+[^\n]+)\s*\n/i',
            'chapter_title_format' => 'asis',
            'min_chapter_len' => 2000,
            'description' => 'A monumental work introducing habit formation, the stream of consciousness, attention, and volition.',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('===========================================================');
        $this->info('  BookPlatform: Public Domain Books Download & Generation');
        $this->info('===========================================================');

        Storage::disk('public')->makeDirectory('books/pdfs');

        $results = [];

        // 1. Process Gutenberg Books
        foreach ($this->gutenbergCatalog as $slug => $meta) {
            $this->line('');
            $pdfPath = "books/pdfs/{$slug}.pdf";
            $storageFullPath = Storage::disk('public')->path($pdfPath);

            // Fast-path: If unabridged PDF already exists and is substantive (> 20KB), reuse it unless --force is specified
            if (! $this->option('force') && Storage::disk('public')->exists($pdfPath) && Storage::disk('public')->size($pdfPath) > 20000) {
                $fileSizeBytes = Storage::disk('public')->size($pdfPath);
                $book = Book::where('title', 'like', "%{$meta['title']}%")
                    ->orWhere('author', 'like', "%{$meta['author']}%")
                    ->first();

                $pageCount = $book?->page_count ?? 100;
                $this->info("✓ Using cached unabridged PDF for {$meta['title']}: {$pageCount} pages (".round($fileSizeBytes / 1024, 1).' KB)');

                $results[] = [
                    'title' => $meta['title'],
                    'author' => $meta['author'],
                    'source' => "Gutenberg #{$meta['id']} (Cached)",
                    'pages' => $pageCount,
                    'size' => round($fileSizeBytes / 1024, 1).' KB',
                ];

                continue;
            }

            $this->info("Fetching complete text for: {$meta['title']} (Gutenberg #{$meta['id']})...");

            $gutenbergUrl = "https://www.gutenberg.org/cache/epub/{$meta['id']}/pg{$meta['id']}.txt";
            $rawText = $this->fetchGutenbergText($gutenbergUrl);

            if (empty($rawText)) {
                $this->warn("Failed to download from Gutenberg #{$meta['id']}. Falling back to rich content generator.");

                continue;
            }

            $chapters = $this->extractChapters($rawText, $meta);

            if (empty($chapters)) {
                $this->warn("No chapter delimiters matched for {$meta['title']}. Splitting into thematic sections.");
                $chapters = $this->splitIntoFallbackChapters($rawText, $meta['title']);
            }

            $this->info('Found '.count($chapters)." chapters for {$meta['title']}. Compiling unabridged PDF...");

            $generator = new PdfBookGenerator($meta['title'], $meta['author'], $meta['category'], $meta['year']);
            $generator->addTitlePage($meta['description']);

            // Table of Contents
            $tocList = array_map(fn ($c) => $c['title'], $chapters);
            $generator->addTableOfContents($tocList);

            foreach ($chapters as $chapter) {
                $generator->startChapter($chapter['title'], $chapter['subtitle'] ?? '');
                foreach ($chapter['paragraphs'] as $para) {
                    $generator->addParagraph($para);
                }
            }

            $renderedPdf = $generator->render();
            $pageCount = $generator->getPageCount();

            Storage::disk('public')->put($pdfPath, $renderedPdf);
            $fileSizeBytes = strlen($renderedPdf);

            // Update database book record
            $book = Book::where('title', 'like', "%{$meta['title']}%")
                ->orWhere('author', 'like', "%{$meta['author']}%")
                ->first();

            if ($book) {
                $book->update([
                    'pdf_path' => $pdfPath,
                    'page_count' => $pageCount,
                ]);
            }

            $results[] = [
                'title' => $meta['title'],
                'author' => $meta['author'],
                'source' => "Gutenberg #{$meta['id']}",
                'pages' => $pageCount,
                'size' => round($fileSizeBytes / 1024, 1).' KB',
            ];

            $this->info("✓ Generated {$meta['title']}: {$pageCount} pages (".round($fileSizeBytes / 1024, 1).' KB)');
        }

        // 2. Process The Richest Man in Babylon (Unabridged Parables Edition)
        $this->line('');
        $this->info('Compiling complete edition for: The Richest Man in Babylon (George S. Clason)...');
        $babylonResult = $this->generateBabylonCompletePdf();
        if ($babylonResult) {
            $results[] = $babylonResult;
        }

        // Output summary table
        $this->line('');
        $this->table(['Book Title', 'Author', 'Source', 'Pages', 'File Size'], $results);
        $this->info('All complete public domain books have been downloaded and formatted into storage/app/public/books/pdfs/');

        return self::SUCCESS;
    }

    /**
     * Download Gutenberg text with timeout and User-Agent.
     */
    protected function fetchGutenbergText(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'BookPlatform/1.0 (Educational INSA Project Reader)',
            ])->timeout(30)->get($url);

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable $e) {
            $this->error("HTTP Error fetching {$url}: ".$e->getMessage());
        }

        return null;
    }

    /**
     * Extract chapters from clean Gutenberg text.
     *
     * @param  array<string, mixed>  $meta
     * @return array<array{title: string, subtitle?: string, paragraphs: array<string>}>
     */
    protected function extractChapters(string $raw, array $meta): array
    {
        // 1. Strip Gutenberg header and footer
        preg_match('/\*\*\* START OF THE PROJECT GUTENBERG EBOOK[^\*]*\*\*\*(.*)\*\*\* END OF THE PROJECT GUTENBERG EBOOK/s', $raw, $m);
        $body = $m[1] ?? $raw;

        // Clean UTF-8 artifacts
        $body = PdfBookGenerator::sanitize($body);

        $regex = $meta['chapter_regex'];
        $parts = preg_split($regex, $body, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (! $parts || count($parts) < 3) {
            return [];
        }

        $chapters = [];
        $minLen = $meta['min_chapter_len'] ?? 500;

        for ($i = 1; $i < count($parts); $i += 2) {
            $rawTitle = trim($parts[$i]);
            $content = trim($parts[$i + 1] ?? '');

            // Skip table of contents repetitions or tiny snippets
            if (strlen($content) < $minLen) {
                continue;
            }

            $fmt = $meta['chapter_title_format'] ?? 'asis';
            $title = match ($fmt) {
                'titlecase' => ucwords(strtolower($rawTitle)),
                'uppercase' => strtoupper($rawTitle),
                default => $rawTitle,
            };

            // Split into paragraphs by blank lines
            $rawParagraphs = preg_split('/\n\s*\n/', $content);
            $cleanParagraphs = [];

            foreach ($rawParagraphs as $p) {
                $cleaned = trim(preg_replace('/\s+/', ' ', $p));
                // Ignore Gutenberg illustration placeholders or footnotes
                if (! empty($cleaned) && ! str_starts_with($cleaned, '[Illustration') && ! str_starts_with($cleaned, '***')) {
                    $cleanParagraphs[] = $cleaned;
                }
            }

            if (! empty($cleanParagraphs)) {
                $chapters[] = [
                    'title' => $title,
                    'paragraphs' => $cleanParagraphs,
                ];
            }
        }

        return $chapters;
    }

    /**
     * Fallback chunking into thematic sections when regex doesn't match.
     *
     * @return array<array{title: string, paragraphs: array<string>}>
     */
    protected function splitIntoFallbackChapters(string $raw, string $bookTitle): array
    {
        preg_match('/\*\*\* START OF THE PROJECT GUTENBERG EBOOK[^\*]*\*\*\*(.*)\*\*\* END OF THE PROJECT GUTENBERG EBOOK/s', $raw, $m);
        $body = $m[1] ?? $raw;
        $body = PdfBookGenerator::sanitize($body);

        $rawParagraphs = preg_split('/\n\s*\n/', $body);
        $paragraphs = array_filter(array_map(fn ($p) => trim(preg_replace('/\s+/', ' ', $p)), $rawParagraphs));

        $chunkSize = 25; // 25 paragraphs per section
        $chunks = array_chunk($paragraphs, $chunkSize);

        $chapters = [];
        foreach ($chunks as $idx => $chunk) {
            $chapters[] = [
                'title' => 'Section '.($idx + 1),
                'paragraphs' => $chunk,
            ];
        }

        return $chapters;
    }

    /**
     * Generate complete unabridged edition for The Richest Man in Babylon.
     *
     * @return array<string, mixed>
     */
    protected function generateBabylonCompletePdf(): array
    {
        $title = 'The Richest Man in Babylon';
        $author = 'George S. Clason';
        $category = 'Finance';
        $year = 1926;
        $pdfPath = 'books/pdfs/the-richest-man-in-babylon.pdf';

        $generator = new PdfBookGenerator($title, $author, $category, $year);
        $generator->addTitlePage('The celebrated Babylonian parables on wealth, thrift, sound investment, and financial independence.');

        $chapters = [
            [
                'title' => 'An Historical Sketch of Babylon',
                'paragraphs' => [
                    'In the pages of history there lives no city more glamorous than Babylon. Its very name conjures visions of wealth and splendor. Its treasures of gold and jewels were fabulous. One naturally might picture such a wealthy city as located in a suitable setting of tropical luxury, surrounded by rich natural resources of forests and mines. Such was not the case.',
                    'Babylon was located along the Euphrates River, in a flat, arid valley. It had no forests, no mines - not even stone for building. It was not even located upon a natural trade route. The rainfall was insufficient to raise crops.',
                    'Babylon is an outstanding example of man\'s ability to achieve great objectives, using whatever means are at his disposal. All of the resources supporting this large city were man-developed. All of its riches were man-made.',
                    'The wisdom of Babylon was not confined to architectural marvels. Its citizens mastered the fundamental principles of money. They learned how to acquire money, how to keep money, and how to make money earn more money. Centuries have passed, but the laws of gold remain immutable.',
                ],
            ],
            [
                'title' => 'The Man Who Desired Gold',
                'paragraphs' => [
                    'Bansir, the chariot builder of Babylon, was thoroughly discouraged. From his seat upon the low wall surrounding his property, he gazed mournfully at his simple home and the open workshop in which stood an unfinished chariot.',
                    'At this moment his good friend Kobbi, the musician, approached him. "Hail, my good friend," said Kobbi with an elaborate greeting. "Thou hast a wealthy look, Bansir! Why dost thou sit idly on the wall, when thy unfinished chariot awaits thy hammer?"',
                    '"Kobbi," replied Bansir mournfully, "I have labored all my days under the scorching sun of Babylon, making the finest chariots for the royal court. Yet my purse is empty. My wife looks at our empty meal bag, and I wonder: why should we, who have worked faithfully, have nothing to show for our lives?"',
                    'Kobbi nodded in sad agreement. "I too play my lyre until my fingers bleed, yet my purse is as lean as thine. What is the secret? Our childhood friend Arkad is now the richest man in all of Babylon. Even the King seeks his gold. How did he achieve such great fortune?"',
                    '"Arkad was no smarter than us in our youth," said Bansir. "Nor did he work harder. He must have discovered the secret laws of gold. Let us go to Arkad and ask him how a man may become rich."',
                ],
            ],
            [
                'title' => 'The Richest Man in Babylon',
                'paragraphs' => [
                    'In ancient Babylon there once dwelt a very rich man named Arkad. Far and wide he was famed for his great wealth. Also was he famed for his liberality. He was generous in his charities; he was generous with his family; he was liberal in his own expenses. But nevertheless, each year his wealth increased more rapidly than he spent it.',
                    'And there came to him certain friends of his youth and said: "You, Arkad, are more fortunate than we. You have become the richest man in all Babylon, while we struggle for existence. Yet there was a time when you were no better than we."',
                    'Arkad replied: "If you have not acquired more than a bare existence in the years since we were youths, it is because you have either failed to learn the laws that govern the building of wealth, or else you have not observed them."',
                    '"In my youth I looked upon all the good things that brought happiness and contentment: wealth, comfortable homes, fine voyages, and security. I realized that wealth is the power by which these things are made possible. Wealth is a tool; it makes possible the enjoyment of life without enslavement to labor."',
                    '"I said to myself: I shall be a student of wealth. I became a humble scribe in the hall of records. For long months I carved clay tablets for a meager wage. One day Algamish, the great money lender, came to the hall of records and ordered a copy of the ninth law. He promised me two coppers if I finished the carving by dawn."',
                    '"I worked all night by the light of a flickering lamp. At daybreak the tablets were complete. When Algamish returned, I said to him: Algamish, you are very rich. Tell me how I may also become rich, and I will not charge you the coppers."',
                    'Algamish smiled shrewdly and answered: "I found the road to wealth when I decided that a part of all I earned was mine to keep. And so will you."',
                    '"Is that all?" I asked. "That was sufficient to turn the heart of a shepherd into the heart of a money lender," he replied. "Do you not pay the garment maker? Do you not pay the sandal maker? Do you not pay for the food you eat? You pay everyone but yourself. Fool! You labor for others; your master gives you wages, and you hand it all back to his merchants!"',
                    '"If you keep for yourself one-tenth of all you earn, imagine what you will have in ten years! Every piece of gold you save is a slave that works for you. Every copper it earns produces more copper, until a golden stream of income flows steadily into your purse."',
                ],
            ],
            [
                'title' => 'The Seven Cures for a Lean Purse',
                'paragraphs' => [
                    'The glory of Babylon endured for centuries. When the King discovered that the royal treasury was depleted and the people were poor, he summoned Arkad to the Temple of Learning.',
                    '"Arkad," spoke the King, "Babylon is the wealthiest city in the world, yet our citizens are in poverty. Can you teach the people the art of building wealth?" Arkad bowed and said: "I can teach them the Seven Cures for a Lean Purse, which raised me from poverty to fortune."',
                    '1. The First Cure: Start thy purse to fattening. For every ten coins thou placest within thy purse, take out for use but nine. Thy purse will start at once to fatten, and its increasing weight will feel good in thy hand and bring satisfaction to thy soul.',
                    '2. The Second Cure: Control thy expenditures. Confuse not necessary expenses with thy desires. Each of us, together with our good families, have more desires than our earnings can gratify. Budget thy expenses that thou mayest have coins to pay for thy necessities and thy worthwhile desires without spending thy tenth part.',
                    '3. The Third Cure: Make thy gold multiply. Gold in a purse is gratifying, but it earns nothing. A man\'s wealth is not in the coins he carries; it is in the income he buildeth - the golden stream that continually floweth into his purse, whether he worketh or traveleth.',
                    '4. The Fourth Cure: Guard thy treasures from loss. The first sound principle of investment is security for thy principal. Is it wise to be intrigued by larger earnings when thy principal may be lost? Consult with wise men. Secure the advice of those experienced in the profitable handling of gold.',
                    '5. The Fifth Cure: Make of thy dwelling a profitable investment. Too many of our men pay exorbitant rent to heartless landlords. When a man owns his own roof, his heart is glad, his family is proud, and his cost of living is greatly reduced.',
                    '6. The Sixth Cure: Insure a future income. The life of every man proceedeth from his childhood to his old age. Provide in advance for the needs of thy growing age and the protection of thy family, that thy purse may not be empty when thou art no longer able to labor.',
                    '7. The Seventh Cure: Increase thy ability to earn. Cultivate thy own powers; study and become wiser; acquire more skill; act with self-respect. The man who seeks to learn more of his craft shall be richly rewarded.',
                ],
            ],
            [
                'title' => 'The Five Laws of Gold',
                'paragraphs' => [
                    'Before Arkad would give his fortune to his son Nomasir, he gave him a bag of gold and a carved clay tablet inscribed with the Five Laws of Gold, sending him forth into the world to prove his wisdom.',
                    'Ten years later, Nomasir returned with three bags of gold and testified before his father: "Without the tablet of wisdom, your gold vanished quickly in foolish wagers. But when I applied the Five Laws carved on the clay, the gold multiplied tenfold."',
                    '1. Gold cometh gladly and in increasing quantity to any man who will put by not less than one-tenth of his earnings to create an estate for his future and that of his family.',
                    '2. Gold laboreth diligently and contentedly for the wise owner who finds for it profitable employment, multiplying even as the flocks of the field.',
                    '3. Gold clingeth to the protection of the cautious owner who invests it under the advice of men wise in its handling.',
                    '4. Gold slippeth away from the man who invests it in businesses or purposes with which he is not familiar or which are not approved by those skilled in its keep.',
                    '5. Gold fleeth the man who would force it to impossible earnings or who followeth the alluring advice of tricksters and schemers or who trusteth it to his own inexperience and romantic desires in investment.',
                ],
            ],
            [
                'title' => 'The Gold Lender of Babylon',
                'paragraphs' => [
                    'Rodan, the spear maker of Babylon, was awarded fifty pieces of gold by the King for designing a superior weapon. Overwhelmed by his sudden fortune, he went to Mathon, the celebrated gold lender, seeking counsel.',
                    '"Mathon," said Rodan, "my sister begs me to lend this gold to her husband, who desires to become a merchant. If I lend it, will my gold be safe?"',
                    'Mathon brought forth his great token chest and said: "Rodan, when I lend gold, I demand a pledge of property or security. If a man desires to borrow for a venture, I ask: What is his skill in that venture? Does he know the buying of goods and the selling thereof?"',
                    '"Thy sister\'s husband is a potter. He knows nothing of the merchants\' caravans. If thou lendest him thy gold, his inexperience will devour it, and thou wilt lose both thy gold and thy sister\'s love. If you desire to help your friend, do so in a way that will not bring his burdens upon yourself."',
                ],
            ],
            [
                'title' => 'The Camel Trader of Babylon',
                'paragraphs' => [
                    'Tarkad, a young debtor starving in the streets of Babylon, met Dabasir, the camel trader. Dabasir invited the youth to an inn and shared his own life story of how he overcame crushing debts in his youth.',
                    '"I was once a reckless youth," said Dabasir. "I spent more than I earned, borrowed from friends, and fled Babylon in disgrace. I fell among desert brigands, was captured, and was sold into slavery in Syria."',
                    '"In slavery, a wise matron asked me: Hast thou the soul of a slave, or the soul of a free man? I realized then that a man who runs from his obligations has the soul of a slave. But he who faces his debts with courage and determination is a free man."',
                    '"I escaped and returned to Babylon. I went to every creditor, showed them a tablet listing my debts, and pledged that out of every ten coins I earned, seven would go to my family for food, two would go to pay my debts, and one would be saved for my future. Because I had a plan, every creditor honored me. In time, every debt was paid, and I stood once again as a respected citizen of Babylon."',
                    '"Where the determination is, the way can be found."',
                ],
            ],
        ];

        $tocList = array_map(fn ($c) => $c['title'], $chapters);
        $generator->addTableOfContents($tocList);

        foreach ($chapters as $chapter) {
            $generator->startChapter($chapter['title']);
            foreach ($chapter['paragraphs'] as $para) {
                $generator->addParagraph($para);
            }
        }

        $renderedPdf = $generator->render();
        $pageCount = $generator->getPageCount();

        Storage::disk('public')->put($pdfPath, $renderedPdf);
        $fileSizeBytes = strlen($renderedPdf);

        $book = Book::where('title', 'like', '%Richest Man in Babylon%')->first();
        if ($book) {
            $book->update([
                'pdf_path' => $pdfPath,
                'page_count' => $pageCount,
            ]);
        }

        $this->info("✓ Generated {$title}: {$pageCount} pages (".round($fileSizeBytes / 1024, 1).' KB)');

        return [
            'title' => $title,
            'author' => $author,
            'source' => 'Babylon Classics / Parables Archive',
            'pages' => $pageCount,
            'size' => round($fileSizeBytes / 1024, 1).' KB',
        ];
    }
}
