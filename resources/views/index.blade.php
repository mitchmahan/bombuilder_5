@extends('main')
@section('head')
<!-- Gridster -->
{!! HTML::script('js/jquery.gridster.min.js') !!}
{!! HTML::style('css/jquery.gridster.min.css') !!}
{!! HTML::script('js/bom-views.index.js') !!}
@stop

@section('menu')
<li class="dropdown">
  <a href="#" 
  class="dropdown-toggle" 
  data-toggle="dropdown" 
  role="button" 
  aria-expanded="false">
    Sites
    <span class="caret"></span>
  </a>

  <ul class="dropdown-menu" role="menu">
    <li class="dropdown-submenu">
      <a href="#">MOSS</a><span class='caret-right'></span>
        <ul id="moss-sites" class="dropdown-menu">
          <li><a class='network-select' data-network='moss' class=''tabindex="-1" href="#">Add All</a></li>
          @if(isset($site_list['moss']))
          @foreach($site_list['moss'] as $site)
            <li><a class='panel-add' data-id="{{$site['site_id']}}" class=''tabindex="-1" href="#">{{{$site['site_name']}}}</a></li>
          @endforeach
          @endif
        </ul>
    </li>
    <li class="dropdown-submenu">
      <a href="#">CORE</a><span class='caret-right'></span>
        <ul id="core-sites" class="dropdown-menu">
          <li><a class='network-select' data-network='core' tabindex="-1" href="#">Add All</a></li>
          @if(isset($site_list['core']))
          @foreach($site_list['core'] as $site)
            <li><a class='panel-add' data-id="{{$site['site_id']}}" class=''tabindex="-1" href="#">{{{$site['site_name']}}}</a></li>
          @endforeach
          @endif
        </ul>
    </li>
    <li class="dropdown-submenu">
      <a href="#">EDGE</a><span class='caret-right'></span>
        <ul id="moss-sites" class="dropdown-menu">
          <li><a class='network-select' data-network='edge' tabindex="-1" href="#">Add All</a></li>
          @if(isset($site_list['edge']))
          @foreach($site_list['edge'] as $site)
            <li><a class='panel-add' data-id="{{$site['site_id']}}" class=''tabindex="-1" href="#">{{{$site['site_name']}}}</a></li>
          @endforeach
          @endif
        </ul>
    </li>
    <li class="dropdown-submenu">
      <a href="#">METRO</a><span class='caret-right'></span>
        <ul id="metro-sites" class="dropdown-menu">
          <li><a class='network-select' data-network='metro' tabindex="-1" href="#">Add All</a></li>
          @if(isset($site_list['metro']))
          @foreach($site_list['metro'] as $site)
            <li><a class='panel-add' data-id="{{$site['site_id']}}" class=''tabindex="-1" href="#">{{{$site['site_name']}}}</a></li>
          @endforeach
          @endif
        </ul>
    </li>
    <li class="divider"></li>
    <li><a id="delete-widgets" href="#">Reset</a></li>
  </ul>
</li>

@stop
@section('content')
@if(Session::has('error'))
    <div class="alert-box text-center warning">
        <h2>{{ Session::get('error') }}</h2>
    </div>
@endif
<div id="ajaxMessages"></div>
<div id="mainContent" class="container-fluid">

  <div class="gridster">
    <ul></ul>
  </div>

  <div class="row">
    <!-- Recent BOMs -->
    <div class="col-lg-offset-3 col-lg-6 col-md-12 col-sm-12 col-xs-12">
      <ul class="list-group">
        <li class="list-group-item active">
          Recent BOMs
        </li>
      @foreach($boms as $bom)
        <a href="/bom/{{$bom->bom_id}}" class="list-group-item">
        <h4 data-id="{{{$bom->bom_id}}}" class="list-group-item-heading">
          {{{strtoupper($bom->bom_name)}}}
        <span class="badge pull-right">
          {{ $bom->createdAt() }}</span>
        </h4>
        <p class="list-group-item-text">{{{$bom->bom_desc}}}</p>
        </a>
      @endforeach
      </il>
    </div>

    {{-- 
    <!-- Recent Parts -->    
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
      <ul class="list-group">
        <li class="list-group-item active">
          Recent Parts
        </li>
      @foreach($parts as $part)
        <a href="/part/{{$part->part_id}}" class="list-group-item">
        <h4 data-id="{{{$part->part_id}}}" class="list-group-item-heading">
          {{{strtoupper($part->part_name)}}}
        </h4>
        <p class="list-group-item-text">{{{$part->descr}}}</p>
        </a>
      @endforeach
      </il>
    </div>
    -->
    --}}
  </div>
</div>

{{-- Add Panel Modal --}}
{!! HTML::modal('panel', '/panel', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Name',
      'name'  => 'SiteName',
      'id'    => 'SiteName',
      'placeholder' => 'Panel'
    ),
    array(
      'type'  => 'text',
      'label' => 'Regex',
      'name'  => 'PanelRegex',
      'id'    => 'PanelRegex',
      'placeholder' => 'bb2, bb4, tier3, etc.'
    )
  )
)
!!}

<!-- Network Selection Modal -->
<div class="modal fade" id="networkSelection" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title">Select a network</h4>
      </div><!-- /.modal-header -->

      <div class="modal-body text-center">
        <p class='lead'>Welcome to BOM Builder 2.0!</p>
        <p><mark>Please select a network below.</mark>
          <br>This will load your home page with default sites.
          <br>You can customize the sites on your home page by using the Sites menu.</p>
        <div class='col-sm-3'>
          <button class='btn btn-default network-select' data-network='moss'>MOSS</button>
        </div>
        <div class='col-sm-3'>
          <button class='btn btn-default network-select' data-network='core'>CORE</button>
        </div>
        <div class='col-sm-3'>
          <button class='btn btn-default network-select' data-network='edge'>EDGE</button>
        </div>
        <div class='col-sm-3'>
          <button class='btn btn-default network-select' data-network='metro'>METRO</button>
        </div>
        <div class='clearfix'></div>
      </div><!-- /.modal-body -->

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">
          Close
        </button>
      </div><!-- /.modal-footer -->
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@stop
