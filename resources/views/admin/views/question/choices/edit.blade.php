@extends('admin.layout')

@section('html_title')Edition | Réponse | Configurateur | Administration @endsection
@section('html_body')id="configurator_add"@endsection
@section('html_js')
	<script type="text/javascript"></script>
@endsection
@section('html_css')
	<style type="text/css"></style>
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
							Modification d'un choix pour cette étape
							<span class="panel-subtitle">Entrer un choix possible pour cette étape du configurateur.</span>
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
							<form method="POST" action="{{ URL::to('/admin/configurator/'.$idQuestion.'/choice/'.$idReponse.'/edit') }}" enctype="multipart/form-data">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="form-group row">
									<div class="col-12 form-group row" style="padding: 0;">
										<label class="col-3 col-form-label text-right">Réponse</label>
										<div class="col-6">
											<input type="text" name="name" maxlength="150" class="form-control" value="{{ $reponse->name }}">
										</div>
									</div>
									<div class="clear"></div>

									<div class="col-12 form-group row" style="padding: 0;">
										<label class="col-3 col-form-label text-right">Description</label>
										<div class="col-6">
											<textarea name="description" class="form-control" rows="8">{{ $reponse->description }}</textarea>
										</div>
									</div>
									<div class="clear"></div>

									<div class="col-12 form-group row" style="padding: 0;">
										<label class="col-3 col-form-label text-right">Image</label>
										<div class="col-6">
											<img src="{{ $reponse->image }}" width="100%" style="border: 1px solid #000000"/>
											<input type="file" name="image" class="form-control"/>
										</div>
									</div>
									<div class="clear"></div>

									<div class="col-12 form-group row" style="padding: 0;">
										<label class="col-3 col-form-label text-right">Question finale du devis</label>
										<div class="col-6">
											<div class="switch-button switch-button-danger">
												<input type="checkbox" name="question_next_end" id="question_next_end" @if($reponse->question_next_end) checked @endif>
												<span>
													<label for="question_next_end"></label>
												</span>
											</div>
										</div>
									</div>
									<div class="clear"></div>

									<div class="col-12 form-group row" style="padding: 0;">
										<label class="col-3 col-form-label text-right">Question suivante</label>
										<div class="col-6">
											<select name="question_next_id" class="form-control custom-select" required>
												<option value="0" selected disabled>-</option>
												@foreach($questions as $q)
													<option value="{{$q->id}}" @if($reponse->question_next_id == $q->id) selected @endif>{{$q->order}} : {{$q->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="clear"></div>

									<div class="col-12 form-group row" style="padding: 0;">
										<label class="col-3 col-form-label text-right">Prix</label>
										<div class="col-6">
											<div class="input-group mb-2">
												<input type="text" name="price" class="form-control" placeholder="0,00" value="{{ $reponse->price }}">
												<span class="input-group-addon">€</span>
											</div>
										</div>
									</div>
									<div class="clear"></div>

									<div class="col-12 form-group row" style="padding: 0;">
										<div class="col-12">
											<br/>
											<p class="text-right">
												<button type="submit" class="btn btn-space btn-yellow btn-primary">Enregistrer</button>
												<a href="{{ URL::to('/admin/configurator/'.$idQuestion.'/choices') }}" class="btn btn-space btn-secondary">Annuler</a>
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