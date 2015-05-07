@extends('main')
@section('content')
<div class='container vertical-align'>
  <!-- Part Information -->
  <div class='row'>
    <div class='col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='partId'>Name</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/vendor/{{$vendor->vendor_id}}" 
                    data-name='vendor_name' 
                    data-pk='{{$vendor->vendor_id}}'
            >
              {{{$vendor->vendor_name}}}
            </a>
          </div>
        </div>
    
        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Comment</label>
          <div class='col-sm-8'>
            <a class='form-control'
                      id='desc' 
                      data-url="/vendor/{{$vendor->vendor_id}}"
                      data-name='comment'
                      data-pk='{{$vendor->vendor_id}}'
             >{{{$vendor->comment}}}
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>
  <div class='row'>
    <div class="panel panel-default">
      <div class="panel-heading">
        Vendor Parts
      </div>
      {!! HTML::table('vendorParts',
          'table table-bordered table-hover text-center',
          array(
            'Part Name'   => 'part_name',
            'Description' => 'descr',
          ),
          $vendor->parts,
          'Part'
        )
      !!}
    </div>
  </div>
</div>

@stop
