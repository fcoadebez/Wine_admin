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
<style>
.cls-1{
  fill : #F5B037 !important;
}
.cls-2{
  fill : #E19D27 !important;
}

</style>
</head>
<body class="mai-splash-screen">
<div class="mai-wrapper mai-error mai-error-404">
<div class="main-content container">
<div class="error-container">
<div class="error-image">
<svg id="Capa_1" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 1018 810">
<polygon points="38 810 146 410 532 242 492 478 392 564 426 760 342 810 38 810" class="cls-1"></polygon>
<polygon points="532 810 732 810 796 576 532 242 492 478 577 389 491 589 589 767 532 810" class="cls-2"></polygon>
<polygon points="0 262 38 364 578 130 596 0 0 262" class="cls-1"></polygon>
<polygon points="936 568 1018 502 596 0 578 130 936 568" class="cls-2"></polygon>
<path d="M303.5,73.5" transform="translate(-41.5 -69.5)" class="cls-2"></path>
<polyline points="348 16 331.95 116.08 237.7 157.7 262 4" class="cls-1"></polyline>
<polygon points="426 26 422 78 332 116 348 16 426 26" class="cls-2"></polygon>
</svg>
</div>
<div class="error-number"> <span>404</span></div>
<div class="error-description">La page demandé n'hexiste pas ou plus.</div>
<div class="error-goback-button"><a href="{{URL::to("/admin")}}" class="btn btn-yellow btn-lg btn-primary">Accueil</a></div>
<div class="footer"> <span class="copy">&copy; 2017 </span><span>MonSiteFR.com</span></div>
</div>
</div>
</div>
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
          </body>
          </html>