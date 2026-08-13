<div class="p-2 m-2 border rounded" style="background: linear-gradient(150deg,#00b6ee, #0175b8);">
    <div class="d-flex align-items-center justify-content-between">
        <button class="btn btn-link text-white d-lg-none p-0 ms-2" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-label="Buka menu">
            <i class="fa-solid fa-bars fa-lg"></i>
        </button>
        <div class="d-flex align-items-center ms-auto">
            <div class="me-3 fw-bold text-white text-truncate">Hallo, {{ Auth::user()->name }}</div>
            <div class="dropdown me-2">
                <i data-bs-toggle="dropdown" class="fa-solid fa-circle-user fa-2x text-white"
                    style="cursor: pointer;"></i>
                <ul class="dropdown-menu dropdown-menu-end mt-2">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item fw-bold text-light-emphasis" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
