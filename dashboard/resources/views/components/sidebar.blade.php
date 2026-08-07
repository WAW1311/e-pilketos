<div class="border-end" style="width: 15vw; background: linear-gradient(150deg,#00b6ee, #0175b8);">
    <center>
        <div class="mt-2">
            <a class="" href="#">
                <img class="img-fluid" src="{{ asset('storage/SMANJA.png') }}" alt="" width="100">
            </a>
        </div>
    </center>
    <nav class="nav flex-column mt-3">
        <a class="nav-link text-light active fw-semibold" aria-current="page" href="/">Beranda</a>
        <br>
        <a class="nav-link text-light fw-semibold" aria-current="page" href="{{ route('kelola_siswa') }}">Kelola
            Data Siswa</a>
        <br>
        {{-- <a class="nav-link text-light fw-semibold" aria-current="page" href="{{ route('fingerprint.index') }}">Kelola
            Sidik Jari</a> --}}
        <br>
        <a class="nav-link text-light fw-semibold" aria-current="page" href="{{ route('kelola_surat') }}">Kelola
            Surat Suara</a>
    </nav>
</div>
