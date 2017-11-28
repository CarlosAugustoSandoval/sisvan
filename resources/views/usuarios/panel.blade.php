@extends('layouts.app')
@section('title') Usuarios @endsection
@section('title_aux') Registro y gestión @endsection
@section('content')
    <div class="col-xs-12">
        <div class="panel">
            <div class="panel-header">
                <h3>Usuarios registrados</h3>
                <div class="control-btn">
                    <a href="#" class="panel-maximize hidden"><i class="icon-size-fullscreen"></i></a>
                </div>
            </div>
            <div class="panel-content">

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#mmenu-fw-usuarios').addClass('active');
    </script>
@endsection
