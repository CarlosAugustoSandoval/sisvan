@extends('layouts.applogin')
@section('content')
    <form class="form-signin" role="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <h3><strong>Inicie sesión</strong> con su cuenta</h3>
        <div class="append-icon {{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="email" name="email" class="form-control form-white username" placeholder="Email" value="{{ old('email') }}" required autofocus>
            @if ($errors->has('email'))
                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
            @endif
            <i class="icon-user"></i>
        </div>
        <div class="append-icon m-b-20 {{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" name="password" class="form-control form-white password" placeholder="Contraseña" required>
            @if ($errors->has('password'))
                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
            @endif
            <i class="icon-lock"></i>
        </div>
        <button type="submit" id="submit-form" class="btn btn-block btn-lg btn-dark btn-rounded">Ingresar</button>
        <div class="form-footer">
            <div class="clearfix">
                <p class="new-here"><a id="password" href="{{ route('password.request') }}">Olvidó su contraseña?</a></p>
            </div>
        </div>
    </form>
@endsection
