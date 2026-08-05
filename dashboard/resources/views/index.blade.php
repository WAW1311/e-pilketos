@extends('layout.app')
@section('content')
    <div class="p-3">
        <div class="d-flex justify-content-evenly mb-5">
            <div class="card bg-primary">
                <div class="card-body text-light fw-bold">
                    <h5 class="card-title">Jumlah Siswa : <span>{{ $siswa ?: '0' }}</span></h5>
                </div>
            </div>
            <div class="card bg-info">
                <div class="card-body text-light-emphasis fw-bold">
                    <h5 class="card-title">Jumlah Surat Suara : <span>{{ $suratSuara ?: '0' }}</span></h5>
                </div>
            </div>
            <div class="card bg-warning">
                <div class="card-body text-light-emphasis fw-bold">
                    <h5 class="card-title">Jumlah Paslon : <span>{{ $paslon ?: '0' }}</span></h5>
                </div>
            </div>
        </div>
        <div class="container-fluid w-75">
            <div>
                <canvas id="myChart"></canvas>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'total suara',
                    data: @json($data),
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
