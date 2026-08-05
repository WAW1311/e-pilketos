@extends('layout.layout')
@section('body')
    <div class="d-flex min-vh-100">
        @include('components.sidebar')
        <div class="container-fluid" style="width: 85vw;">
            @include('components.navbar')
            <div class="border rounded">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
