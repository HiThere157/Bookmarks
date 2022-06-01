@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Overview</h1>
    </div>

    <div class="container-fluid d-flex flex-wrap">
        @foreach($bookmarks as $bookmark)
            <a class="d-flex justify-content-center align-items-center flex-column rounded shadow m-3 text-decoration-none" 
                style="width: 15%; min-width: 15rem; aspect-ratio: 1.7" 
                href="{{ $bookmark->url }}">
                <h1>
                    {{ $bookmark->title }}
                </h1>
                <h5>
                    {{ $bookmark->created_at->format('d.m.Y') }}
                </h5>
            </a>
        @endforeach
    </div>

@stop
