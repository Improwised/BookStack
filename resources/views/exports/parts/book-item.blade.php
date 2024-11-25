<div class="page-break"></div>
<h1 id="book-{{$book->id}}">{{ $book->name }}</h1>

<div>{!! $book->descriptionHtml() !!}</div>

@foreach($bookChildren as $bookChild)
    @if($bookChild->isA('chapter'))
        @include('exports.parts.chapter-item', ['chapter' => $bookChild])
    @else
        @include('exports.parts.page-item', ['page' => $bookChild, 'chapter' => null])
    @endif
@endforeach