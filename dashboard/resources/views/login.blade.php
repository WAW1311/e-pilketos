@extends('layout.layout')
@section('body')
    <div class="d-flex justify-content-center align-items-center border border-dark p-3"
        style="min-height: 100vh; background: linear-gradient(150deg,#00b6ee, #0175b8);">
        <div class="card p-3 w-100" style="max-width: 400px;">
            <form action=" {{ route('login') }} " method="POST">
                @csrf
                <img class="d-flex mb-4 mx-auto" src="{{ asset('storage/SMANJA.png') }}" alt="" width="100"
                    height="100">
                <h1 class="h3 mb-3 text-center fw-bold">Login Panitia</h1>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('username') }}"
                        required autofocus>
                    <label for="email">Email</label>
                </div>
                @error('email')
                    <div class="mt-1 alert alert-danger">{{ $message }}</div>
                @enderror
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" required name="password" id="password"
                        placeholder="password..">
                    <label for="floatingPassword">Password</label>
                </div>
                <center><button class="btn btn-primary w-100 py-2" type="submit">Login</button></center>
            </form>
        </div>
    </div>
@endsection
