@extends('admin.layout')

@section('html_title')Devis | Administration @endsection
@section('html_body')id="quote"@endsection
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
                    <div class="panel-heading">Listes des devis
                        <div class="tools">
                            <a href="{{ URL::to("/admin/quote/list") }}"><span class="icon s7-refresh"></span></a>
                        </div>
                    </div>
                    <div class="panel-body">
	                    @if(Session::get('alert') ==! null)
		                    <div class="col-12">
			                    <div role="alert" class="alert alert-{{Session::get('alert')[0]}} alert-icon alert-icon-colored alert-dismissible">
				                    <div class="icon"><span class="s7-{{Session::get('alert')[1]}}"></span></div>
				                    <div class="message">
					                    <button type="button" data-dismiss="alert" aria-label="Close" class="close">
						                    <span aria-hidden="true" class="s7-close"></span>
					                    </button>
					                    {{Session::get('alert')[2]}}
				                    </div>
			                    </div>
		                    </div>
	                    @endif
                        <table id="table1" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Prix</th>
                                    <th>Remise</th>
                                    <th>A payer</th>
                                    <th>A réellement payer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotes as $q)
	                                <tr>
                                    <td>{{ $q->id }}</td>
                                    <td>@if(!is_null($q->date_update)) {{ date('d/m/Y', strtotime($q->date_update)) }} @else {{ date('d/m/Y', strtotime($q->date_creation)) }} @endif</td>
                                    <td>{{ $q->rClient()->first()->name }} {{ $q->rClient()->first()->firstname }}</td>
                                    <td>{{ ($q->recontact == 1 ? "Souhaite être recontacté." : ($q->ordered == 1 ? ($q->payment == 1 ? "A commandé et à payé ".$q->payment_price." €" : "A commandé mais n'a pas payé.") : "")) }}</td>
                                    <td>{{ number_format($q->price*(1-($q->discount/100)), 2, ',', ' ') }} €</td>
                                    <td>{{ $q->discount }} %</td>
                                    <td>{{ number_format($q->payment_price, 2, ',', ' ') }} €</td>
                                    <td>{{ number_format((function($mts){
                                        $s = 0;
                                        foreach ($mts as $mt => $m){
                                            $s = $s + $m->montant;
                                        }
                                        return $s;
                                    })($q->rDevisTransactions()->where("finish", "=", "1")->get()), 2, ',', ' ') }} €</td>
                                    <td>
                                        {{--<a href="{{ URL::to("/admin/quote/".$q->id."/details") }}" title="Voir le détail"><span class="icon s7-look"></span></a>
                                        &nbsp;--}}
                                        <a href="{{ URL::to($q->estimate) }}" title="Voir le PDF de devis"><span class="icon s7-note2"></span></a>
                                        &nbsp;
                                        <a href="{{ URL::to($q->invoice) }}" title="Voir le PDF de facture"><span class="icon s7-file"></span></a>
                                        &nbsp;
                                        <a href="{{ URL::to("/admin/quote/".$q->id."/discount") }}" title="Appliquer une remise"><span class="icon s7-ticket"></span></a>
                                        &nbsp;
                                        <a href="{{ URL::to("/admin/quote/".$q->id."/archive") }}" title="Archiver le devis"><span class="icon s7-trash"></span></a>
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