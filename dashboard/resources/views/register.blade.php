@extends('layout.layout')
@section('body')
    <div class="d-flex justify-content-center align-items-center border border-dark"
        style="height: 100vh; background: linear-gradient(150deg,#00b6ee, #0175b8);">
        <div class="card w-25 p-3">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <img class="d-flex mb-4 mx-auto" src="{{ asset('storage/SMANJA.png') }}" alt="" width="100"
                    height="100">
                <h1 class="h3 mb-3 text-center fw-bold">Registrasi</h1>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" id="name">
                    <label for="name">name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="email...">
                    <label for="email">email</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" required name="password" id="password"
                        placeholder="password..">
                    <label for="password">Password</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Register</button>
            </form>
        </div>
    </div>
@endsection
