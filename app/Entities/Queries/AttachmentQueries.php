<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Queries\ProvidesEntityQueries;
use BookStack\Uploads\Attachment;
use Illuminate\Database\Eloquent\Builder;

class AttachmentQueries implements ProvidesEntityQueries
{

    protected static array $listAttributes = [
        'id',
        'name',
        'uploaded_to',
    ];

    public function start(): Builder
    {
        return Attachment::query();
    }

    public function findVisibleById(int $id): ?Entity
    {
        return $this->start()->scopes('visible')->find($id);
    }

    public function visibleForList(): Builder
    {
        return $this->start()
            ->select(array_merge(static::$listAttributes, ['page_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('pages')
                    ->whereColumn('pages.id', '=', 'attachments.uploaded_to');
            }]));
    }
}
