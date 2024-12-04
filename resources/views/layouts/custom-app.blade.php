<!doctype html>
<html lang="en" dir="ltr">
<!-- This "custom-app.blade.php" master page is used only for "custom" page content present in "views/livewire" Ex: login, 404 -->

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ env('APP_NAME') }}">
    <meta name="keywords" content="dengue, vacunacion, corrientes">

    @include('layouts.components.landing.styles')

</head>

@yield('body')
@include('modals.select-empresa')

<!-- GLOBAL-LOADER -->
<div id="global-loader">
    <img src="{{ asset('assets/images/loader.svg') }}" class="loader-img" alt="Loader">
</div>

@yield('content')

@include('layouts.components.custom-scripts')

</body>

</html>
