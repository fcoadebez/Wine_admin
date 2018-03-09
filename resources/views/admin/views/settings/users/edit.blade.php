@extends('admin.layout')

@section('html_title')Edition | Utilisateurs | Paramètres | Administration @endsection
@section('html_body')id="settings_user_edit"@endsection
@section('html_js')
<script type="text/javascript">
</script>
@endsection
@section('html_css')
<style type="text/css">
</style>
@endsection
@section('html_content')
@include('admin.partials/_nav')
<div class="mai-wrapper">
    @include('admin.partials/_menu')
    <div class="main-content container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-divider">
                        Edition d'un utilisateur
                        <span class="panel-subtitle">Editer les champs que vous souhaitez modifier.</span>
                    </div>
                    <div class="panel-body">

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

                        <br/>
                        <form method="POST" action="{{ URL::to('/admin/settings/user/'.$user->id.'/edit') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <div class="col-12 form-group row" style="padding: 0;">
                                    <label class="col-3 col-form-label text-right">Nom</label>
                                    <div class="col-6">
                                        <input type="text" name="name" maxlength="50" class="form-control" value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="clear"></div>

                                <div class="col-12 form-group row" style="padding: 0;">
                                    <label class="col-3 col-form-label text-right">Prénom</label>
                                    <div class="col-6">
                                        <input type="text" name="firstname" maxlength="50" class="form-control" value="{{ $user->firstname }}">
                                    </div>
                                </div>
                                <div class="clear"></div>

                                <div class="col-12 form-group row" style="padding: 0;">
                                    <label class="col-3 col-form-label text-right">Email</label>
                                    <div class="col-6">
                                        <input type="email" name="email" maxlength="75" class="form-control" value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="clear"></div>

                                <div class="col-12 form-group row" style="padding: 0;">
                                    <label class="col-3 col-form-label text-right">Mot de passe</label>
                                    <div class="col-6">
                                        <input type="password" name="password" maxlength="100" class="form-control">
                                    </div>
                                </div>
                                <div class="clear"></div>

                                <div class="col-12 form-group row" style="padding: 0;">
                                    <div class="col-12">
                                        <br/>
                                        <p class="text-right">
                                            <button type="submit" class="btn btn-space btn-yellow btn-primary">Enregistrer</button>
                                            <a href="{{ URL::to('/admin/settings/user/list') }}" class="btn btn-space btn-secondary">Annuler</a>
                                        </p>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection