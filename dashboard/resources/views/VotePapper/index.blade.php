@extends('layout.app')
@section('content')
    <div class="p-3">
        <a class="btn btn-primary m-3 ms-0 fw-semibold" href="{{ route('tambah_surat_GET') }}">Tambah Surat Suara</a>
        <table id="myTable" class="table table-responsive">
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
                            <div class="d-flex flex-row align-items-center">
                                <a href="{{ route('votecounts', ['vote_id' => $votepapper->vote_id]) }}"
                                    class="btn btn-success me-3">Hasil</a>
                                <a href="{{ route('update_surat_GET', ['id' => $votepapper->vote_id]) }}"
                                    class="btn btn-warning me-3">Edit</a>
                                <form action="{{ route('delete_surat_POST', ['id' => $votepapper->vote_id]) }}"
                                    method="POST" class="d-inline" onclick="return confirmation(this)">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
