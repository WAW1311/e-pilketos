@extends('layout.app')
@section('content')
    <div class="card p-3">
        <h3 class="text-center mb-5 fw-bold">Tambah Surat Suara</h3>
        <form action="{{ route('tambah_surat_POST') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <center>
                <div class="d-flex align-items-center mb-3 w-25">
                    <label for="periode" class="form-label fw-bold me-2" style="width: 100px;">Periode :</label>
                    <input type="text" class="form-control" id="periode" name="periode" required>
                </div>
            </center>
            <div class="d-flex flex-row justify-content-around mb-5">
                <div class="card p-3">
                    <h4 class="text-center">Paslon 1</h4>
                    <label for="name" class="form-label">Ketua</label>
                    <select class="form-control" id="ketua1" name="ketua1" required>
                        <option selected disabled>Pilih Ketua</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <br>
                    <label for="name" class="form-label">Wakil</label>
                    <select class="form-control" id="wakil1" name="wakil1" required>
                        <option selected disabled>Pilih Wakil</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <br>
                    <label for="foto1" class="form-label">Foto</label>
                    <input class="form-control" type="file" name="foto1" id="foto1" required>
                </div>
                <div class="card p-3">
                    <h4 class="text-center">Paslon 2</h4>
                    <label for="name" class="form-label">Ketua</label>
                    <select class="form-control" id="ketua2" name="ketua2" required>
                        <option selected disabled>Pilih Ketua</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <br>
                    <label for="name" class="form-label">Wakil</label>
                    <select class="form-control" id="wakil2" name="wakil2" required>
                        <option selected disabled>Pilih Wakil</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <br>
                    <label for="foto2" class="form-label">Foto</label>
                    <input class="form-control" type="file" name="foto2" id="foto2" required>
                </div>
                <div class="card p-3">
                    <h4 class="text-center">Paslon 3</h4>
                    <label for="name" class="form-label">Ketua</label>
                    <select class="form-control" id="ketua3" name="ketua3" required>
                        <option selected disabled>Pilih Ketua</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <br>
                    <label for="name" class="form-label">Wakil</label>
                    <select class="form-control" id="wakil3" name="wakil3" required>
                        <option selected disabled>Pilih Wakil</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                        @endforeach
                    </select>
                    <br>
                    <label for="foto3" class="form-label">Foto</label>
                    <input class="form-control" type="file" name="foto3" id="foto3" required>
                </div>
            </div>
            <center>
                <div class="d-flex flex-row justify-content-evenly align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <label for="dimulai" class="form-label fw-bold me-2" style="width: 100px;">Dimulai :</label>
                        <input class="form-control" type="datetime-local" name="dimulai" id="dimulai" required>
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="berakhir" class="form-label fw-bold me-2" style="width: 100px;">Berakhir :</label>
                        <input class="form-control" type="datetime-local" name="berakhir" id="berakhir" required>
                    </div>
                </div>
            </center>
            <center><button type="submit" class="btn btn-primary">Simpan</button></center>
        </form>
    </div>
@endsection
