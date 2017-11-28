@extends('layouts.app')
@section('title') UPGDS @endsection
@section('title_aux') Registro y gestión @endsection
@section('content')
    <div class="col-xs-12">
        <div class="panel">
            <div class="panel-header">
                <h3>UPGDS registradas</h3>
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
        $('#mmenu-fw-upgds').addClass('active');
    </script>
@endsection
