<div dir="auto">

    <h1 class="break-text" id="bkmrk-page-title">{{$page->name}}</h1>

    <div style="clear:left;"></div>

    @if($page->is_encrypted && !isset($export))
        <h5 class="text-neg encrypt-message">This File is Encrypted To Decrypt Enter Password By Click on <strong>View</strong> at Right Side Menu</h5>
    @endif
    <div class="page-detail-content">
        @if (isset($diff) && $diff)
            {!! $diff !!}
        @else
            {!! isset($page->renderedHTML) ? $page->renderedHTML : $page->html !!}
        @endif
    </div>
</div>