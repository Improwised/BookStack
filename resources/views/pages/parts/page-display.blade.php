<div dir="auto">

    <h1 class="break-text" id="bkmrk-page-title">{{$page->name}}</h1>

    <div style="clear:left;"></div>
    @if($page->is_encrypted)
        <h1 class="text-center encrypt-icon"><span>@icon('lock')</span></h1>
    @endif
    <div class="page-detail-content hidden">
        @if (isset($diff) && $diff)
            {!! $diff !!}
        @else
            {!! isset($page->renderedHTML) ? $page->renderedHTML : $page->html !!}
        @endif
    </div>
</div>