@extends('installer::layouts.app', [
    'title' => 'Database'
])

@section('content')
    <form action="{{ route('installer.database.store') }}" method="post" class="ajaxform">
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <label for="driver">Database Driver</label>
                <select class="formSelect" name="driver" id="driver" aria-label="Default select example" required>
                    <option value="mysql" selected>MySQL</option>
                    <option value="pgsql">PgSQL</option>
                    <option value="sqlsrv">SQLSrv</option>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label for="host">Database Host</label>
                <input type="text" class="form-control" name="host" id="host" value="localhost" placeholder="Database Host" required>
            </div>

            <div class="col-12 col-md-6">
                <label for="port">Database Port</label>
                <input type="number" class="form-control" name="port" id="port" value="3306" placeholder="Database Port" required>
            </div>

            <div class="col-12 col-md-6">
                <label for="username">Database User Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Database User Username" required>
            </div>

            <div class="col-12 col-md-6">
                <label for="password">Database User Password</label>
                <input type="text" class="form-control" name="password" id="password"
                       placeholder="Database User Password">
            </div>

            <div class="col-12 col-md-6">
                <label for="database">Database Name</label>
                <input type="text" class="form-control" name="database" id="database" placeholder="Database Name" required>
            </div>

            <div class="button-group">
                <div class="row justify-content-end">
                    <div class="col-12 col-md-6">
                        <button class="btn btn-primary w-100" type="submit">
                            Save & Continue
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('pageScripts')
    <script>
        $('#driver').on('change', function () {
            if($(this).val() === 'mysql'){
                $('#port').val('3306')
            }else if($(this).val() === 'pgsql') {
                $('#port').val('5432')
            }else if($(this).val() === 'sqlsrv') {
                $('#port').val('1433')
            }
        })
    </script>
@endpush
