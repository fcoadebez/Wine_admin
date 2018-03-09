@extends('admin.layout')

@section('html_title')Utilisateurs | Paramètres | Administration @endsection
@section('html_body')id="settings_user_list"@endsection
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
                    <div class="panel-heading">Listes des utilisateurs
                        <div class="tools">
                            <a href="{{ URL::to("/admin/settings/user/add") }}"><span class="icon s7-plus"></span></a>
                            <a href="{{ URL::to("/admin/settings/user/list") }}"><span class="icon s7-refresh"></span></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if(isset($alert))
                        <div class="col-12">
                            <div role="alert" class="alert alert-{{$alert['type']}} alert-icon alert-icon-colored alert-dismissible">
                                <div class="icon"><span class="s7-{{$alert['icon']}}"></span></div>
                                <div class="message">
                                    <button type="button" data-dismiss="alert" aria-label="Close" class="close">
                                        <span aria-hidden="true" class="s7-close"></span>
                                    </button>
                                    {{$alert['message']}}
                                </div>
                            </div>
                        </div>
                        @endif

                        <table id="table1" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Date de création du compte</th>
                                    <th>Date de dernière connexion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $u)
                                <tr>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->firstname }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ date('d/m/Y', strtotime($u->date_creation)) }}</td>
                                    <td>{{ ($u->date_lastauth != "" && !is_null($u->date_lastauth) ? date('d/m/Y', strtotime($u->date_lastauth)) : "") }}</td>
                                    <td>
                                        <a href="{{ URL::to("/admin/settings/user/".$u->id."/edit") }}"><span class="icon s7-edit"></span></a>
                                        @if(app('request')->session()->get("user")->id != $u->id)
                                        <a href="{{ URL::to("/admin/settings/user/".$u->id."/remove") }}"><span class="icon s7-trash"></span></a>
                                        @endif
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