<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=EDGE" >
  <meta name="_token" content="{{ csrf_token() }}"/>

  <title>Network BOM Builder</title>

  <!-- Open Sans Google Font -->
  <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

  <!-- Jquery -->
  {!! HTML::script('js/jquery-2.1.3.min.js') !!}

  <!-- Bootstrap 3 -->
  {!! HTML::style('css/bootstrap.min.css') !!}
  {!! HTML::style('css/bootstrap-theme.min.css') !!}
  {!! HTML::script('js/bootstrap.min.js') !!}

  <!-- Bootstrap Editable Elements -->
  {!! HTML::style('css/bootstrap-editable.css') !!}
  {!! HTML::script('js/bootstrap-editable.min.js') !!}

  <!-- Editable Table Widget -->
  {!! HTML::script('js/jquery-editableTableWidget.js') !!}

  <!-- Custom Javascript/CSS for the BOM Site -->
  {!! HTML::style('css/bom.css') !!}
  {!! HTML::script('js/bom.js') !!}

  @yield('head')

</head>
<body>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" 
        class="navbar-toggle collapsed" 
        data-toggle="collapse" 
        data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/">BOM Builder</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        @yield('menu')
        @if( Auth::check() )
        <li class="dropdown">
          <a href="#" 
            class="dropdown-toggle" 
            data-toggle="dropdown" 
            role="button" 
            aria-expanded="false">
            Manage<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/site">    Sites</a></li>
            <li><a href="/bom">     BOMs</a></li>
            <li><a href="/part">    Parts</a></li>
            <li><a href="/vendor">  Vendors</a></li>
            <li><a href="/cablerun">Cableruns</a></li>
            <li><a href="/cable">   Cables</a></li>
            <li class="divider"></li>
            <li><a href="/user">    Users</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" 
            class="dropdown-toggle dropdown-success" 
            data-toggle="dropdown" 
            role="button" 
            aria-expanded="false">
            Add
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            @yield('addMenu')
            <li class="divider"></li>
            <li><a class="btn-modal" data-modal="bom" href="#">New BOM</a></li>
          </ul>
        </li>
        @endif
      </ul>
      <div class="navbar-form navbar-right" role="login">
        @if( Auth::check() )
        <button id="logout-btn" name="logout-btn" class="btn btn-danger">
          <span class="glyphicon glyphicon-user"></span> 
          Logout
        </button>
        @else
        <button data-modal="login" id="login-btn" name="login-btn" class="btn btn-primary btn-modal">
          <span class="glyphicon glyphicon-user"></span> 
          Login
        </button>
        @endif
      </div>
      <form class="navbar-form navbar-right" role="search">
        <div class="form-group">
          <input type="search" 
            id="search_input" 
            class="form-control search" 
            placeholder="Search for a BOM">
        </div>
      </form>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

@yield('content')

{{-- Login Modal --}}
{!! HTML::modal('login', '/auth/login', 
  array(
    array(
      'type'  => 'email',
      'label' => 'E-mail',
      'name'  => 'email',
      'id'    => 'email',
      'placeholder' => 'user@level3.com'
    ),
    array(
      'type'  => 'password',
      'label' => 'Password',
      'name'  => 'password',
      'id'    => 'password',
      'placeholder' => '**'
    )
  )
)
!!}

{{-- Add BOM Modal --}}
{!! HTML::modal('bom', '/bom', 
  array(
    array(
      'type'  => 'text',
      'label' => 'BOM Name',
      'name'  => 'Bom',
      'id'    => 'bom_name',
      'placeholder' => 'BB2 CSW-GE'
    ),
    array(
      'type'  => 'text',
      'label' => 'Description',
      'name'  => 'Desc',
      'id'    => 'bom_desc',
      'placeholder' => 'Short description ...'
    )
  )
)
!!}

</body>

<footer class="footer">
  <div class="container text-center">
    <p class="text-muted">
      Need help with this site?
      <a href='mailto:DL-CoreSystemsAndTools@Level3.com?Subject=BOM Builder 2.0 Help'>E-mail</a>
    </p>
    <p>
      Download the source code for this site.
      <a href='{{asset('bom_builder_2.0.tgz')}}'>Source Code</a>
    </p>
    
  </div>
</footer>
<!-- Jquery Devbridge Autocomplete -->
{!! HTML::script('js/jquery-devbridgeAutocomplete.min.js') !!}
@if( Auth::check() )
{!! HTML::script('js/bom-loggedin.js') !!}
@endif

@yield('footer')
</html>
