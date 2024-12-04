<!doctype html> <!-- This "switcher.blade.php" master page is used only for "switcher" page content present in "views/livewire" -->
<html lang="en" dir="ltr">
	<head>

		<!-- META DATA -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="{{env('APP_NAME')}}">
		<meta name="keywords" content="dengue, vacunacion, corrientes">

        @include('layouts.components.styles')

    </head>

    <body class="ltr app sidebar-mini">

        <!-- GLOBAL-LOADER -->
		<div id="global-loader">
			<img src="{{asset('assets/images/loader.svg')}}" class="loader-img" alt="Loader">
		</div>
		<!-- /GLOBAL-LOADER -->

        <!-- PAGE -->
		<div class="page">
			<div class="page-main">

                @include('layouts.components.app-header1')

                @include('layouts.components.app-sidebar')

                <!--app-content open-->
                <div class="app-content main-content mt-0">
                    <div class="side-app">

                        <!-- CONTAINER -->
                        <div class="main-container container-fluid">

                                @yield('content')

                        </div>
                    </div>
                </div>
                    <!-- CONTAINER CLOSED -->
             </div>

            @include('layouts.components.modal')

            @yield('modal')

            @include('layouts.components.footer')

        </div>
        <!-- page -->

        @include('layouts.components.scripts')

    </body>

</html>
