@extends('layout.app')
@section('content')
    <div class="card p-3">
        <h3 class="text-center mb-5 fw-bold">Edit Surat Suara</h3>
        <form action="{{ route('update_surat_POST', ['id' => $datavote->vote_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <center>
                <div class="d-flex align-items-center mb-3 w-100" style="max-width: 360px;">
                    <label for="periode" class="form-label fw-bold me-2" style="width: 100px;">Periode :</label>
                    <input type="text" class="form-control" id="periode" name="periode" value="{{ $datavote->periode }}" required>
                </div>
            </center>
            <div class="row g-3 mb-5">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card p-3 h-100">
                        <h4 class="text-center">Paslon 1</h4>
                        <label for="name" class="form-label">Ketua</label>
                        <select class="form-control" id="ketua1" name="ketua1" required>
                            <option disabled>Pilih Ketua</option>
                            @foreach ($datasiswa as $siswa)
                                <option {{ $datavote->paslon_first->ketua->nis == $siswa->nis ? 'selected' : '' }} value="{{ $siswa->nis }}">{{ $siswa->nis }} -- {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="name" class="form-label">Wakil</label>
                        <select class="form-control" id="wakil1" name="wakil1" required>
                            <option disabled>Pilih Wakil</option>
                            @foreach ($datasiswa as $siswa)
                                <option {{ $datavote->paslon_first->wakil->nis == $siswa->nis ? 'selected' : '' }} value="{{ $siswa->nis }}">{{ $siswa->nis }} -- {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="foto1" class="form-label">Foto</label>
                        <input class="form-control" type="file" name="foto1" id="foto1">
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card p-3 h-100">
                        <h4 class="text-center">Paslon 2</h4>
                        <label for="name" class="form-label">Ketua</label>
                        <select class="form-control" id="ketua2" name="ketua2" required>
                            <option disabled>Pilih Ketua</option>
                            @foreach ($datasiswa as $siswa)
                                <option {{ $datavote->paslon_second->ketua->nis == $siswa->nis ? 'selected' : '' }} value="{{ $siswa->nis }}">{{ $siswa->nis }} -- {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="name" class="form-label">Wakil</label>
                        <select class="form-control" id="wakil2" name="wakil2" required>
                            <option disabled>Pilih Wakil</option>
                            @foreach ($datasiswa as $siswa)
                                <option {{ $datavote->paslon_second->wakil->nis == $siswa->nis ? 'selected' : '' }} value="{{ $siswa->nis }}">{{ $siswa->nis }} -- {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="foto2" class="form-label">Foto</label>
                        <input class="form-control" type="file" name="foto2" id="foto2">
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card p-3 h-100">
                        <h4 class="text-center">Paslon 3</h4>
                        <label for="name" class="form-label">Ketua</label>
                        <select class="form-control" id="ketua3" name="ketua3" required>
                            <option disabled>Pilih Ketua</option>
                            @foreach ($datasiswa as $siswa)
                                <option {{ $datavote->paslon_third->ketua->nis == $siswa->nis ? 'selected' : '' }} value="{{ $siswa->nis }}">{{ $siswa->nis }} -- {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="name" class="form-label">Wakil</label>
                        <select class="form-control" id="wakil3" name="wakil3" required>
                            <option disabled>Pilih Wakil</option>
                            @foreach ($datasiswa as $siswa)
                                <option {{ $datavote->paslon_third->wakil->nis == $siswa->nis ? 'selected' : '' }} value="{{ $siswa->nis }}">{{ $siswa->nis }} -- {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <br>
                        <label for="foto3" class="form-label">Foto</label>
                        <input class="form-control" type="file" name="foto3" id="foto3">
                    </div>
                </div>
            </div>
            <center>
                <div class="row g-3 justify-content-center mb-4 w-100" style="max-width: 640px;">
                    <div class="col-12 col-sm-6 d-flex align-items-center">
                        <label for="dimulai" class="form-label fw-bold me-2 mb-0" style="width: 100px;">Dimulai :</label>
                        <input class="form-control" type="datetime-local" name="dimulai" id="dimulai" value="{{ $datavote->dimulai }}" required>
                    </div>
                    <div class="col-12 col-sm-6 d-flex align-items-center">
                        <label for="berakhir" class="form-label fw-bold me-2 mb-0" style="width: 100px;">Berakhir :</label>
                        <input class="form-control" type="datetime-local" name="berakhir" id="berakhir" value="{{ $datavote->berakhir }}" required>
                    </div>
                </div>
            </center>
            <center><button type="submit" class="btn btn-primary">Simpan</button></center>
        </form>
    </div>
@endsection
