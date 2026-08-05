@extends('layout.layout')
@section('body')
    <center>
        <div class="container py-5 min-vh-100">
            <h1 class="text-uppercase fw-bold">Hasil Suara Pilketos Sma Negeri 1 Ulujami</h1>
            <h2 class="mb-5 text-uppercase fw-bold"> Periode {{ $periode }}</h2>

            {{-- Statistik --}}
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card text-white bg-primary mb-3 w-50">
                        <div class="card-header">Total Siswa</div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $totalSiswa }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-success mb-3 w-50">
                        <div class="card-header">Total Suara Masuk</div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $totalSuara }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($paslonList as $paslon)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 text-center shadow-sm">
                            <div class="card-header">
                                <h1 class="mb-0">{{ $paslon['nomor'] }}</h1>
                            </div>
                            <img src="{{ asset('storage/' . $paslon['asset']) }}" class="card-img-top img-fluid"
                                alt="Foto Paslon {{ $paslon['nomor'] }}" style="max-height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h3 class="mb-1"><strong>KETUA : </strong> {{ $paslon['ketua'] }}</h3>
                                <h3 class="mb-1"><strong>WAKIL : </strong> {{ $paslon['wakil'] }}</h3>
                            </div>
                            <div class="card-footer">
                                <h3 class="fw-bold" id="{{ $paslon['paslon_id'] }}">{{ $paslon['vote'] }} Suara</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-muted">
                <small>Voting ditutup pada: {{ $tanggalTutup }}</small><br>
            </div>
        </div>
    </center>
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            window.Echo.channel('vc.{{ $vote_id }}')
                .listen('.votecount', (e) => {
                    console.log('Votecount received:', e);
                    document.getElementById(e.paslon_id).innerHTML = `${e.vote} Suara`;
                });
        });
    </script>
@endsection
