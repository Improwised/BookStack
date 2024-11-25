<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Http\Controller;
use Throwable;

class BookshelfExportController extends Controller
{
    public function __construct(
        protected BookshelfQueries $queries,
        protected ExportFormatter $exportFormatter,
    ) {
        $this->middleware('can:content-export');
    }

    /**
     * Export a book as a PDF file.
     *
     * @throws Throwable
     */
    public function pdf(string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        $pdfContent = $this->exportFormatter->bookshelfToPdf($bookshelf);

        return $this->download()->directly($pdfContent, $bookshelfSlug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     *
     * @throws Throwable
     */
    public function html(string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        $htmlContent = $this->exportFormatter->bookshelfToContainedHtml($bookshelf);

        return $this->download()->directly($htmlContent, $bookshelfSlug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function plainText(string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        $textContent = $this->exportFormatter->bookshelfToPlainText($bookshelf);

        return $this->download()->directly($textContent, $bookshelfSlug . '.txt');
    }

    /**
     * Export a book as a markdown file.
     */
    public function markdown(string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        $textContent = $this->exportFormatter->bookshelfToMarkdown($bookshelf);

        return $this->download()->directly($textContent, $bookshelfSlug . '.md');
    }
}
