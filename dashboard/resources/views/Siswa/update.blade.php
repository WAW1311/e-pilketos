@extends('layout.app')
@section('content')
<div>
    <h2 class="text-center">Ubah Data Siswa</h2>
    <center><form class="w-25 border p-3" action="{{ route('update_siswa_POST') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nis" class="form-label">NIS</label>
            <input type="text" class="form-control" id="nis" name="nis" value="{{ $siswa->nis }}" required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $siswa->nama }}" required>
        </div>
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" id="kelas" name="kelas" value="{{ $siswa->kelas }}" required>
        </div>
        <div class="mb-3">
            <label for="wali_kelas" class="form-label">Wali Kelas</label>
            <input type="text" class="form-control" id="wali_kelas" name="wali_kelas" value="{{ $siswa->wali_kelas }}" required>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('kelola_siswa') }}" class="btn btn-danger">Kembali</a>
        </div>
    </form></center>
</div>
@endsection
