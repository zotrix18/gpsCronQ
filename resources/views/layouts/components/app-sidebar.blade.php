<!--APP-SIDEBAR-->
<style>
    .noBeforeIcon.slide > ul > li:nth-child(1) > a:before{
        content: '';
    }
</style>
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="{{ route('home') }}">
                <h3 class="mx-2 text-start header-brand-img light-logo1" style="font-weight: bold">Vehiculos</h3>
                <h3 class="header-brand-img light-logo" style="font-weight: bold">V</h3>
            </a>
            <!-- LOGO -->
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left">
            <i class="fa-solid fa-location-dot"></i>
            </div>

            <ul class="side-menu">
                <li>
                    <h3>Inicio</h3>
                </li>                            
                
                <li class="noBeforeIcon slide ">
                    <a class="side-menu__item has-link " data-bs-toggle="slide" role="button">
                    <i class="fa-solid fa-location-dot me-2"></i>
                        <span class="side-menu__label">GPS</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu" disabled>                                             
                            <li>
                                <a class="slide-item "
                                    href=""  
                                    disabled                                  
                                    >
                                    Dispositivos
                                </a>
                            </li>
                                                  
                    </ul>
                </li>
            <div class="slide-right" id="slide-right">
                <svg fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </div>
</div>
