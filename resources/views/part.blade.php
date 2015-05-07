@extends('main')

@section('head')
  {!! HTML::script('js/bom-views.parts.js') !!}
@stop

@section('addMenu')
<li><a class="btn-modal" data-modal="childPart" href="#">Child</a></li>
<li><a class="btn-modal" data-modal="image" href="#">Image</a></li>
@stop

@section('content')
<div class='container-fluid'>
  <!-- Part Information -->
  <div class='row'>
    <div class='col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='partId'>Part</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/part/{{$part->part_id}}" 
                    data-name='part_name' 
                    data-pk='{{$part->part_id}}'
            >
              {{{$part->part_name}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='partId'>Item Master</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/part/{{$part->part_id}}" 
                    data-name='level3_pn' 
                    data-pk='{{$part->part_id}}'
            >
              {{{$part->level3_pn}}}
            </a>
          </div>
        </div>
    
        <div class='form-group'>
          <label class='control-label col-sm-2' for='regex'>Vendor</label>
          <div class='col-sm-8'>
            <a  class='form-control'
                id="vendor" 
                data-url="/part/{{$part->part_id}}" 
                data-name='vendor_id'
                data-pk='{{$part->part_id}}'
                value='{{{$part->panel_regex}}}'
            >
              {{{$part->vendor->vendor_name}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Description</label>
          <div class='col-sm-8'>
            <textarea class='form-control'
                      id='desc' 
                      rows='2' 
                      data-url="/part/{{$part->part_id}}"
                      data-name='descr'
                      data-pk='{{$part->part_id}}'
             >{{{$part->descr}}}</textarea>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='comment'>Comment</label>
          <div class='col-sm-8'>
            <a class='form-control edit'
                      id='comment'
                      rows='2' 
                      data-url="/part/{{$part->part_id}}"
                      data-name='comment'
                      data-pk='{{$part->part_id}}'
             >{{{$part->comment}}}</a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='rmu'>RMU</label>
          <div class='col-sm-2'>
            <a class='form-control edit'
                      id='rmu'
                      data-url="/part/{{$part->part_id}}"
                      data-name='rmu'
                      data-pk='{{$part->part_id}}'
             >{{{$part->rmu}}}</a>
          </div>
          <label class='control-label col-sm-1' for='power'>Power</label>
          <div class='col-sm-2'>
            <a class='form-control edit'
                      id='power'
                      data-url="/part/{{$part->part_id}}"
                      data-name='power'
                      data-pk='{{$part->part_id}}'
             >{{{$part->power}}}</a>
          </div>
          <label class='control-label col-sm-1' for='price'>Price</label>
          <div class='col-sm-2'>
            <a class='form-control edit'
                      id='price'
                      data-url="/part/{{$part->part_id}}"
                      data-name='price'
                      data-pk='{{$part->part_id}}'
             >{{{$part->price}}}</a>
          </div>
        </div>

      </form>
    </div>
  </div>

  <div class='row-fluid'>
    <div class='col-md-12 col-lg-12 col-sm-12 col-xs-12'>
      <h3>Referenced by</h3>
      {!! HTML::table(
          'parentPart',
          'table table-hover table-bordered text-center',
          array(
            'Part'        => 'part_name',
            'Vendor'      => array('vendor', 'vendor_name'),
            'Description' => 'descr',
            'Item Master' => 'level3_pn',
            'Price'       => 'price',
            'Count'       => array('pivot', 'count')
          ),
          $part->parents,
          'Part')
      !!}
    </div>
    <div class='col-md-12 col-lg-12 col-sm-12 col-xs-12'>
      <h3>Children</h3>
        {!! HTML::table(
            'Part',
            ' table table-hover table-bordered text-center',
            array(
              'Part'        => 'part_name',
              'Vendor'      => array('vendor', 'vendor_name'),
              'Description' => 'descr',
              'Item Master' => 'level3_pn',
              'Price'       => 'price',
              'Count'       => array('pivot', 'count')
            ),
            $part->children,
            'Part',
            true
          )
        !!}
    </div>
    <div class='col-md-6 col-lg-6 col-sm-12 col-xs-12'>
      <h3>Images</h3>
      {!! HTML::urls($part->urls, True) !!}
    </div>
  </div>

  <div class='row-fluid'>
    <div class='col-md-6 col-lg-6 col-sm-12 col-xs-12'>
      <h3>Attached to BOMs</h3>
      {!! HTML::table(
        'Bom',
        'table table-hover table-bordered text-center',
        array(
          'BOM'         => 'bom_name',
        ),
        $part->boms,
        'Bom')
      !!}
    </div>
  </div>
</div>

{!! HTML::modal('childPart', "part/$part->part_id/addChild", 
  array(
    array(
      'type'  => 'text',
      'label' => 'Part ID',
      'name'  => 'partSearch',
      'id'    => 'partSearch',
      'placeholder' => 'Search'
    ),
    array(
      'type'  => 'number',
      'label' => 'Count',
      'name'  => 'PartCount',
      'id'    => 'partCount',
      'placeholder' => '0'
    )
  ))
!!}

{{-- Add Image Modal --}}
{!! HTML::modal('image', "part/$part->part_id/addImage", 
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
@stop

@section('footer')
<!-- Data Tables JS and CSS -->
{!! HTML::style('css/dataTables.bootstrap.css') !!}
{!! HTML::script('js/jquery-dataTables.min.js') !!}
{!! HTML::script('js/dataTables.bootstrap.js') !!}
@stop
