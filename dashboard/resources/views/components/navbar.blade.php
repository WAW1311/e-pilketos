<div class="p-2 mb-3 border rounded" style="background: linear-gradient(150deg,#00b6ee, #0175b8);">
    <div class="d-flex flex-row-reverse align-items-center">
        <div class="dropdown me-3">
            <i data-bs-toggle="dropdown" class="fa-solid fa-circle-user fa-2x text-white"
                style="cursor: pointer;"></i>
            <ul class="dropdown-menu mt-2">
                <li><a class="dropdown-item fw-bold text-light-emphasis"
                        href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>
        <div class="me-3 fw-bold text-white">Hallo, {{ Auth::user()->name }}</div>
    </div>
</div>
