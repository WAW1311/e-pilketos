@extends('layout.layout')
@section('body')
    <div class="container py-4 py-md-5 min-vh-100 text-center">
        <h1 class="text-uppercase fw-bold fs-3 fs-md-1">Hasil Suara Pilketos Sma Negeri 1 Ulujami</h1>
        <h2 class="mb-4 mb-md-5 text-uppercase fw-bold fs-4"> Periode {{ $periode }}</h2>

        {{-- Statistik --}}
        <div class="row g-3 mb-4 mb-md-5 justify-content-center">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card text-white bg-primary">
                    <div class="card-header">Total Siswa</div>
                    <div class="card-body">
                        <h3 class="card-title mb-0">{{ $totalSiswa }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card text-white bg-success">
                    <div class="card-header">Total Suara Masuk</div>
                    <div class="card-body">
                        <h3 class="card-title mb-0" id="total-suara">{{ $totalSuara }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4 justify-content-center">
            @foreach ($paslonList as $paslon)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-header">
                            <h1 class="mb-0">{{ $paslon['nomor'] }}</h1>
                        </div>
                        <img src="{{ asset('storage/' . $paslon['asset']) }}" class="card-img-top img-fluid"
                            alt="Foto Paslon {{ $paslon['nomor'] }}" style="max-height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h3 class="mb-1 fs-5"><strong>KETUA : </strong> {{ $paslon['ketua'] }}</h3>
                            <h3 class="mb-1 fs-5"><strong>WAKIL : </strong> {{ $paslon['wakil'] }}</h3>
                        </div>
                        <div class="card-footer">
                            <h3 class="fw-bold mb-0" id="{{ $paslon['paslon_id'] }}"
                                data-count="{{ $paslon['vote'] }}">{{ $paslon['vote'] }} Suara</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-muted mt-4">
            <small>Voting ditutup pada: {{ $tanggalTutup }}</small>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            const totalEl = document.getElementById('total-suara');

            function refreshTotal() {
                let total = 0;
                document.querySelectorAll('[data-count]').forEach((el) => {
                    total += parseInt(el.dataset.count, 10) || 0;
                });
                if (totalEl) totalEl.innerHTML = total;
            }

            window.Echo.channel('vc.{{ $vote_id }}')
                .listen('.votecount', (e) => {
                    console.log('Votecount received:', e);
                    const el = document.getElementById(e.paslon_id);
                    if (el) {
                        el.dataset.count = e.count;
                        el.innerHTML = `${e.count} Suara`;
                        refreshTotal();
                    }
                });
        });
    </script>
@endsection
