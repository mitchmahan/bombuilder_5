@extends('generic_table')

@section('addMenu')
<li><a class="btn-modal" data-modal="cablerun" href="#">Cablerun</a></li>
@stop

@section('table')
{!! HTML::table('cableTable',
      'data-table table table-condensed table-hover table-bordered text-center',
      array(
        'Name'        => 'run_name',
        'NE ID'       => 'ne_id',
        'NE Type'     => 'ne_type',
        'Port'        => 'ne_port',
        'NE Id'       => 'remote_ne_id',
        'NE TYPE'     => 'remote_ne_type',
        'PORT'        => 'remote_ne_port',
      ),
      $data,
      'Cablerun',
      true)
!!}

{{-- Add Cablerun Modal --}}
{!! HTML::modal('cablerun', '/cablerun', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Name',
      'name'  => 'RunName',
      'id'    => 'RunName',
      'placeholder' => 'Juniper Mgmt to NA'
    ),
    array(
      'type'  => 'text',
      'label' => 'Local NE ID',
      'name'  => 'NeId',
      'id'    => 'NeId',
      'placeholder' => 'device.city'
    ),
    array(
      'type'  => 'text',
      'label' => 'Type',
      'name'  => 'NeType',
      'id'    => 'NeType',
      'placeholder' => 'router, transport'
    ),
    array(
      'type'  => 'text',
      'label' => 'Port',
      'name'  => 'NePort',
      'id'    => 'NePort',
      'placeholder' => 'Fa0/0, Mgmt0/0, Async1 ...'
    ),
    array(
      'type'  => 'text',
      'label' => 'Cable',
      'name'  => 'CableSearch',
      'id'    => 'cableSearch',
      'placeholder' => 'Search for cable. Cat5, Fiber ...'
    ),
    array(
      'type'  => 'text',
      'label' => 'Remote NE ID',
      'name'  => 'R_NeId',
      'id'    => 'R_NeId',
      'placeholder' => 'device.city'
    ),
    array(
      'type'  => 'text',
      'label' => 'Type',
      'name'  => 'R_NeType',
      'id'    => 'R_NeType',
      'placeholder' => 'router, transport'
    ),
    array(
      'type'  => 'text',
      'label' => 'Port',
      'name'  => 'R_NePort',
      'id'    => 'R_NePort',
      'placeholder' => 'Fa0/0, Mgmt0/0, Async1 ...'
    ),
    array(
      'type'  => 'text',
      'label' => 'Notes',
      'name'  => 'Notes',
      'id'    => 'Notes',
      'placeholder' => ''
    ),
  )
)
!!}
@stop
