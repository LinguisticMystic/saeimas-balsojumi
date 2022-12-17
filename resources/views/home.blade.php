@extends('layout')

@section('title')
    Saeimas balsojumi
@endsection

@section('content')
    <h1>Welcome!</h1>

    <div>
        @foreach ($resources as $resource)
            <p><a href="{{ $resource }}"><?= $resource ?></a></p>
        @endforeach
    </div>
@endsection
