<div>
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Detalles</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('empresas.index') }}" >Empresas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalles</li>
            </ol>
        </div>
    </div>

    {{-- CONTENT --}}
    <div>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif


        <div class=" card custom-card">
            <div class="card-header border-bottom d-flex">
                <a href="{{ route('empresas.index') }}" type="button" 
                    class="btn btn-outline-secondary btn-icon">
                    <i class="fa fa-arrow-left"></i>
                </a>

                <div class="main-header-center d-none d-xl-block mx-3">
                    <h3 class="card-title">{{ $empresa->empresa }}</h3>
                </div>
            </div>

            <!-- ROW-1 OPEN -->
            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card">
                    <div class="tab-content">
                            <div class="tab-pane active show" id="profileMain">
                                <div class="card-body p-0">
                                    <div class="p-5">
                                        <h3 class="card-title">Datos generales</h3>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6><b>Nombre</b></h6>
                                                <p class="text-dark-light">{{ $empresa->empresa }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6><b>Key</b></h6>
                                                <p class="text-dark-light">{{ $empresa->key }}</p>
                                            </div>
                                        </div>                                                                                                                                                                                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>