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
{{--
<!-- Modal -->
<div class="modal fade" id="exampleModalStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Productos que se quedaron con una pieza</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Referencia</th>
              <th scope="col">Imagen</th>
              <th scope="col">Nombre</th>
            </tr>
          </thead>
          <tbody>
          @foreach($data['cantidad'] as $row)
            <tr>
              <td>{{ $row['id'] }}</td>
              <td>{{ $row['referencia'] }}</td>
              <td><img src="https://himart.com.mx/api/images/products/{{ $row['id'] }}/{{ $row['id_img'] }}/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4&display=full" width="30" height="30" /></td>
              <td>{{ $row['nombre'] }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModalVentas" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Productos que no han tenido ventas por mas de 30 días</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Referencia</th>
              <th scope="col">Imagen</th>
              <th scope="col">Nombre</th>
              <th scope="col">Días</th>
            </tr>
          </thead>
          <tbody>
          @foreach($data['lista_produ'] as $key => $produ)
            @if(array_key_exists($key, $data['array_produ']))
              <tr>
                <td>{{ $data['array_produ'][$key]['id'] }}</td>
                <td>{{ $data['array_produ'][$key]['referencia'] }}</td>
                <td><img src="https://himart.com.mx/api/images/products/{{ $data['array_produ'][$key]['id'] }}/{{ $data['array_produ'][$key]['id_img'] }}/?ws_key=I24KTKXC8CLL94ENE1R1MX3SR8Q966H4&display=full" width="30" height="30" /></td>
                <td>{{ $data['array_produ'][$key]['nombre'] }}</td>
                <td>{{ str_replace("+", "", $data['array_produ_dias'][$key]['dias']) }}</td>
              </tr>
            @endif
          @endforeach
          </tbody>
        </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
--}}