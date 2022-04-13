<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Sistema de ERP</title>
  <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
  <!-- DaterangePicker -->
  <link rel="stylesheet" href="{{ asset('page/assets/js/daterangepicker/daterangepicker.css') }}">

  <!-- fullCalendar 2.2.5-->
  <link href="{{ asset('fullcalendar-scheduler/main.min.css') }}" rel="stylesheet" type="text/css" />
  
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=utf-8">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css')}}">

  @stack('styles')
  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
  <link rel="stylesheet" href="{{ asset('estilos_personalizados/styles.css')}}">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- Iconos de Bootstrap descargados-->
  <link rel="stylesheet" href="{{asset('css/bootstrap-icons-1.8.1/bootstrap-icons.css')}}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <script src="https://cdn.ckeditor.com/ckeditor5/32.0.0/classic/ckeditor.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    @include('admin.layout.header')
    <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    @include('admin.layout.aside')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              @yield('title')
              {{--  <h1 class="m-0 text-dark">Dashboard</h1> --}}
            </div><!-- /.col -->
            <div class="col-sm-6">
              @yield('content-header')

            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
       @include('auxiliares.mensaje_exito')
       @include('auxiliares.error_formulario')
       <div class="container-fluid">
        @yield('content')

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @include('admin.layout.footer')

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js')}}"></script>

<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.js')}}"></script>


<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>




@stack('scripts')


<!-- overlayScrollbars -->
<script src="{{ asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('assets/dist/js/demo.js')}}"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script>
  $(function () {
       $('#agregar-caducidad').click(function(){
            $('#caducidad-produ').append('<div class="row"><div class="col-md-2"><label for="nombre">No. de piezas:</label><input type="text" name="num_cad[]" class="form-control" required></div><div class="col-md-3"><label for="nombre">Fecha de caducidad:</label><input type="date" name="fecha_cad[]" class="form-control" required></div></div>');
       });
       /*
       $('#referencia').change(function(){
            var op_ref = $(this).val();

            if(op_ref != 1){
              $('#ver-referencia').show();
            }else{
              $('#ver-referencia').hide();
            }
       });
       */
      $('.btn-confirm').on('click', function(){
          var confirm = $(this).val();
          var data = confirm.split("-");
          confirm = data[0];
          id_orden = data[1];
          
          $.ajax({
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
              type: "POST",
              url: "{{ url('/confirmacion-p') }}",
              data: { confirm: confirm, id_orden: id_orden },
              success: function(response){
                  if(response.msg == 1){     
                      $('.a-' + id_orden).hide();
                      $('.r-' + id_orden).show();
                  }else{
                      $('.r-' + id_orden).hide();
                      $('.a-' + id_orden).show();
                  }
              }
          });
      });
      /*
      setTimeout(function(){
        $("#table_id").DataTable();
      }, 1500);*/
  });
</script>
<script>
  ClassicEditor
          .create( document.querySelector( '#editor1' ) )
          .then( editor => {
                  console.log( editor );
          } )
          .catch( error => {
                  console.error( error );
  });
  ClassicEditor
          .create( document.querySelector( '#editor2' ) )
          .then( editor => {
                  console.log( editor );
          } )
          .catch( error => {
                  console.error( error );
  });
  ClassicEditor
          .create( document.querySelector( '#editor3' ) )
          .then( editor => {
                  console.log( editor );
          } )
          .catch( error => {
                  console.error( error );
  });
  ClassicEditor
          .create( document.querySelector( '#editor4' ) )
          .then( editor => {
                  console.log( editor );
          } )
          .catch( error => {
                  console.error( error );
  });
</script>
<script>
  $(document).ready( function() {
    /*
      setTimeout(function(){
        alert('ACTIVO');
        $('#table_id').DataTable();
      }, 7000);*/
      //$('#table_id').DataTable();
  });
</script>
</body>

</html>
