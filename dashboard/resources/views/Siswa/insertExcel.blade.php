@extends('layout.app')

@section('content')

<div class="py-3">

    <h2 class="text-center fw-semibold">Import Data Siswa</h2>

    <div class="card border-info mx-auto mb-3" style="max-width: 420px;">
        <div class="card-header text-white" style="background: linear-gradient(150deg,#00b6ee, #0175b8) !important;">
            Format File Excel
        </div>

        <div class="card-body">
            <p class="mb-2">
                File yang diupload harus berformat <b>CSV, XLS, XLSX</b> dengan kolom utama:
            </p>

            <table class="table table-bordered table-sm text-center mb-2">
                <thead class="table-light">
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>123456</td>
                        <td>Budi Santoso</td>
                        <td>X IPA 1</td>
                        <td>Ahmad, S.Pd</td>
                    </tr>
                </tbody>
            </table>

            <small class="text-muted">
                Pastikan urutan kolom sesuai agar data dapat diproses dengan benar.
            </small>
        </div>
    </div>


    <center>

        <form class="border p-3 w-100"
              style="max-width: 420px;"
              action="{{ route('tambah_siswa_POST',['excel' => 'true']) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="mb-3">
                <label for="file" class="form-label">
                    Upload File Excel <span class="text-danger">*</span>
                </label>

                <input type="file"
                       class="form-control"
                       id="file"
                       name="file"
                       accept=".csv"
                       required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('kelola_siswa') }}"
                   class="btn btn-danger">
                    Kembali
                </a>

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </div>

        </form>

    </center>

</div>

@endsection
