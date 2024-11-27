<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Http\Controller;
use BookStack\Http\Request;
use Illuminate\Support\Facades\Response;
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
    public function pdf(Request $request, string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        if ($request['split'] === true) {
            return $this->downloadAllInZip($bookshelf, 'pdf');
        } else {
            $htmlContent = $this->exportFormatter->bookshelfToPdf($bookshelf);

            return $this->download()->directly($htmlContent, $bookshelfSlug . '.pdf');
        }
    }

    /**
     * Export a book as a contained HTML file.
     *
     * @throws Throwable
     */
    public function html(Request $request, string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        if ($request['split'] === true) {
            return $this->downloadAllInZip($bookshelf, 'html');
        } else {
            $htmlContent = $this->exportFormatter->bookshelfToContainedHtml($bookshelf);

            return $this->download()->directly($htmlContent, $bookshelfSlug . '.html');
        }
    }

    /**
     * Export a book as a plain text file.
     */
    public function plainText(Request $request, string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        if ($request['split'] === true) {
            return $this->downloadAllInZip($bookshelf, 'txt');
        } else {
            $htmlContent = $this->exportFormatter->bookshelfToPlainText($bookshelf);

            return $this->download()->directly($htmlContent, $bookshelfSlug . '.txt');
        }
    }

    /**
     * Export a book as a markdown file.
     */
    public function markdown(Request $request, string $bookshelfSlug)
    {
        $bookshelf = $this->queries->findVisibleBySlugOrFail($bookshelfSlug);
        if ($request['split'] === true) {
            return $this->downloadAllInZip($bookshelf, 'md');
        } else {
            $htmlContent = $this->exportFormatter->bookshelfToMarkdown($bookshelf);

            return $this->download()->directly($htmlContent, $bookshelfSlug . '.md');
        }
    }

    public function downloadAllInZip(Bookshelf $bookshelf, string $type)
    {
        $bookshelf->load('books');

        $zip = new \ZipArchive();

        $tempFilePath = storage_path('app/public/' . $bookshelf->slug . '.zip');
        if ($zip->open($tempFilePath, \ZipArchive::CREATE) === true) {
            foreach ($bookshelf->books as $book) {
                $pdfContent = $this->getContentBasedOntype($book, $type);
                $zip->addFromString($book->slug, $pdfContent);
            }
            $zip->close();

            return Response::download($tempFilePath)->deleteFileAfterSend(true);
        }
    }

    public function getContentBasedOntype(Book $book, string $type)
    {
        switch ($type) {
            case 'pdf':
                return $this->exportFormatter->bookToPdf($book);
                break;


            case 'html':
                return $this->exportFormatter->bookToContainedHtml($book);
                break;

            case 'txt':
                return $this->exportFormatter->bookToPlainText($book);
                break;

            case 'md':
                return $this->exportFormatter->bookToMarkdown($book);
                break;
            default:
                return "";
                break;
        }
    }
}
