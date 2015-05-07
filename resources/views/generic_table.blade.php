@extends('main')

@section('content')
<div class='page-title'>
  <h2 class="lead text-center">{{{strtoupper($page['name'])}}}</h2>
  <div class='container-fluid'>
      @yield('table')
  </div>
</div>
@stop

@section('footer')
<!-- Data Tables JS and CSS -->
{!! HTML::style('css/dataTables.bootstrap.css') !!}
{!! HTML::script('js/jquery-dataTables.min.js') !!}
{!! HTML::script('js/dataTables.bootstrap.js') !!}
{!! HTML::script('js/bom-views.generic_table.js') !!}
@stop
