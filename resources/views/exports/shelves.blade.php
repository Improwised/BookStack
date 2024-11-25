@extends('layouts.export')

@section('title', $bookshelf->name)

@section('content')

    <h1 style="font-size: 4.8em">{{$bookshelf->name}}</h1>
    <div>{!! $bookshelf->descriptionHtml() !!}</div>

    @include('exports.parts.shelves-contents-menu',['bookshelfBooks' => $bookshelfChildrens])

    @foreach($bookshelfChildrens as $bookshelfChildren)
        @if($bookshelfChildren->isA('book'))
            @include('exports.parts.book-item',['bookChildren'=>$bookshelfChildren->bookChildrens,'book'=>$bookshelfChildren])
        @endif
    @endforeach
@endsection