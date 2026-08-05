@extends('layout.app')
@section('content')
    <div class="card p-3">
        <h3 class="text-center mb-5 fw-bold">Ubah Data Sidik Jari</h3>
        <form action="{{ route('fingerprint.store') }}" method="POST">
            @csrf
            <label for="name" class="form-label">Siswa</label>
            <select class="form-control" id="siswa" name="siswa">
                <option selected disabled>Pilih Siswa</option>
                @foreach ($siswa as $s)
                    <option <?= $fingerprint->siswa->nis == $s->nis ? 'selected' : '' ?> value="{{ $s->nis }}">{{ $s->nis }} -- {{ $s->nama }}</option>
                @endforeach
            </select>
            <br>
            <label for="name" class="form-label">Surat Suara</label>
            <select class="form-control" id="vote_id" name="vote_id">
                <option selected disabled>Pilih Surat Suara</option>
                @foreach ($votepapper as $papper)
                    <option <?= $fingerprint->vote->vote_id == $papper->vote_id ? 'selected' : '' ?> value="{{ $papper->vote_id }}">{{ $papper->vote_id }} -- {{ $papper->periode }}</option>
                @endforeach
            </select>
            <br>
            <div class="mb-3">
                <label for="template" class="form-label">Template Sidik Jari</label>
                <textarea type="text" class="form-control" id="template" name="template" readonly>{{ $fingerprint->template }}</textarea>
            </div>
            <center><button type="submit" class="btn btn-primary">Simpan</button></center>
        </form>
    </div>
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            window.Echo.channel('stored-fingerprint')
                .listen('.fingerprint.stored', (e) => {
                    console.log('Fingerprint received:', e.fingerprint);
                    document.getElementById('template').value = e.fingerprint;
                });
        });
    </script>
@endsection
