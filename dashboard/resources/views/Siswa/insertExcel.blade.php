@extends('layout.app')
@section('content')
<div class="py-3">
    <h2 class="text-center">Import Data Siswa</h2>
    <center>
        <form class="border p-3 w-100" style="max-width: 420px;" action="{{ route('tambah_siswa_POST',['excel' => 'true']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">File Excel</label>
                <input type="file" class="form-control" id="file" name="file" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('kelola_siswa') }}" class="btn btn-danger">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </center>
</div>
@endsection
