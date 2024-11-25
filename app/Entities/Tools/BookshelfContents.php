<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Bookshelf;

class BookshelfContents
{
    public function __construct(protected Bookshelf $bookshelf)
    {
    }

    public function getTree(bool $renderPages = false)
    {
        $books = $this->bookshelf->books()->scopes('visible')->get();

        $books->each(function ($book) use ($renderPages) {
            $book->setAttribute('bookChildrens', (new BookContents($book))->getTree(false, $renderPages));
        });

        return collect($books);
    }
}
