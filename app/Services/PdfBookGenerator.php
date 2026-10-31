<?php

namespace App\Services;

class PdfBookGenerator
{
    private string $title;

    private string $author;

    private string $category;

    private int $year;

    private array $pages = [];

    private array $currentPageLines = [];

    private int $currentLineCount = 0;

    private const MAX_LINES_PER_PAGE = 36;

    public function __construct(string $title, string $author, string $category, int $year)
    {
        $this->title = $title;
        $this->author = $author;
        $this->category = $category;
        $this->year = $year;
    }

    /**
     * Sanitize text to pure ASCII for standard Helvetica Type1 PDF rendering.
     */
    public static function sanitize(string $text): string
    {
        $replaces = [
            "\xc2\xa0" => ' ', // non-breaking space
            '“' => '"',
            '”' => '"',
            '‘' => "'",
            '’' => "'",
            '—' => ' - ',
            '–' => ' - ',
            '…' => '...',
            '•' => '*',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ô' => 'o',
            'ó' => 'o',
            'ö' => 'o',
            'ï' => 'i',
            'í' => 'i',
            'ü' => 'u',
            'ú' => 'u',
            'ç' => 'c',
            'ñ' => 'n',
            'Æ' => 'AE',
            'æ' => 'ae',
            'Œ' => 'OE',
            'œ' => 'oe',
        ];
        $text = strtr($text, $replaces);

        // Remove any other non-printable or non-ASCII characters
        $clean = preg_replace('/[^\x20-\x7E\t\r\n]/', '', $text);

        return trim($clean ?? '');
    }

    /**
     * Add a full-page cover / title page.
     */
    public function addTitlePage(string $synopsis): void
    {
        $lines = [
            ['type' => 'spacer', 'height' => 40],
            ['type' => 'category', 'text' => strtoupper(self::sanitize($this->category)).' • '.self::sanitize((string) $this->year)],
            ['type' => 'spacer', 'height' => 20],
            ['type' => 'title', 'text' => strtoupper(self::sanitize($this->title))],
            ['type' => 'spacer', 'height' => 15],
            ['type' => 'author', 'text' => 'By '.self::sanitize($this->author)],
            ['type' => 'spacer', 'height' => 30],
            ['type' => 'rule'],
            ['type' => 'spacer', 'height' => 20],
            ['type' => 'subtitle', 'text' => 'BookPlatform Authorized Digital Edition'],
            ['type' => 'note', 'text' => 'Public Domain & Curated Reader Edition • INSA Project'],
            ['type' => 'spacer', 'height' => 30],
            ['type' => 'subtitle', 'text' => 'About This Work'],
        ];

        $cleanSynopsis = self::sanitize($synopsis);
        $wrappedSynopsis = explode("\n", wordwrap($cleanSynopsis, 75, "\n"));
        foreach ($wrappedSynopsis as $line) {
            $lines[] = ['type' => 'body', 'text' => $line];
        }

        $lines[] = ['type' => 'spacer', 'height' => 40];
        $lines[] = ['type' => 'note', 'text' => '"Read today, lead tomorrow." - BookPlatform'];

        $this->pages[] = $lines;
    }

    /**
     * Add Table of Contents page(s).
     *
     * @param  array<string|array{title: string, page?: int}>  $chapters
     */
    public function addTableOfContents(array $chapters): void
    {
        $currentLines = [
            ['type' => 'title', 'text' => 'Table of Contents'],
            ['type' => 'spacer', 'height' => 20],
            ['type' => 'rule'],
            ['type' => 'spacer', 'height' => 25],
        ];

        $defaultPage = 3;
        $itemsInPage = 0;

        foreach ($chapters as $idx => $chap) {
            if (is_array($chap)) {
                $rawTitle = $chap['title'] ?? ('Chapter '.($idx + 1));
                $pNum = $chap['page'] ?? $defaultPage;
            } else {
                $rawTitle = (string) $chap;
                $pNum = $defaultPage;
            }

            $title = self::sanitize($rawTitle);
            $num = $idx + 1;
            $entry = "Chapter {$num}: {$title}";
            if (strlen($entry) > 48) {
                $entry = substr($entry, 0, 45).'...';
            }

            $dots = str_repeat('.', max(4, 52 - strlen($entry)));
            $currentLines[] = ['type' => 'toc', 'text' => "{$entry} {$dots} Page {$pNum}"];
            $currentLines[] = ['type' => 'spacer', 'height' => 12];
            $defaultPage++;
            $itemsInPage++;

            if ($itemsInPage >= 22) {
                $this->pages[] = $currentLines;
                $currentLines = [
                    ['type' => 'title', 'text' => 'Table of Contents (Cont.)'],
                    ['type' => 'spacer', 'height' => 15],
                    ['type' => 'rule'],
                    ['type' => 'spacer', 'height' => 20],
                ];
                $itemsInPage = 0;
            }
        }

        if (! empty($currentLines)) {
            $this->pages[] = $currentLines;
        }
    }

    /**
     * Start a new chapter on a fresh page.
     */
    public function startChapter(string $chapterTitle, string $chapterSubtitle = ''): void
    {
        $this->flushCurrentPage();

        $cleanTitle = self::sanitize($chapterTitle);
        $this->currentPageLines[] = ['type' => 'title', 'text' => $cleanTitle];
        $this->currentLineCount += 3;

        $cleanSub = self::sanitize($chapterSubtitle);
        if (! empty($cleanSub)) {
            $this->currentPageLines[] = ['type' => 'subtitle', 'text' => $cleanSub];
            $this->currentPageLines[] = ['type' => 'spacer', 'height' => 10];
            $this->currentLineCount += 2;
        }

        $this->currentPageLines[] = ['type' => 'rule'];
        $this->currentPageLines[] = ['type' => 'spacer', 'height' => 15];
        $this->currentLineCount += 2;
    }

    /**
     * Add a paragraph of text to the chapter with auto-wrapping and auto-pagination.
     */
    public function addParagraph(string $text): void
    {
        $clean = self::sanitize($text);
        if (empty($clean)) {
            return;
        }

        $wrapped = explode("\n", wordwrap($clean, 80, "\n"));

        foreach ($wrapped as $lineText) {
            if ($this->currentLineCount >= self::MAX_LINES_PER_PAGE) {
                $this->flushCurrentPage();
            }

            $this->currentPageLines[] = ['type' => 'body', 'text' => $lineText];
            $this->currentLineCount++;
        }

        // Add a paragraph spacing line
        if ($this->currentLineCount < self::MAX_LINES_PER_PAGE) {
            $this->currentPageLines[] = ['type' => 'spacer', 'height' => 10];
            $this->currentLineCount++;
        }
    }

    public function addKeyTakeaway(string $text): void
    {
        if ($this->currentLineCount + 3 >= self::MAX_LINES_PER_PAGE) {
            $this->flushCurrentPage();
        }

        $this->currentPageLines[] = ['type' => 'spacer', 'height' => 10];
        $this->currentPageLines[] = ['type' => 'subtitle', 'text' => 'Core Takeaway:'];
        $wrapped = explode("\n", wordwrap($text, 75, "\n"));
        foreach ($wrapped as $line) {
            $this->currentPageLines[] = ['type' => 'quote', 'text' => '  • '.$line];
            $this->currentLineCount++;
        }
        $this->currentPageLines[] = ['type' => 'spacer', 'height' => 12];
        $this->currentLineCount += 2;
    }

    private function flushCurrentPage(): void
    {
        if (! empty($this->currentPageLines)) {
            $this->pages[] = $this->currentPageLines;
            $this->currentPageLines = [];
            $this->currentLineCount = 0;
        }
    }

    public function getPageCount(): int
    {
        return count($this->pages) + (! empty($this->currentPageLines) ? 1 : 0);
    }

    /**
     * Render the entire document to standard PDF 1.4 binary string.
     */
    public function render(): string
    {
        $this->flushCurrentPage();

        $pageCount = count($this->pages);
        $objs = [];
        $offsets = [];

        // 1. Catalog
        $objs[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        // 2. Pages object
        $kids = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            $pageObjId = 3 + ($i - 1) * 2;
            $kids[] = "{$pageObjId} 0 R";
        }
        $kidsStr = implode(' ', $kids);
        $objs[2] = "<< /Type /Pages /Kids [{$kidsStr}] /Count {$pageCount} >>";

        // Fonts: Helvetica, Helvetica-Bold, Helvetica-Oblique
        $fontRegularId = 3 + ($pageCount * 2);
        $fontBoldId = $fontRegularId + 1;
        $fontItalicId = $fontRegularId + 2;

        $objs[$fontRegularId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objs[$fontBoldId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        $objs[$fontItalicId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique >>';

        // Render each page
        for ($i = 1; $i <= $pageCount; $i++) {
            $pageObjId = 3 + ($i - 1) * 2;
            $contentObjId = $pageObjId + 1;

            $objs[$pageObjId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents {$contentObjId} 0 R /Resources << /Font << /F1 {$fontRegularId} 0 R /F2 {$fontBoldId} 0 R /F3 {$fontItalicId} 0 R >> >> >>";

            $stream = '';

            // Running header (on pages > 1)
            if ($i > 1) {
                $headerText = addcslashes($this->title.' • '.$this->author, '()\\');
                $stream .= "BT /F3 8 Tf 50 805 Td ({$headerText}) Tj ET\n";
                $stream .= "0.5 w 50 798 m 545 798 l S\n";
            }

            // Running footer
            $stream .= "0.5 w 50 45 m 545 45 l S\n";
            $footerLeft = addcslashes('BookPlatform Edition', '()\\');
            $stream .= "BT /F1 8 Tf 50 32 Td ({$footerLeft}) Tj ET\n";
            $stream .= "BT /F2 8 Tf 500 32 Td (Page {$i} of {$pageCount}) Tj ET\n";

            // Render body items
            $y = 760;
            foreach ($this->pages[$i - 1] as $item) {
                $type = $item['type'] ?? 'body';

                switch ($type) {
                    case 'title':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F2 16 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 24;
                        break;
                    case 'subtitle':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F2 11 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 18;
                        break;
                    case 'category':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F2 9 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 14;
                        break;
                    case 'author':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F3 13 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 20;
                        break;
                    case 'toc':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F2 9 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 15;
                        break;
                    case 'quote':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F3 9.5 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 14;
                        break;
                    case 'note':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F3 9 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 14;
                        break;
                    case 'body':
                        $esc = addcslashes($item['text'], '()\\');
                        $stream .= "BT /F1 9.5 Tf 50 {$y} Td ({$esc}) Tj ET\n";
                        $y -= 13.5;
                        break;
                    case 'rule':
                        $stream .= "0.5 w 50 {$y} m 545 {$y} l S\n";
                        $y -= 10;
                        break;
                    case 'spacer':
                        $y -= ($item['height'] ?? 10);
                        break;
                }
            }

            $len = strlen($stream);
            $objs[$contentObjId] = "<< /Length {$len} >>\nstream\n{$stream}\nendstream";
        }

        // Build entire PDF file string
        $out = "%PDF-1.4\n";
        ksort($objs);
        $maxId = max(array_keys($objs));

        foreach ($objs as $id => $body) {
            $offsets[$id] = strlen($out);
            $out .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefOffset = strlen($out);
        $out .= "xref\n0 ".($maxId + 1)."\n";
        $out .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $maxId; $i++) {
            if (isset($offsets[$i])) {
                $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
            } else {
                $out .= "0000000000 65535 f \n";
            }
        }

        $out .= "trailer\n<< /Size ".($maxId + 1)." /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $out;
    }
}
