@extends('main')

@section('head')
  {!! HTML::script('js/bom-views.site.js'); !!}
@stop

@section('addMenu')
<li><a class="btn-modal" data-modal="SiteBom" href="#">Bom</a></li>
@stop

@section('content')
<div class='container vertical-align'>
  <div class='row'>
  <!-- Part Information -->
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='partId'>Site Name</label>
          <div class='col-sm-10'>
            <a  class='form-control edit'
                    data-url="/site/{{$site->site_id}}" 
                    data-name='site_name' 
                    data-pk='{{$site->site_id}}'
            >
              {{{$site->site_name}}}
            </a>
          </div>
        </div>
    
        <div class='form-group'>
          <label class='control-label col-sm-2' for='regex'>Regex</label>
          <div class='col-sm-10'>
            <a  class='form-control edit'
                data-url="/site/{{$site->site_id}}" 
                data-name='panel_regex' 
                data-pk='{{$site->site_id}}'
            >
              {{{$site->panel_regex}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='network'>Network</label>
          <div class='col-sm-10'>
            <a  class='form-control edit'
                data-url="/site/{{$site->site_id}}" 
                data-name='network' 
                data-pk='{{$site->site_id}}'
            >
              {{{$site->network}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Description</label>
          <div class='col-sm-10'>
            <textarea class='form-control'
                      id='desc' 
                      rows='2' 
                      data-url="/site/{{$site->site_id}}"
                      data-name='site_desc'
                      data-pk='{{$site->site_id}}'
                      data-type="textarea"
             >{{{$site->site_desc}}}
            </textarea>
          </div>
        </div>

      </form>
    </div>
  </div>
  <div class='row'>
    <div class="panel panel-default">
      <div class="panel-heading">
        BOM List
      </div>
      {!! HTML::table('siteBom',
          'table table-bordered table-hover text-center',
          array(
            'BOM'         => 'bom_name',
            'Description' => 'bom_desc',
            'Created'     => 'created_at',
            'Updated'     => 'updated_at'
          ),
          $site->boms,
          'Bom',
          true
        );
      !!}
    </div>
  </div>
</div>


{{-- Add BOM Modal --}}
{!! HTML::modal('SiteBom', "/site/$site->site_id/addBom", 
  array(
    array(
      'type'  => 'text',
      'label' => 'BOM Name',
      'name'  => 'siteBomSearch',
      'id'    => 'siteBomSearch',
      'placeholder' => 'Search'
    )
  )
)
!!}

@stop
