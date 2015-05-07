@extends('generic_table')
@section('addMenu')
<li><a class="btn-modal" data-modal="part" href="#">Add Part</a></li>
@stop

@section('table')
{!! HTML::table(
   'fullPartsTable',
   'data-table table table-condensed table-hover table-bordered text-center',
   array(
     'Part'        => 'part_name',
     'Vendor'      => array('vendor', 'vendor_name'),
     'Description' => 'descr',
     'Item Master' => 'level3_pn',
     'Price'       => 'price'
   ),
   $data,
   'Part',
   true)
!!}

{{-- Add Part Modal --}}
{!! HTML::modal('part', '/part', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Part ID',
      'name'  => 'PartName',
      'id'    => 'partName',
      'placeholder' => 'EX4500-24T, ...'
    ),
    array(
      'type'  => 'text',
      'label' => 'Vendor',
      'name'  => 'VendorSearch',
      'id'    => 'vendorSearch',
      'placeholder' => 'Search'
    ),
    array(
      'type'  => 'text',
      'label' => 'Description',
      'name'  => 'Descr',
      'id'    => 'descr',
      'placeholder' => 'Juniper EX4500, 24-Port Chassis'
    ),
    array(
      'type'  => 'text',
      'label' => 'Item Master',
      'name'  => 'ItemMaster',
      'id'    => 'itemMaster',
      'placeholder' => '317228'
    ),
    array(
      'type'  => 'text',
      'label' => 'Price',
      'name'  => 'Price',
      'id'    => 'price',
      'placeholder' => '0.00'
    ),
    array(
      'type'  => 'number',
      'label' => 'RMU',
      'name'  => 'Rmu',
      'id'    => 'rmu',
      'placeholder' => '0'
    ),
    array(
      'type'  => 'text',
      'label' => 'Power',
      'name'  => 'Power',
      'id'    => 'power',
      'placeholder' => '14 AMP'
    ),
  )
)
!!}
@stop

@section('footer')
<!-- Data Tables JS and CSS -->
{!! HTML::style('css/dataTables.bootstrap.css') !!}
{!! HTML::script('js/jquery-dataTables.min.js') !!}
{!! HTML::script('js/dataTables.bootstrap.js') !!}
{!! HTML::script('js/bom-views.parts.js') !!}
@stop
