<div class="page-break"></div>

@if (isset($chapter))
    <div class="chapter-hint">{{$chapter->name}}</div>
@endif

<h1 id="page-{{$page->id}}">{{ $page->name }}</h1>
{!! $page->html !!}

@if($page->attachments()->where('external',1)->get()->count() > 0)
    <br><hr>
    <h3>Attachment Links</h3>
    @foreach ($page->attachments()->where('external',1)->get() as $attachment)
        <br><a href="{{$attachment->path}}"> {{$attachment->name}} <a/><br>
    @endforeach
    <br><br>
@endif 