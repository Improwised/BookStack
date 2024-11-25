@if(count($bookshelfBooks) > 0)
    <ul class="contents">
        @foreach($bookshelfBooks as $bookshelfbook)
            <li><a href="#{{$bookshelfbook->getType()}}-{{$bookshelfbook->id}}">{{ $bookshelfbook->name }}</a></li>
            @include('exports.parts.book-contents-menu', ['children' => $bookshelfbook->bookChildrens])
        @endforeach
    </ul>
@endif