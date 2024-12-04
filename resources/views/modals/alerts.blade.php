{{-- start:alertSuccess --}}
@if(session('success') !== null)
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span class="alert-inner--icon me-2"><i class="fe fe-thumbs-up"></i></span>
    <span class="alert-inner--text"><strong>Exito!</strong> - {{ session('success') }}!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
@endif
{{-- Fin --}}

{{-- start:alertError --}}
@if(session('error') !== null)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span class="alert-inner--icon me-2"><i class="fe fe-slash"></i></span>
    <span class="alert-inner--text"><strong>Error!</strong> - {{ session('error') }}!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
@endif
{{-- Fin --}}

{{-- start:alertError --}}
@if (session('errors') !== null)
    @if(count(session('errors')) > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-inner--icon me-2"><i class="fe fe-slash"></i></span>
        <span class="alert-inner--text"><strong>Error!</strong> - {{ $errors->first() }}!</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    @endif
@endif
{{-- Fin --}}