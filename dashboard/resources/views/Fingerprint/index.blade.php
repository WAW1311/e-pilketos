@extends('layout.app')
@section('content')
    <div class="p-3">
        <div>
            <h2 class="text-center">Data Sidik Jari</h2>
            <div class="d-flex justify-content-start">
                <a href="{{ route('fingerprint.create') }}" class="fw-semibold btn btn-primary mb-3">Tambah Data</a>
            </div>
        </div>
        <div>
            <table id="myTable" class="table table-responsive">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">Hashed Sidik Jari</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $fp)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $fp->siswa->nama }}</td>
                            <td>{{ Str::limit($fp->template, 20) }}</td>
                            <td>
                                <a href="{{ route('fingerprint.edit', $fp) }}" class="fw-semibold btn btn-warning">Edit</a>
                                <form action="{{ route('fingerprint.destroy', $fp) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirmation(this)">
                                    @csrf
                                    @method('DELETE')
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
