<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">

        <div class="image">
            <img src="{{ asset('images/usuarios/'.Auth::user()->foto) }}" class="img-circle elevation-2" alt="User Image">
        </div>

        <div class="info">
            <a href="#" class="d-block">{{ Auth::user()->name  }} {{ Auth::user()->surname  }}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->

            <li class="nav-header">Menú</li>

            <li class="nav-item">

                <a href="{{route('admin.productos.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Productos</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.clientes.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-user-check"></i>
                    <p>Clientes</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fa-solid fas  fa-cash-register"></i>
                    <p>Reportes<i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.reportes') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Reporte de ventas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.poco-stock') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Productos con poco stock</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.pocas-ventas') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Productos con pocas ventas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.caducidad-proxima') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Productos por caducar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.facturas.index') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Facturas</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fa-solid fas fa-address-card"></i>
                    <p>Empleados<i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{route('admin.permisos.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Permisos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.vacaciones.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Vacaciones</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fa-solid fas fa-users"></i>
                    <p>Recursos Humanos<i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.personal.index') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Personal</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.autorizacion.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Autorizaciones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.festivos.index') }}" class="nav-link">
                            <i class="nav-icon fa-solid bi bi-calendar2-week-fill"></i>
                            <p>Festivos</p>
                        </a>
                    </li>
                </ul>
            </li>           

            



            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fa-solid fas  fa-cash-register"></i>
                    <p>Configuración<i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{route('admin.usuario.index')}}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Categorias</p>
                        </a>
                    </li>
                    

            <li class="nav-item">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout').submit();"
                    class="nav-link">
                    <form id="logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <i class="nav-icon fa fa-sign-out-alt"></i>
                    <p>Cerrar Sesión</p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>