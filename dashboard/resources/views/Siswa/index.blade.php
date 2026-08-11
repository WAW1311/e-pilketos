@extends('layout.app')
@section('content')
<div class="p-3">
    <div>
        <h2 class="text-center">Data Siswa</h2>
        <div class="d-flex justify-content-start">
            <a href="{{ route('tambah_siswa_GET',['excel' => 'false']) }}" class="fw-semibold btn btn-primary mb-3">Tambah Data</a>
            <a href="{{ route('tambah_siswa_GET',['excel' => 'true']) }}" class="fw-semibold btn btn-success mb-3 ms-2">Import Dari Excel</a>
        </div>
    </div>
    <div class="table-responsive">
        <table id="myTable" class="table align-middle" style="min-width: 640px;">
            <thead>
              <tr>
                <th scope="col">No</th>
                <th scope="col">Nis</th>
                <th scope="col">Nama</th>
                <th scope="col">Kelas</th>
                <th scope="col">Wali Kelas</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($dataSiswa as $siswa)
                <tr>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td>{{ $siswa->nis }}</td>
                  <td>{{ $siswa->nama }}</td>
                  <td>{{ $siswa->kelas }}</td>
                  <td>{{ $siswa->wali_kelas }}</td>
                  <td>
                    <a href="/admin/dashboard/kelola/siswa/update?nis={{ $siswa->nis }}" class="fw-semibold btn btn-warning">Edit</a>
                    <form action="/admin/dashboard/kelola/siswa/delete?nis={{ $siswa->nis }}" method="POST" style="display:inline;" onsubmit="return confirmation(this)">
                        @csrf
                        <button type="submit" class="fw-semibold btn btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
