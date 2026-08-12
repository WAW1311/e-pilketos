<div class="offcanvas-lg offcanvas-start flex-shrink-0" tabindex="-1" id="sidebarMenu"
    aria-labelledby="sidebarMenuLabel" style="--bs-offcanvas-width: 260px; z-index: 10000;">
    <div class="offcanvas-header text-bg-primary d-lg-none"
        style="background: linear-gradient(150deg,#00b6ee, #0175b8) !important;">
        <h5 class="offcanvas-title mb-0 fw-bold text-white" id="sidebarMenuLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0 h-100">
        <div class="d-flex flex-column flex-grow-1 w-100 border-end"
            style="background: linear-gradient(150deg,#00b6ee, #0175b8);">
            <div class="text-center mt-3">
                <a href="/">
                    <img class="img-fluid" src="{{ asset('storage/SMANJA.png') }}" alt="Logo SMANJA" width="100">
                </a>
            </div>
            <nav class="nav flex-column mt-4 gap-2">
                <a class="nav-link text-light active fw-semibold" aria-current="page" href="/">Beranda</a>
                <a class="nav-link text-light fw-semibold" href="{{ route('kelola_siswa') }}">Kelola Data Siswa</a>
                {{-- <a class="nav-link text-light fw-semibold" href="{{ route('fingerprint.index') }}">Kelola Sidik Jari</a> --}}
                <a class="nav-link text-light fw-semibold" href="{{ route('kelola_surat') }}">Kelola Surat Suara</a>
                <a class="nav-link text-light fw-semibold" href="{{ route('berita_acara') }}">Berita Acara</a>
            </nav>
        </div>
    </div>
</div>
