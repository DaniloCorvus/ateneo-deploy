<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ateneo - Software de Gestión en Salud</title>


    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="{{ asset('images/favicon.ico') }}"
    />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> 
</head>
<body>

  <div id="app-prueba">
      <!--  -->
    <div v-if="usuarioAutenticado && (ruta!='login')" >
    <div id="preloader">
      <div id="status">
        <div class="spinner">
          <i class="ri-loader-line spin-icon"></i>
        </div>
      </div>
    </div>
    <div id="layout-wrapper">
        <Navbar @logout="redireccionar">></Navbar> 
        <Sidebar></Sidebar>  
      <div class="main-content">
        <div class="page-content">
          <div class="container-fluid">
              <router-view></router-view>
          </div>
        </div>
        <foot></foot>
      </div> 
    </div>
    </div>
    <div v-else>
        <router-view></router-view>
    </div>
  </div>


<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
