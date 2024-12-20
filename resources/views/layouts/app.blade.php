<!doctype html>
<html lang="en" dir="ltr">
<!-- This "app.blade.php" master page is used for all the pages content present in "views/livewire" except "custom" and "switcher" pages -->

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ env('APP_NAME') }}">
    <meta name="keywords" content="dengue, vacunacion, corrientes">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJ26HvcKulycOxiXGWasm60NnniILp_Co&loading=async"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/dynamicSelect.css')}}">
    <script src="https://use.fontawesome.com/releases/v6.2.0/js/all.js"></script>
    <script src="{{ asset('assets/js/dynamicSelect.js')}}"></script>


    @include('layouts.components.styles')

    @livewireStyles

</head>

<body class="ltr app sidebar-mini light-mode">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        @php
        use Illuminate\Support\Facades\Storage;
        @endphp
        @if(session('empresa')->logoPath && session('empresa')->logoPath && Storage::exists('public/'.session('empresa')->logoPath))
            <img src="{{ asset('storage/' . session('empresa')->logoPath) }}" width="200px" class="loader-img spring-image" alt="Loader" style="top: calc((100dvh - 200px) / 2)!important;">
        @else
            <img src="{{ asset('assets/images/loader.svg') }}" class="loader-img" alt="Loader">
        @endif
    </div>
    <!-- /GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page">
        <div class="page-main">

            @include('layouts.components.app-header')

            @include('layouts.components.app-sidebar')

            @include('modals.select-empresa')

            <!--app-content open-->
            <div class="app-content main-content mt-0">
                <div class="side-app">

                    <!-- CONTAINER -->
                    <div class="main-container container-fluid">
                        @isset($slot)
                            {{ $slot }}
                        @endisset


                        @yield('content')




                    </div>
                </div>
            </div>
            <!-- CONTAINER CLOSED -->
        </div>


        @yield('modal')

        @include('layouts.components.footer')

    </div>
    <!-- page -->
    @include('layouts.components.scripts')
    @livewireScripts


</body>

</html>
