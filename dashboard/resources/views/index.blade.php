@extends('layout.app')
@section('content')
    <div class="p-3">
        <div class="row g-3 mb-5 justify-content-center">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card bg-primary h-100">
                    <div class="card-body text-light fw-bold">
                        <h5 class="card-title mb-0">Jumlah Siswa : <span>{{ $siswa ?: '0' }}</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card bg-info h-100">
                    <div class="card-body text-light-emphasis fw-bold">
                        <h5 class="card-title mb-0">Jumlah Surat Suara : <span>{{ $suratSuara ?: '0' }}</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card bg-warning h-100">
                    <div class="card-body text-light-emphasis fw-bold">
                        <h5 class="card-title mb-0">Jumlah Paslon : <span>{{ $paslon ?: '0' }}</span></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid px-0" style="max-width: 900px;">
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
