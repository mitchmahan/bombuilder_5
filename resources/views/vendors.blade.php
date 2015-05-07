@extends('generic_table')

@section('addMenu')
<li><a class='btn-modal' data-modal='vendor' href="#">Vendor</a></li>
@stop

@section('table')
{!! HTML::table('vendorTable',
      'data-table table table-condensed table-hover table-bordered text-center',
      array(
        'Vendor Name' => 'vendor_name',
        'Comment'    => 'comment'
      ),
      $data,
      'Vendor',
      true
      )
!!}

{!! HTML::modal('vendor', '/vendor', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Name',
      'name'  => 'VendorName',
      'id'    => 'VendorName',
      'placeholder' => 'Cisco'
    ),
    array(
      'type'  => 'text',
      'label' => 'Comment',
      'name'  => 'Comment',
      'id'    => 'Comment',
      'placeholder' => ''
    )
  )
)
!!}
@stop
