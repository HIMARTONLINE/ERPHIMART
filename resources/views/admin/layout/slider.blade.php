<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">

        <div class="image">
            <img src="{{ asset('assets/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>

        <div class="info">
            <a href="#" class="d-block">Pablo Ramos Jimenez</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->

            <li class="nav-header">Menú</li>

            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fas fa-store"></i>
                    <p>Sucursales</p>
                </a>
            </li>


            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Usuarios</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fas fa-user-check"></i>
                    <p>Clientes</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{route('admin.productos.index')}}" class="nav-link">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Productos</p>


                </a>
            </li>

            <li class="nav-item">
                <a href="" class="nav-link">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Categorias</p>


                </a>
            </li>


            <li class="nav-item">
                <a href="" class="nav-link">

                    <i class="nav-icon fa-solid fas fa-address-card"></i>


                    <p>Empleados</p>
                </a>
            </li>


            <li class="nav-item">
                <a href="{{route('buscar.mes.index')}}" class="nav-link">
                    <i class="nav-icon fa-solid fas  fa-cash-register"></i>



                    <p>Ventas</p>
                </a>
            </li>








            <li class="nav-item">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="nav-link">
                    <form id="logout-form" action="" method="POST" style="display: none;">
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