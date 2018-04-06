@extends('admin.layout')

@section('html_title')Edition | Question | Administration @endsection
@section('html_body')id="question_edit"@endsection
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
                        Modification de la question - {{ $question->question }}
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
                        <form method="POST" action="{{ URL::to('/admin/question/'.$id.'/edit') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <label class="col-3 col-form-label text-right">Question</label>
                                <div class="col-6">
                                    <input type="text" name="question" maxlength="150" class="form-control" value="{{ $question->question }}">
                                </div>
                            </div>

                            <div class="responses">
                                <div class="response_container">
                                  @foreach($question->rQuestionResponse()->get() as $response)
                                    <div class="response" style="position: relative;">
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label text-right">Réponse</label>
                                            <div class="col-6">
                                                <input type="text" name="response[]" maxlength="250" class="form-control" value="{{ $response->response }}">
                                            </div>
                                        </div>
                                        <div class="profil">
                                            <div class="form-group row">
                                                <label class="col-3 col-form-label text-right">Profil principal</label>
                                                <div class="col-6">
                                                    <select id="profil" name="profil[]">
                                                      @foreach($profils as $profil)
                                                        @if($profil->id == $response->profil_id)
                                                          <option value="{{ $profil->id }}" selected>{{ $profil->profil }}</option>
                                                        @else
                                                          <option value="{{ $profil->id }}">{{ $profil->profil }}</option>
                                                        @endif
                                                      @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <a style='position: absolute; right: 200px; top: 0;' class='btn btn-space btn-secondary delete_response'><span class='icon s7-close'></span></a>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                              <p class="text-center">
                                  <a class="add_response" href="{{ URL::to('/admin/question/list') }}" class="btn btn-space btn-secondary">Ajouter une réponse</a>
                              </p>

                            <div class="form-group row">
                                <div class="col-12">
                                    <br/>
                                    <p class="text-right">
                                        <button type="submit" class="btn btn-space btn-yellow btn-primary">Enregistrer</button>
                                        <a href="{{ URL::to('/admin/question/list') }}" class="btn btn-space btn-secondary">Annuler</a>
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