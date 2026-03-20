<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" /> -->
    <link href="{{ asset('css/datatables/style.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
    <!-- <link href="//cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet"> -->
    <link href="{{ asset('css/datatables/dataTables.min.css') }}" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> -->
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet" />
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/dropzone.min.css') }}" rel="stylesheet" />
    <!-- <link href="https://cdn.datatables.net/rowreorder/1.3.3/css/rowReorder.dataTables.min.css" rel="stylesheet"> -->
    <link href="{{ asset('css/datatables/rowReorder.dataTables.min.css') }}" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" /> -->
    <link href="{{ asset('css/plyr.css') }}" rel="stylesheet" />

    @stack('styles')
</head>

<body class="sb-nav-fixed">
    @yield('content')
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="{{ asset('js/jquery-3.6.0.min.js')}}"></script>
    <!-- <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script> -->
    <script src="{{ asset('js/fontawesome/all.js')}}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="{{ asset('js/bootstrap.bundle.min.js')}}"></script>
    <!-- <script src="//cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script> -->
    <script src="{{ asset('js/jquery.dataTables.min.js')}}"></script>
    <!-- <script src="https://cdn.datatables.net/rowreorder/1.3.3/js/dataTables.rowReorder.min.js"></script> -->
    <script src="{{ asset('js/dataTables.rowReorder.min.js')}}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
    <script src="{{ asset('js/toastr.min.js')}}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <!-- <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script> -->
    <script src="{{ asset('js/plyr.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script> -->
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script> -->
    <script src="{{ asset('js/pdf.min.js')}}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script> -->
    <script src="{{ asset('js/papaparse.min.js')}}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script> -->
    <script src="{{ asset('js/xlsx.full.min.js')}}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.0/mammoth.browser.min.js"></script> -->
    <script src="{{ asset('js/mammoth.browser.min.js')}}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script> -->
    <script src="{{ asset('js/jquery.mask.js')}}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/admin.js')}}"></script>
    @stack('scripts')
</body>

</html>