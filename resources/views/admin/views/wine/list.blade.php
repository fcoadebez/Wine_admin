@extends('admin.layout')

@section('html_title')Vins | Administration @endsection
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
						<div class="panel-heading">Listes des vins
							<div class="tools">
								<a href="{{ URL::to("/admin/wine/add") }}">
									<span class="icon s7-plus"></span>
								</a>
								<a href="{{ URL::to("/admin/wine/list") }}">
									<span class="icon s7-refresh"></span>
								</a>
							</div>
						</div>
						<div class="panel-body">
							@if(session('alert'))
								<div class="col-lg-12">
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
								</div>
							@endif

							<table id="table1" class="table table-striped table-hover">
								<thead>
								<tr>
									<th>Dénomination</th>
									<th>Annee</th>
									<th>Type</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tbody>
								@foreach($vins as $vin)
									<tr>
										<td>{{ $vin->name }}</td>
										<td>{{ $vin->year }}</td>
										<td>{{ $vin->rType()->first()->type }}</td>
										<td>
											@if($vin->order != "1")
												<a href="{{ URL::to("/admin/wine/".$vin->id."/order/up") }}">
													<span class="icon s7-angle-up"></span>
												</a>
											@else
												&nbsp; &nbsp; &nbsp;
											@endif

											@if($vin->order != $vin->count())
												<a href="{{ URL::to("/admin/wine/".$vin->id."/order/down") }}">
													<span class="icon s7-angle-down"></span>
												</a>
											@else
												&nbsp; &nbsp; &nbsp;
											@endif

											<a href="{{ URL::to("/admin/wine/".$vin->id."/edit") }}" title="Edition deu vin">
												<span class="icon s7-edit"></span>
											</a>
											<a href="{{ URL::to("/admin/wine/".$vin->id."/remove") }}" title="Supprimer le vin">
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