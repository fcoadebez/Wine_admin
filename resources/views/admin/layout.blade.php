<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>@yield('html_title', 'Administration')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="HETIS <contact@hetis.fr>">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('web-admin/assets/img/favicon.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('web-admin/assets/lib/stroke-7/style.css') }}"/>
    <link rel="stylesheet" type="text/css"
    href="{{ asset('web-admin/assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css') }}"/>
    <link rel="stylesheet" type="text/css"
    href="{{ asset('web-admin/assets/lib/x-editable/bootstrap4-editable/css/bootstrap-editable.css') }}"/>
    <link rel="stylesheet" type="text/css"
    href="{{ asset('web-admin/assets/lib/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('web-admin/assets/lib/select2/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('web-admin/assets/css/app.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('web-admin/assets/css/custom.css') }}" type="text/css"/>
</head>
<body @yield('html_body')>
    @yield('html_content')
    <script src="{{ asset('web-admin/assets/lib/jquery/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/tether/js/tether.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}"
    type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/js/app.js') }}" type="text/javascript"></script>

    <script src="{{ asset('web-admin/assets/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/js/dataTables.bootstrap4.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/plugins/buttons/js/buttons.html5.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/plugins/buttons/js/buttons.flash.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/plugins/buttons/js/buttons.print.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/plugins/buttons/js/buttons.colVis.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/datatables/plugins/buttons/js/buttons.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/js/app-tables-datatables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/x-editable/bootstrap4-editable/js/bootstrap-editable.min.js') }}"
    type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/x-editable/inputs-ext/typeaheadjs/typeaheadjs.js') }}"
    type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/x-editable/inputs-ext/typeaheadjs/lib/typeahead.js') }}"
    type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/lib/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/js/app-form-xeditable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/js/custom.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web-admin/assets/js/lang.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //initialize the javascript
            App.init();
        });
    </script>
    <div id="html_js">@yield('html_js')</div>
    <div id="html_css">@yield('html_css')</div>
</body>
</html>