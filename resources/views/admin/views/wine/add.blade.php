@extends('admin.layout')

@section('html_title')Ajout | Vin | Administration @endsection
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
                        Ajout d'un vin
                        <span class="panel-subtitle">Veuillez remplir le formulaire ci dessous</span>
                    </div>
                    <div class="panel-body">
	                    @if(isset($alert))
		                    <div role="alert" class="alert alert-{{$alert['type']}} alert-icon alert-icon-colored alert-dismissible">
			                    <div class="icon">
				                    <span class="s7-{{$alert['icon']}}"></span>
			                    </div>
			                    <div class="message">
				                    <button type="button" data-dismiss="alert" aria-label="Close" class="close">
					                    <span aria-hidden="true" class="s7-close"></span>
				                    </button>
				                    {{$alert['message']}}
			                    </div>
		                    </div>
	                    @endif

                        <br/>
                        <form method="POST" action="{{ URL::to('/admin/wine/add') }}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Dénomination</label>
                                <div class="col-6">
                                    <input type="text" name="denomination" maxlength="150" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Millésime</label>
                                <div class="col-6">
                                    <input type="text" name="millesime" maxlength="150" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Photo</label>
                                <div class="col-6">
                                    <input type="file" name="photo" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Catégorie</label>
                                <div class="col-6">
                                    <select id="categorie" name="categorie">
                                      @foreach($wine_type as $type)
                                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Description</label>
                                <div class="col-6">
                                    <input type="text" name="description" maxlength="150" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Prix</label>
                                <div class="col-6">
                                    <input type="text" name="prix" maxlength="150" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">

                                <div class="col-12">
                                    <br/>
                                    <p class="text-right">
                                        <button type="submit" class="btn btn-space btn-yellow btn-primary">Enregistrer</button>
                                        <a href="{{ URL::to('/admin/wine/list') }}" class="btn btn-space btn-secondary">Annuler</a>
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
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery.curCSS = function(element, prop, val) {
        return jQuery(element).css(prop, val);
    };

    $('#tastes').autocomplete({
      source : '{!!URL::route('autocomplete')!!}',
      appendTo: ".ui-widget"
    });
</script>
@endsection