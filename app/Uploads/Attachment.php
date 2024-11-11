<?php

namespace BookStack\Uploads;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Users\Models\HasCreatorAndUpdater;
use BookStack\Users\Models\HasOwner;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property ?Page  $page
 * @property bool   $external
 * @property int    $uploaded_to
 * @property User   $updatedBy
 * @property User   $createdBy
 *
 * @method static Entity|Builder visible()
 */
class Attachment extends Entity
{
    use HasCreatorAndUpdater;
    use HasFactory;
    use HasOwner;

    public string $textField = 'name';

    public string $htmlField = 'name';

    protected $fillable = ['name', 'order'];
    protected $hidden = ['path', 'page'];
    protected $casts = [
        'external' => 'bool',
    ];

    public static function bootSoftDeletes()
    {
        // No operation: override with an empty method
    }

    /**
     * Get the downloadable file name for this upload.
     */
    public function getFileName(): string
    {
        if (str_contains($this->name, '.')) {
            return $this->name;
        }

        return $this->name . '.' . $this->extension;
    }

    /**
     * Get the page this file was uploaded to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'uploaded_to')->with(['chapter','book']);
    }

    public function attachmentJointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'uploaded_to')
        ->where('joint_permissions.entity_type', '=', 'page');
    }

    /**
     * Get the url of this file.
     */
    public function getUrl($openInline = false): string
    {
        if ($this->external && !str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        return url('/attachments/' . $this->id . ($openInline ? '?open=true' : ''));
    }

    /**
     * Get the representation of this attachment in a format suitable for the page editors.
     * Detects and adapts video content to use an inline video embed.
     */
    public function editorContent(): array
    {
        $videoExtensions = ['mp4', 'webm', 'mkv', 'ogg', 'avi'];
        if (in_array(strtolower($this->extension), $videoExtensions)) {
            $html = '<video src="' . e($this->getUrl(true)) . '" controls width="480" height="270"></video>';
            return ['text/html' => $html, 'text/plain' => $html];
        }

        return ['text/html' => $this->htmlLink(), 'text/plain' => $this->markdownLink()];
    }

    /**
     * Generate the HTML link to this attachment.
     */
    public function htmlLink(): string
    {
        return '<a target="_blank" href="' . e($this->getUrl()) . '">' . e($this->name) . '</a>';
    }

    /**
     * Generate a MarkDown link to this attachment.
     */
    public function markdownLink(): string
    {
        return '[' . $this->name . '](' . $this->getUrl() . ')';
    }

    /**
     * Scope the query to those attachments that are visible based upon related page permissions.
     */
    public function scopeVisible(Builder $query): Builder
    {
        $permissions = app()->make(PermissionApplicator::class);

        return $permissions->restrictPageRelationQuery(
            $query,
            'attachments',
            'uploaded_to'
        );
    }

    protected function performDeleteOnModel()
    {
        // Perform a direct delete without relying on soft deletes
        return $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey())->delete();
    }

    // Prevent soft deletes by setting the deleted column to null
    public function getDeletedAtColumn()
    {
        return null;
    }

    public function scopeWithTrashed($query)
    {
        // No conditions added, so it includes both active and inactive records
        return $query;
    }

    public function scopeOnlyTrashed($query)
    {
        // No conditions added, so it includes both active and inactive records
        return $query;
    }
}
