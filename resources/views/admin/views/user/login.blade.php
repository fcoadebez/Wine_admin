@extends('admin.layout')

@section('html_title')Connexion | Administration @endsection
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
                    class="logo-img mb-4" style="width: 70px;">
                </div>
                <span
                class="splash-description text-center mt-5 mb-5">Connexion au backoffice</span>
                <form action="{{ URL::to('/admin/user/login') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    @if(isset($alert))
                    <div role="alert" class="alert alert-{{$alert['type']}} alert-icon alert-icon-colored alert-dismissible">
                        <div class="icon"><span class="s7-{{$alert['icon']}}"></span></div>
                        <div class="message">
                            <button type="button" data-dismiss="alert" aria-label="Close" class="close">
                                <span aria-hidden="true" class="s7-close"></span>
                            </button>
                            {{$alert['message']}}
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <div class="input-group"><span class="input-group-addon"><i class="icon s7-user"></i></span>
                            <input id="username" name="email" type="email" placeholder="E-mail" autocomplete="off" style="padding: 10px;    background-color: transparent !important;" 
                            class="form-control" {!! @valueIfExist($inputs["email"]) !!}>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group"><span class="input-group-addon"><i class="icon s7-lock"></i></span>
                            <input id="password" name="password" type="password" placeholder="Mot de passe" class="form-control" style="padding: 10px;">
                        </div>
                    </div>
                    <div class="form-group login-submit">
                        <button data-dismiss="modal" type="submit" class="btn btn-lg btn-primary btn-red btn-block">Connexion</button>
                    </div>
                    <div class="form-group row login-tools">
                        {{--<div class="col-6 login-remember">
                            <label class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input"><span
                                class="custom-control-indicator"></span><span class="custom-control-description">Se souvenir de moi</span>
                            </label>
                        </div>--}}
                    </div>
                </form>
                <div class="out-links"><a href="http://www.hetis.fr">Copyright © 2018 Wine&Me</a></div>
            </div>
        </div>
    </div>
</div>
@endsection