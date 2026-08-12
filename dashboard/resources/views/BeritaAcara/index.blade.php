@extends('layout.app')
@section('content')
    <div class="p-3">
        <h2 class="text-center mb-3">Berita Acara</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $jabatanFields = [
                'kepala_sekolah' => ['label' => 'Kepala Sekolah', 'indukLabel' => 'NIP'],
                'waka_kesiswaan' => ['label' => 'Waka Kesiswaan', 'indukLabel' => 'NIP'],
                'ketua_kpu' => ['label' => 'Ketua KPU', 'indukLabel' => 'NIS'],
                'sekretaris_kpu' => ['label' => 'Sekretaris KPU', 'indukLabel' => 'NIS'],
            ];
        @endphp

        <div class="card mb-4 shadow-sm">
            <div class="card-header fw-bold">
                Penanggung Jawab
                <small class="text-muted fw-normal">— data ini dipakai pada tanda tangan berita acara & tersimpan untuk cetakan berikutnya</small>
            </div>
            <div class="card-body">
                <form action="{{ route('simpan_penanggung_jawab') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        @foreach ($jabatanFields as $jabatan => $field)
                            @php $pj = $penanggungJawab->get($jabatan); @endphp
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <p class="fw-semibold mb-2">{{ $field['label'] }}</p>
                                    <div class="mb-2">
                                        <label class="form-label mb-1">Nama</label>
                                        <input type="text" class="form-control"
                                            name="penanggung_jawab[{{ $jabatan }}][nama]"
                                            value="{{ $pj->nama ?? '' }}" placeholder="Nama {{ $field['label'] }}">
                                    </div>
                                    <div>
                                        <label class="form-label mb-1">{{ $field['indukLabel'] }}</label>
                                        <input type="text" class="form-control"
                                            name="penanggung_jawab[{{ $jabatan }}][nomor_induk]"
                                            value="{{ $pj->nomor_induk ?? '' }}" placeholder="{{ $field['indukLabel'] }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary fw-semibold">Simpan Penanggung Jawab</button>
                    </div>
                </form>
            </div>
        </div>

        <h5 class="fw-bold mb-2">Cetak Berita Acara</h5>
        <div class="table-responsive">
            <table id="myTable" class="table align-middle" style="min-width: 1100px;">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Id Voting</th>
                        <th colspan="2">Paslon 1</th>
                        <th colspan="2">Paslon 2</th>
                        <th colspan="2">Paslon 3</th>
                        <th rowspan="2">Periode</th>
                        <th rowspan="2">Dimulai</th>
                        <th rowspan="2">Berakhir</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Ketua</th>
                        <th>Wakil</th>
                        <th>Ketua</th>
                        <th>Wakil</th>
                        <th>Ketua</th>
                        <th>Wakil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datavote as $votepapper)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $votepapper->vote_id }}</td>
                            <td>{{ $votepapper->paslon_first->ketua->nama ?? '-' }}</td>
                            <td>{{ $votepapper->paslon_first->wakil->nama ?? '-' }}</td>
                            <td>{{ $votepapper->paslon_second->ketua->nama ?? '-' }}</td>
                            <td>{{ $votepapper->paslon_second->wakil->nama ?? '-' }}</td>
                            <td>{{ $votepapper->paslon_third->ketua->nama ?? '-' }}</td>
                            <td>{{ $votepapper->paslon_third->wakil->nama ?? '-' }}</td>
                            <td>{{ $votepapper->periode }}</td>
                            <td>{{ $votepapper->dimulai }}</td>
                            <td>{{ $votepapper->berakhir }}</td>
                            <td>
                                <form action="{{ route('cetak_berita_acara', ['id' => $votepapper->vote_id]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Cetak
                                        Berita Acara</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
