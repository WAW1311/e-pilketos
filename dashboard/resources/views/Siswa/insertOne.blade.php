@extends('layout.app')
@section('content')
<div class="py-3">
    <h2 class="text-center">Tambah Data Siswa</h2>
    <center><form class="w-25 border p-3" action="{{ route('tambah_siswa_POST',['excel' => 'false']) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nis" class="form-label">NIS</label>
            <input type="text" class="form-control" id="nis" name="nis" required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" id="kelas" name="kelas" required>
        </div>
        <div class="mb-3">
            <label for="wali_kelas" class="form-label">Wali Kelas</label>
            <input type="text" class="form-control" id="wali_kelas" name="wali_kelas" required>
        </div>
        <div class="d-flex justify-content-between">
            <a href="{{ route('kelola_siswa') }}" class="btn btn-danger">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form></center>
</div>
@endsection
