@extends('generic_table')

@section('addMenu')
<li><a class='btn-modal' data-modal='cable' href="#">Cable</a></li>
@stop

@section('table')
{!! HTML::table(
    'cableTable',
    'data-table table table-condensed table-bordered table-hover text-center',
    array(
      'Type'        => 'type',
      'Bandwidth'   => 'bandwidth',
      'Max Length'  => 'maxlength',
    ),
    $data,
    'Cable',
    true
  )
!!}

{{-- Add Cablerun Modal --}}
{!! HTML::modal('cable', '/cable', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Type',
      'name'  => 'CableType',
      'id'    => 'CableType',
      'placeholder' => 'Cat5e'
    ),
    array(
      'type'  => 'text',
      'label' => 'Bandwidth',
      'name'  => 'Bandwidth',
      'id'    => 'Bandwidth',
      'placeholder' => '1000Mbps'
    ),
    array(
      'type'  => 'text',
      'label' => 'Max Length',
      'name'  => 'MaxLength',
      'id'    => 'MaxLength',
      'placeholder' => '100 Meters'
    )
  )
)
!!}
@stop
