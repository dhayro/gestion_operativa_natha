
{{-- @extends('layouts.app') --}}

{{-- @section('sidebar') --}}
<style>
    .nav-logo .nav-item.theme-logo {
        background-color: white;
        border-radius: 8px;
        padding: 8px;
        margin-bottom: 8px;
    }
    .nav-logo .nav-item.theme-logo img {
        max-width: 60px;
        height: auto;
    }
    .kong a {
        font-size: 18px !important;
        color: #e0e6ed !important;
        font-weight: 600;
        line-height: 1.1;
    }
</style>

<div class="sidebar-wrapper sidebar-theme">

    <nav id="sidebar">

        <div class="navbar-nav theme-brand flex-row  text-center">
            <div class="nav-logo">
                <div class="nav-item theme-logo">
                    <a href="{{getRouterValue()}}dashboard/analytics">
                        <img src="{{Vite::asset('resources/images/Logo sin fondo.png')}}" class="logo-light navbar-logo-g" alt="logo">
                        <img src="{{Vite::asset('resources/images/Logo sin fondo.png')}}" class="logo-dark navbar-logo-g" alt="logo">
                    </a>
                </div>
                <div class="kong ">
                    <a href="{{getRouterValue()}}dashboard/analytics" class="nav-link"> CONTRATISTAS<br>GENERALES </a>
                </div>
            </div>
            <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                </div>
            </div>
        </div>

        <div class="shadow-bottom"></div>
        <ul class="list-unstyled menu-categories" id="accordionExample">

            <li class="menu {{ Request::routeIs('dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('dashboard.index') }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <!-- MAESTROS DE SISTEMA -->
            <li class="menu {{ ($catName === 'maestros-sistema') ? 'active' : '' }}">
                <a href="#maestros-sistema" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'maestros-sistema') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M12 1v6m0 6v6"></path><path d="M4.22 4.22l4.24 4.24m5.08 0l4.24-4.24"></path><path d="M1 12h6m6 0h6"></path><path d="M4.22 19.78l4.24-4.24m5.08 0l4.24 4.24"></path></svg>
                        <span>Maestros de Sistema</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'maestros-sistema') ? 'show' : '' }}" id="maestros-sistema" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('cargos.index') ? 'active' : '' }}">
                        <a href="{{ route('cargos.index') }}">Cargos</a>
                    </li>
                    <li class="{{ Request::routeIs('areas.index') ? 'active' : '' }}">
                        <a href="{{ route('areas.index') }}">Áreas</a>
                    </li>
                    <li class="{{ Request::routeIs('empleados.index') ? 'active' : '' }}">
                        <a href="{{ route('empleados.index') }}">Empleados</a>
                    </li>
                </ul>
            </li>

            <!-- MAESTROS DE NEGOCIO -->
            <li class="menu {{ ($catName === 'maestros-negocio') ? 'active' : '' }}">
                <a href="#maestros-negocio" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'maestros-negocio') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>Maestros de Negocio</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'maestros-negocio') ? 'show' : '' }}" id="maestros-negocio" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('ubigeo.index') ? 'active' : '' }}">
                        <a href="{{ route('ubigeo.index') }}">Ubigeo</a>
                    </li>
                    <li class="{{ Request::routeIs('categorias.index') ? 'active' : '' }}">
                        <a href="{{ route('categorias.index') }}">Categorías</a>
                    </li>
                    <li class="{{ Request::routeIs('unidad_medidas.index') ? 'active' : '' }}">
                        <a href="{{ route('unidad_medidas.index') }}">Unidades de Medida</a>
                    </li>
                    <li class="{{ Request::routeIs('materiales.index') ? 'active' : '' }}">
                        <a href="{{ route('materiales.index') }}">Materiales</a>
                    </li>
                    <li class="{{ Request::routeIs('stock_materiales.index') ? 'active' : '' }}">
                        <a href="{{ route('stock_materiales.index') }}">📦 Stock por Cuadrilla</a>
                    </li>
                    <li class="{{ Request::routeIs('proveedores.index') ? 'active' : '' }}">
                        <a href="{{ route('proveedores.index') }}">Proveedores</a>
                    </li>
                </ul>
            </li>

            <!-- MAESTROS DE ACTIVOS -->
            <li class="menu {{ ($catName === 'maestros-activos') ? 'active' : '' }}">
                <a href="#maestros-activos" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'maestros-activos') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        <span>Maestros de Activos</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'maestros-activos') ? 'show' : '' }}" id="maestros-activos" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('vehiculos.index') ? 'active' : '' }}">
                        <a href="{{ route('vehiculos.index') }}">Vehículos</a>
                    </li>
                    <li class="{{ Request::routeIs('tipo_combustibles.index') ? 'active' : '' }}">
                        <a href="{{ route('tipo_combustibles.index') }}">Tipos de Combustible</a>
                    </li>
                    <li class="{{ Request::routeIs('soats.index') ? 'active' : '' }}">
                        <a href="{{ route('soats.index') }}">SOATs</a>
                    </li>
                    <li class="{{ Request::routeIs('medidor.index') ? 'active' : '' }}">
                        <a href="{{ route('medidor.index') }}">Medidores</a>
                    </li>
                </ul>
            </li>

            <!-- PROCESOS Y SERVICIOS -->
            <li class="menu {{ ($catName === 'procesos-servicios') ? 'active' : '' }}">
                <a href="#procesos-servicios" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'procesos-servicios') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 2 17 12 22 22 17 22 7 12 2"></polygon><polyline points="2 7 12 12 22 7"></polyline><polyline points="12 12 12 22"></polyline></svg>
                        <span>Procesos y Servicios</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'procesos-servicios') ? 'show' : '' }}" id="procesos-servicios" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('tipos-actividad.index') ? 'active' : '' }}">
                        <a href="{{ route('tipos-actividad.index') }}">Tipos de Actividad</a>
                    </li>
                    <li class="{{ Request::routeIs('tipo-comprobantes.index') ? 'active' : '' }}">
                        <a href="{{ route('tipo-comprobantes.index') }}">Tipos de Comprobante</a>
                    </li>
                    <li class="{{ Request::routeIs('neas.index') ? 'active' : '' }}">
                        <a href="{{ route('neas.index') }}">NEAs</a>
                    </li>
                    <li class="{{ Request::routeIs('pecosas.index') ? 'active' : '' }}">
                        <a href="{{ route('pecosas.index') }}">PECOSAs</a>
                    </li>
                    <li class="{{ Request::routeIs('stock.index') ? 'active' : '' }}">
                        <a href="{{ route('stock.index') }}">
                            Consulta de Stock
                        </a>
                    </li>
                </ul>
            </li>

            <!-- PROPIEDADES E INFRAESTRUCTURA -->
            <li class="menu {{ ($catName === 'propiedades') ? 'active' : '' }}">
                <a href="#propiedades" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'propiedades') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>Propiedades e Infraestructura</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                                <ul class="collapse submenu list-unstyled {{ ($catName === 'propiedades') ? 'show' : '' }}" id="propiedades" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('tipo_propiedades.index') ? 'active' : '' }}">
                        <a href="{{ route('tipo_propiedades.index') }}">Tipos de Propiedad</a>
                    </li>
                    <li class="{{ Request::routeIs('construcciones.index') ? 'active' : '' }}">
                        <a href="{{ route('construcciones.index') }}">Construcciones</a>
                    </li>
                    <li class="{{ Request::routeIs('usos.index') ? 'active' : '' }}">
                        <a href="{{ route('usos.index') }}">Usos</a>
                    </li>
                    <li class="{{ Request::routeIs('situaciones.index') ? 'active' : '' }}">
                        <a href="{{ route('situaciones.index') }}">Situaciones</a>
                    </li>
                    <li class="{{ Request::routeIs('servicios-electricos.index') ? 'active' : '' }}">
                        <a href="{{ route('servicios-electricos.index') }}">Servicios Eléctricos</a>
                    </li>
                    <li class="{{ Request::routeIs('suministro.index') ? 'active' : '' }}">
                        <a href="{{ route('suministro.index') }}">Suministros</a>
                    </li>
                </ul>
            </li>

            <li class="menu {{ ($catName === 'operativa') ? 'active' : '' }}">
                <a href="#operativa" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'operativa') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        <span>Gestión Operativa</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'operativa') ? 'show' : '' }}" id="operativa" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('cuadrillas.index') ? 'active' : '' }}">
                        <a href="{{ route('cuadrillas.index') }}">Cuadrillas</a>
                    </li>
                    <li class="{{ Request::routeIs('papeletas.index') ? 'active' : '' }}">
                        <a href="{{ route('papeletas.index') }}">Papeletas de Trabajo</a>
                    </li>
                    <li class="{{ Request::routeIs('fichas_actividad.index') ? 'active' : '' }}">
                        <a href="{{ route('fichas_actividad.index') }}">Fichas de Actividad</a>
                    </li>
                </ul>
            </li>

            <!-- CONSULTAS E INFORMES -->
            <li class="menu {{ ($catName === 'consultas') ? 'active' : '' }}">
                <a href="#consultas" data-bs-toggle="collapse" aria-expanded="{{ ($catName === 'consultas') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        <span>Consultas e Informes</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'consultas') ? 'show' : '' }}" id="consultas" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('consulta_nea.index') ? 'active' : '' }}">
                        <a href="{{ route('consulta_nea.index') }}"><i class="fas fa-search"></i> NEAs y Movimientos</a>
                    </li>
                </ul>
            </li>

        </ul>
        
    </nav>

</div>
{{-- @endsection --}}