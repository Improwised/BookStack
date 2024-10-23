<?php

namespace BookStack\Entities\Models;

use BookStack\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Chapter.
 *
 * @property Collection<Page> $pages
 * @property ?int             $default_template_id
 * @property ?Page            $defaultTemplate
 * @property Image|null       $cover
 */
class Chapter extends BookChild implements HasCoverImage
{
    use HasFactory;
    use HasHtmlDescription;

    public float $searchFactor = 1.2;

    protected $fillable = ['name', 'description', 'priority'];
    protected $hidden = ['pivot', 'deleted_at', 'description_html'];

    /**
     * Get the pages that this chapter contains.
     *
     * @return HasMany<Page>
     */
    public function pages(string $dir = 'ASC'): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('priority', $dir);
    }

    /**
     * Get the url of this chapter.
     */
    public function getUrl(string $path = ''): string
    {
        $parts = [
            'books',
            urlencode($this->book_slug ?? $this->book->slug),
            'chapter',
            urlencode($this->slug),
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Get the Page that is used as default template for newly created pages within this Chapter.
     */
    public function defaultTemplate(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'default_template_id');
    }

    /**
     * Get the visible pages in this chapter.
     */
    public function getVisiblePages(): Collection
    {
        return $this->pages()
        ->scopes('visible')
        ->orderBy('draft', 'desc')
        ->orderBy('priority', 'asc')
        ->get();
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function coverImageTypeKey(): string
    {
        return 'cover_chapter';
    }

    public function getChapterCover(int $width = 440, int $height = 250): string
    {
        $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        if (!$this->image_id || !$this->cover) {
            return $default;
        }

        try {
            return $this->cover->getThumb($width, $height, false) ?? $default;
        } catch (Exception $err) {
            return $default;
        }
    }
}
