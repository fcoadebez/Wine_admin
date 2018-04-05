@extends('admin.layout')

@section('html_title')Configurateur | Administration @endsection
@section('html_body')id="configurator_list"@endsection
@section('html_js')
	<script type="text/javascript">

        //We use this to apply style to certain elements
        $.extend(true, $.fn.dataTable.defaults, {
            dom:
            "<'row mai-datatable-header'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row mai-datatable-body'<'col-sm-12'tr>>" +
            "<'row mai-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
        });

        $.extend($.fn.dataTable.ext.classes, {
            sFilterInput: "form-control form-control-sm",
            sLengthSelect: "form-control form-control-sm",
        });

        $("#table1").dataTable({
            "language": Lang.French.dataTable,
            columnDefs: [
                {orderable: false, targets: -1}
            ]
        });

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
				<div class="col-sm-12">
					<div class="panel panel-default panel-table">
						<div class="panel-heading">Listes des choix possibles de l'étape
							<div class="tools">
								<a href="{{ URL::to("/admin/configurator/".$idQuestion."/choice/add") }}">
									<span class="icon s7-plus"></span>
								</a>
								<a href="{{ URL::to("/admin/configurator/".$idQuestion."/choices") }}">
									<span class="icon s7-refresh"></span>
								</a>
							</div>
						</div>
						<div class="panel-body">
							@if(session('alert'))
								<div role="alert" class="alert alert-{{ session('alert')['type'] }} alert-icon alert-icon-colored alert-dismissible">
									<div class="icon">
										<span class="s7-{{ session('alert')['icon'] }}"></span>
									</div>
									<div class="message">
										<button type="button" data-dismiss="alert" aria-label="Close" class="close">
											<span aria-hidden="true" class="s7-close"></span>
										</button>
										{{ session('alert')['message'] }}
									</div>
								</div>
							@endif

							<table id="table1" class="table table-striped table-hover">
								<thead>
								<tr>
									<th>Réponse</th>
									<th>Description</th>
									<th>Question finale du devis</th>
									<th>Prix</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tbody>
								@foreach($choices as $c)
									<tr>
										<td>{{ $c->name }}</td>
										<td>{{ $c->description }}</td>
										<td>{{ ($c->question_next_end == "1" ? "Oui" : "Non") }}</td>
										<td>{{ number_format($c->price, 2, ',', ' ') }} €</td>
										<td>
											<a href="{{ URL::to("/admin/configurator/".$idQuestion."/choice/".$c->id."/edit") }}">
												<span class="icon s7-edit"></span>
											</a>
											<a href="{{ URL::to("/admin/configurator/".$idQuestion."/choice/".$c->id."/remove") }}">
												<span class="icon s7-trash"></span>
											</a>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection