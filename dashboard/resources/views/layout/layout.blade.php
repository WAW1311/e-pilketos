<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('assets/DataTables/datatables.min.css') }}">
    @vite(['resources/js/app.js'])
    <title>{{ $title }}</title>
</head>

<body class="bg-body-tertiary">
    @yield('body')
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    {{-- <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
    <script>
        function confirmation(form) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

            return false;
        }
    </script>
    @if (session()->has('success'))
        <script>
            Swal.fire({
                title: "Sukses!",
                text: @json(session('success')),
                icon: "success"
            });
        </script>
    @elseif (session()->has('error'))
        <script>
            Swal.fire({
                title: "Error!",
                text: @json(session('error')),
                icon: "error"
            });
        </script>
    @endif
    <div style="bottom: 0; width: 100%; z-index: 9999;">
        <footer>
            @include('components.footer')
        </footer>
    </div>
</body>

</html>
