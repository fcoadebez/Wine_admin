@extends('admin.layout')

@section('html_title')Mot de passe | Administration @endsection
@section('html_body')class="mai-splash-screen" id="login"@endsection
@section('html_css')
<style type="text/css">


</style>
@endsection
@section('html_content')
<div class="mai-wrapper mai-login">
    <div class="main-content container">
        <div class="splash-container row">
            <div class="col-sm-12 form-message">
                <div class="logo">
                    <img src="{{ asset('web-admin/assets/img/logo-2x.png') }}" alt="logo" width="239"
                    class="logo-img mb-4">
                </div>
                <span
                class="splash-description text-center mt-5 mb-5">Modifier mon mot de passe</span>

                @if(isset($alert))
                <div role="alert" class="alert alert-{{ $alert['type'] }} alert-icon alert-icon-colored alert-dismissible">
                    <div class="icon"><span class="s7-{{ $alert['icon'] }}"></span></div>
                    <div class="message">
                        <button type="button" data-dismiss="alert" aria-label="Close" class="close">
                            <span aria-hidden="true" class="s7-close"></span>
                        </button>
                        {{ $alert['message'] }}
                    </div>
                </div>
                @endif

                @if(isset($redirect))
                <meta http-equiv="refresh" content="{{$redirect['time']}};URL='{{$redirect['url']}}'" />    
                @endif

                <form action="{{ URL::to('/admin/user/password') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <div class="input-group"><span class="input-group-addon"><i class="icon s7-lock"></i></span>
                            <input type="password" name="password_good" placeholder="Mot de passe actuel" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group"><span class="input-group-addon"><i class="icon s7-key"></i></span>
                            <input type="password" name="password_new" placeholder="Nouveau mot de passe" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group"><span class="input-group-addon"><i class="icon s7-key"></i></span>
                            <input type="password" name="password_new2" placeholder="Confirmation du mot de passe" class="form-control">
                        </div>
                    </div>
                    <div class="form-group login-submit">
                        <button data-dismiss="modal" type="submit" class="btn btn-lg btn-primary btn-yellow btn-block">Modifier</button>
                    </div>
                </form>
                <div class="out-links"><a href="http://www.hetis.fr">Copyright © 2017 Hetis</a></div>
            </div>
        </div>
    </div>
</div>
@endsection