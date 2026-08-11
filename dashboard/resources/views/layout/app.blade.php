@extends('layout.layout')
@section('body')
    <div class="d-flex min-vh-100">
        @include('components.sidebar')
        <div class="flex-grow-1 min-vw-0">
            @include('components.navbar')
            <div class="border rounded m-2">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
