@extends('admin.layout')

@section('html_title')Configurateur | Administration @endsection
@section('html_body')id="configurator_list"@endsection
@section('html_js') @endsection
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
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Paramètres</div>
                    <div class="panel-body">
                        <a href="{{ URL::to("/admin/settings/user/list") }}" class="btn btn-space btn-primary btn-big btn-yellow"><i class="icon s7-users"></i> Utilisateurs </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection