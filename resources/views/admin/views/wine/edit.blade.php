@extends('admin.layout')

@section('html_title')Edition | Vin | Administration @endsection
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
                        Modification du vin - {{ $wine->denomination }}
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
                        <form method="POST" action="{{ URL::to('/admin/wine/'.$id.'/edit') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Dénomination</label>
                                <div class="col-6">
                                    <input type="text" name="denomination" maxlength="150" class="form-control" value="{{ $wine->denomination }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Millésime</label>
                                <div class="col-6">
                                    <input type="text" name="millesime" maxlength="150" class="form-control" value="{{ $wine->annee }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Photo</label>
                                <div class="col-9" >
                                    <img src="/vins/{{ $wine->photo }}" style="max-height: 100px; margin-bottom: 20px;">
                                </div>
                                <div class="col-3"></div>
                                <div class="col-6">
                                    <input type="file" name="photo" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Catégorie</label>
                                <div class="col-6">
                                    <select id="categorie" name="categorie">
                                      <option value="1" selected>Blanc</option>
                                      <option value="2">Rouge</option>
                                      <option value="3">Rosé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Description</label>
                                <div class="col-6">
                                    <input type="text" name="description" maxlength="150" class="form-control" value="{{ $wine->description }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Prix</label>
                                <div class="col-6">
                                    <input type="text" name="prix" maxlength="150" class="form-control" value="{{ $wine->prix }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12">
                                    <br/>
                                    <p class="text-right">
                                        <button type="submit" class="btn btn-space btn-yellow btn-primary">Enregistrer</button>
                                        <a href="{{ URL::to('/admin/vins/list') }}" class="btn btn-space btn-secondary">Annuler</a>
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