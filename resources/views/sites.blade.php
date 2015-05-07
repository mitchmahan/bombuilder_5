@extends('generic_table')
@section('head')
  {!! HTML::script('js/bom-views.site.js'); !!}
@stop
@section('addMenu')
<li><a class="btn-modal" data-modal="site" href="/site">Site</a></li>
@stop

@section('table')
  @if($data->count() > 0)
      {!! HTML::table('siteTable',
          'data-table table table-condensed table-bordered table-hover text-center',
          array(
            'Name'        => 'site_name',
            'Description' => 'site_desc',
            'Panel Regex' => 'panel_regex',
            'Network'     => 'network',
            'Created'     => 'created_at',
            'Updated'     => 'updated_at'
          ),
          $data,
          'Site',
          true
        )
      !!}
  @else
  <div class='row'>
    <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center'>
    <h3>No sites have been defined yet.</h3>
  </div>
  @endif
</div>

{{-- Add Site Modal --}}
{!! HTML::modal('site', '/site/', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Name',
      'name'  => 'SiteName',
      'id'    => 'siteName',
      'placeholder' => 'BB2 Tier3 ...'
    ),
    array(
      'type'  => 'text',
      'label' => 'Description',
      'name'  => 'SiteDesc',
      'id'    => 'SiteDesc',
      'placeholder' => ''
    ),
    array(
      'type'  => 'text',
      'label' => 'Panel Regex',
      'name'  => 'PanelRegex',
      'id'    => 'PanelRegex',
      'placeholder' => 'bb2, tier3c, etc.'
    ),
    array(
      'type'  => 'text',
      'label' => 'Network',
      'name'  => 'Network',
      'id'    => 'network',
      'placeholder' => 'core, moss, edge'
    )
  )
)
!!}

@stop
