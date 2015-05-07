@extends('main')
@section('head')
  {!! HTML::script('js/bom-views.bom.js') !!}
@stop

@section('addMenu')
<li><a class="btn-modal" data-modal='part' href="#">Part</a></li>
<li><a class="btn-modal" data-modal='cablerun' href="#">Cablerun</a></li>
<li><a class="btn-modal" data-modal='url' href="#">URL</a></li>
<li><a class="btn-modal" data-modal='visio' href="#">Visio</a></li>
<li><a class="btn-modal" data-modal='image' href="#">Image</a></li>
@stop

@section('content')
<!-- BOM TITLE AND TABS -->
<div class='container-fluid'>
  <!-- Part Information -->
  <div class='row'>
    <div class='col-lg-offset-3 col-md-offset-2 col-sm-offset-2 col-lg-6 col-md-8 col-sm-8 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='title'>Name</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                data-url="/bom/{{$bom->bom_id}}" 
                data-name='bom_name' 
                data-pk='{{$bom->bom_id}}'
            >
              {{{$bom->bom_name}}}
            </a>
          </div>
        </div>
    
        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Description</label>
          <div class='col-sm-8'>
            <textarea class='form-control'
                      rows=2
                      id='desc'
                      data-url="/bom/{{$bom->bom_id}}"
                      data-name='bom_desc'
                      data-pk='{{$bom->bom_id}}'
             >{{{$bom->bom_desc}}}</textarea>
          </div>
        </div>

      </form>
    </div>
  </div>

  <div class='row'>
    <nav class='navbar-default'>
    <div class='navbar-header'>
      <button type="button" 
       class="navbar-toggle collapsed" 
       data-toggle="collapse" 
       data-target="#tabs-menu">
       <span class="sr-only">Toggle navigation</span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
      </button>
    </div>
      <ul id='tabs-menu' class="nav navbar-collapse collapse nav-tabs nav-justified">
        <li class="active"><a data-toggle="tab" href="#partsList">Parts List</a></li>
        <li><a data-toggle="tab" href="#rmuPower">RMU/Power</a></li>
        <li><a data-toggle="tab" href="#cableRuns">Cable Runs</a></li>
        <li><a data-toggle="tab" href="#urls">IWO/Config</a></li>
        <li><a data-toggle="tab" href="#partImages">Part Images</a></li>
      </ul>
    </nav>
  </div>
</div>

<div class="tab-content">
  <div id='partsList' class='tab-pane fade in active'>
    <!-- Part List Table -->      
    {!! HTML::parts(array(
                    'class' => 'table table-condensed table-bordered'
                  ), 
                  array(
                    'Vendor'      => array('vendor', 'vendor_name'),
                    'Vendor Part#' => 'part_name',
                    'Item Master' => 'level3_pn',
                    'Description' => 'descr',
                    'Comment'     => 'comment',
                    'Count'       => array('pivot', 'count'),
                    'Price'       => 'price'
                  ),
                  $bom->parts
                  )
  !!}
  </div>

  <!-- RMU and Power -->
  <div id='rmuPower' class='tab-pane fade'>
    <!-- RMU / POWER Table -->      
    {!! HTML::table('rmuTable',
      'table table-condensed table-bordered text-center', 
      array(
        'Part'  => 'part_name',
        'RMU'   => 'rmu',
        'Power' => 'power'
      ),
      $bom->parts,
      'Part')
    !!}
  </div>

  <!-- Cable Runs -->
  <div id='cableRuns' class='tab-pane fade'>
    {!! HTML::table('cableTable',
      'table table-condensed table-bordered text-center',
      array(
        'NE ID'       => 'ne_id',
        'NE Type'     => 'ne_type',
        'Port'        => 'ne_port',
        'Cable Type'  => array('cable', 'type'), 
        'Bandwidth'   => array('cable', 'bandwidth'),
        'NE Id'       => 'remote_ne_id',
        'NE TYPE'     => 'remote_ne_type',
        'PORT'        => 'remote_ne_port',
        'Notes'       => 'notes'
      ), 
      $bom->cableruns, 
      'Cablerun',
      true) 
    !!}
  </div>

  <!-- URLS and BOM aka "IWO" Images -->
  <div id='urls' class='tab-pane fade text-center'>
    <form class="form-inline" role="form">
      <div class='col-sm-4 col-sm-offset-4'>
        <div class="form-group">
          <div class='checkbox'>
            <label for='transuntrust'>
              {!! Form::checkbox('Transuntrust','','', array(
                'id' => 'transuntrust', 
                'type' => 'checkbox'
              )) !!}

              Transuntrust?
            </label>
          </div>
        </div>
      </div>
    </form>
    <div class='clearfix'></div>

    {!! HTML::urls($bom->urls, True) !!}
  </div>

  <!-- PART IMAGES -->
  <div id='partImages' class='tab-pane fade text-center'>
    {!! HTML::partUrls($bom->parts) !!}
  </div>

</div>

{{-- HIDDEN MODALS / FORMS FOR ADDING CONTENT --}}
@if(Auth::check())

{{-- Add Part Modal --}}
{!! HTML::modal('part', "bom/$bom->bom_id/addPart", 
  array(
    array(
      'type'  => 'text',
      'label' => 'Vendor Part ID',
      'name'  => 'partSearch',
      'id'    => 'partSearch',
      'placeholder' => 'Search'
    ),
    array(
      'type'  => 'number',
      'label' => 'Count',
      'name'  => 'Count',
      'id'    => 'count',
      'placeholder' => '0'
    )
  )
)
!!}

{{-- Add Cablerun Modal --}}
{!! HTML::modal('cablerun', "bom/$bom->bom_id/addCablerun", 
  array(
    array(
      'type'  => 'text',
      'label' => 'Cable Run',
      'name'  => 'cablerunSearch',
      'id'    => 'cablerunSearch',
      'placeholder' => 'Search...'
    )
  )
)
!!}

{{-- Add URL Modal --}}
{!! HTML::modal('url', "bom/$bom->bom_id/addUrl", 
  array(
    array(
      'type'   => 'text',
      'label'  => 'URL',
      'name'   => 'Url',
      'id'     => 'Url',
      'placeholder' => 'http://'
    ),
    array(
      'type'   => 'checkbox',
      'label'  => 'Transuntrust?',
      'name'   => 'Transuntrust',
      'id'     => false,
      'placeholder' => false
    )
 )
)
!!}

{{-- Add Visio Modal --}}
{!!
HTML::modal('visio', "bom/$bom->bom_id/addVisio", 
  array(
    array(
      'type'   => 'file',
      'label'  => 'Visio',
      'name'   => 'Visio',
      'id'     => 'Visio',
    ),
    array(
      'type'   => 'file',
      'label'  => 'Visio Image (.png only)',
      'name'   => 'Image',
      'id'     => 'Image',
    )
  )
)
!!}

{{-- Add Image Modal --}}
{!! HTML::modal('image', "bom/$bom->bom_id/addImage", 
  array(
    array(
      'type'   => 'file',
      'label'  => 'Image',
      'name'   => 'Image',
      'id'     => 'Image',
    )
 )
)
!!}
@endif
@stop

@section('footer')
@stop
