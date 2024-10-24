<?php

/**
 * Default system settings.
 *
 * Changes to these config files are not supported by BookStack and may break upon updates.
 * Configuration should be altered via the `.env` file or environment variables.
 * Do not edit this file unless you're happy to maintain any changes yourself.
 */

return [

    'app-name'             => 'BookStack',
    'app-logo'             => '',
    'app-name-header'      => true,
    'app-editor'           => 'wysiwyg',
    'app-color'            => '#206ea7',
    'app-color-light'      => 'rgba(32,110,167,0.15)',
    'link-color'           => '#206ea7',
    'bookshelf-color'      => '#a94747',
    'book-color'           => '#077b70',
    'chapter-color'        => '#af4d0d',
    'page-color'           => '#206ea7',
    'page-draft-color'     => '#7e50b1',
    'app-color-dark'       => '#195785',
    'app-color-light-dark' => 'rgba(32,110,167,0.15)',
    'link-color-dark'      => '#429fe3',
    'bookshelf-color-dark' => '#ff5454',
    'book-color-dark'      => '#389f60',
    'chapter-color-dark'   => '#ee7a2d',
    'page-color-dark'      => '#429fe3',
    'page-draft-color-dark' => '#a66ce8',
    'app-custom-head'      => false,
    'registration-enabled' => false,

    // User-level default settings
    'user' => [
        'ui-shortcuts'          => '{}',
        'ui-shortcuts-enabled'  => false,
        'dark-mode-enabled'     => env('APP_DEFAULT_DARK_MODE', false),
        'bookshelves_view_type' => env('APP_VIEWS_BOOKSHELVES', 'grid'),
        'bookshelf_view_type'   => env('APP_VIEWS_BOOKSHELF', 'grid'),
        'books_view_type'       => env('APP_VIEWS_BOOKS', 'grid'),
        'pages_view_type'       => env('APP_VIEWS_BOOKS', 'grid'),
    ],

];
