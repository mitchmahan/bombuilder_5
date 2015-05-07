@extends('generic_table')
@section('table')
{!! HTML::table(
    'bom',
    'data-table table table-condensed table-hover table-bordered text-center',
    array(
      'BOM'         => 'bom_name',
      'Description' => 'bom_desc',
      'Created'     => 'created_at',
      'Updated'     => 'updated_at'
    ),
    $data,
    'Bom',
    true
  )
!!}
@stop
