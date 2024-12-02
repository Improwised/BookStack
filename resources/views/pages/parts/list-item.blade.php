@component('entities.list-item-basic', ['entity' => $page])
@if($page->is_encrypted)
    <span>@icon('lock')</span>
@else
    <div class="entity-item-snippet">
        <p class="text-muted break-text">{{ $page->getExcerpt() }}</p>
    </div>
@endif
@endcomponent