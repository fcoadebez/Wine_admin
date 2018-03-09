@extends('admin.layout')

@section('html_title')Ajout | Configurateur | Administration @endsection
@section('html_body')id="configurator_add"@endsection
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
                        Appliquer une remise
                        <span class="panel-subtitle">Entrer un pourcentage de remise à appliquer au devis.</span>
                    </div>
                    <div class="panel-body">

                        @if(Session::get('alert') ==! null)
                        <div role="alert" class="alert alert-{{Session::get('alert')[0]}} alert-icon alert-icon-colored alert-dismissible">
                            <div class="icon"><span class="s7-{{Session::get('alert')[1]}}"></span></div>
                            <div class="message">
                                <button type="button" data-dismiss="alert" aria-label="Close" class="close">
                                    <span aria-hidden="true" class="s7-close"></span>
                                </button>
                                {{Session::get('alert')[2]}}
                            </div>
                        </div>
                        @endif

                        <br/>
                        <form method="POST" action="{{ URL::to('/admin/quote/'.$quote->id.'/discount') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <div class="col-12 form-group row" style="padding: 0;">
                                    <label class="col-3 col-form-label text-right">Pourcentage</label>
                                    <div class="col-6">
                                        <div class="input-group mb-2">
                                            <input type="number" name="discount" min="0" max="100" value="{{$quote->discount}}" class="form-control">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>

                                <div class="col-12 form-group row" style="padding: 0;">
                                    <div class="col-12">
                                        <br/>
                                        <p class="text-right">
                                            <button type="submit" class="btn btn-space btn-yellow btn-primary">Enregistrer</button>
                                            <a href="{{ URL::to('/admin/quote/list') }}" class="btn btn-space btn-secondary">Annuler</a>
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