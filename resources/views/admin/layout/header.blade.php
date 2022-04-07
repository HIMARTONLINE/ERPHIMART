<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
       </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" >
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">5</span>
        </a>
        {{--
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
          @if(count($data['cantidad']) > 0)
            <span class="dropdown-item dropdown-header" data-toggle="modal" data-target="#exampleModalStock">Tienes productos con una pieza</span>
          @else
            <a href="#" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> No tienes notificaciones
              <!-- <span class="float-right text-muted text-sm">3 mins</span> -->
            </a>
          @endif
          <div class="dropdown-divider"></div>
          @if(count($data['cantidad']) > 0)
            <span class="dropdown-item dropdown-header" data-toggle="modal" data-target="#exampleModalVentas">Tienes productos con poca venta</span>
          @else
            <a href="#" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> No tienes notificaciones
              <!-- <span class="float-right text-muted text-sm">3 mins</span> -->
            </a>
          @endif
          --}}
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
        </div>
      </li>
    </ul>
</nav>