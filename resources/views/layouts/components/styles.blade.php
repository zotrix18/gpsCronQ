<!-- TITLE -->
<title>{{ env('APP_NAME') }}</title>

<!-- FAVICON -->
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/brand/cronq.png') }}" />

<!-- BOOTSTRAP CSS -->
<link id="style" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

<!-- STYLE CSS -->
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/skin-modes.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

<style>
    #global-loader {
        transition: opacity 0.5s ease;
    }

    #global-loader.fade-out {
        opacity: 0;
    }

    .select2-container {
        width: 100% !important;

    }
</style>

@yield('styles')

<!--- FONT-ICONS CSS -->
<link href="{{ asset('assets/plugins/icons/icons.css') }}" rel="stylesheet" />
