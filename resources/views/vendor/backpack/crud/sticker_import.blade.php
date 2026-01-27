@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix')),
        'Cromos' => url(config('backpack.base.route_prefix').'/sticker'),
        'Importar CSV' => false,
    ];
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">Importar Cromos desde CSV</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Sube un archivo CSV con los datos de los cromos.</p>
    </section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Subir archivo CSV</h5>
            </div>
            <div class="card-body">
                <form action="{{ url(config('backpack.base.route_prefix').'/sticker/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Archivo CSV</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">Sube un archivo CSV con las columnas especificadas en el formato.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" value="1">
                            <label class="form-check-label" for="update_existing">
                                Actualizar cromos existentes (basado en el número de cromo)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="la la-upload"></i> Importar
                    </button>
                    <a href="{{ url(config('backpack.base.route_prefix').'/sticker') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Formato del CSV</h5>
            </div>
            <div class="card-body">
                <p>El archivo CSV debe tener las siguientes columnas:</p>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Columna</th>
                            <th>Requerida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>number</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>name</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>page_number</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>position_x</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>position_y</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>width</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>height</td><td><span class="badge bg-danger">Sí</span></td></tr>
                        <tr><td>rarity</td><td><span class="badge bg-secondary">No</span></td></tr>
                        <tr><td>is_horizontal</td><td><span class="badge bg-secondary">No</span></td></tr>
                    </tbody>
                </table>

                <p class="mt-3"><strong>Valores para rareza:</strong></p>
                <ul>
                    <li><code>common</code> (por defecto)</li>
                    <li><code>shiny</code></li>
                </ul>

                <p><strong>Valores para is_horizontal:</strong></p>
                <ul>
                    <li><code>0</code> o <code>false</code> - Vertical (por defecto)</li>
                    <li><code>1</code> o <code>true</code> - Horizontal</li>
                </ul>

                <hr>

                <p><strong>Ejemplo:</strong></p>
                <pre class="bg-light p-2" style="font-size: 0.75rem; overflow-x: auto;">number,name,page_number,position_x,position_y,width,height,rarity,is_horizontal
1,Mascota,1,50,100,150,200,common,0
2,Balón,1,250,100,150,200,shiny,1</pre>

                <a href="{{ url(config('backpack.base.route_prefix').'/sticker/import/template') }}" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="la la-download"></i> Descargar plantilla
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
