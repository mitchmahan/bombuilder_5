@extends('main')
@section('content')
<div class='container vertical-align'>
  <div class='row'>
  <!-- Part Information -->
  <div class='row'>
    <div class='col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='cablerunId'>Name</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/cablerun/{{$cablerun->cablerun_id}}" 
                    data-name='run_name' 
                    data-pk='{{$cablerun->cablerun_id}}'
            >
              {{{$cablerun->run_name}}}
            </a>
          </div>
        </div>
    

        <div class='form-group'>
          <label class='control-label col-sm-2' for='cablerunId'>NE ID</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/cablerun/{{$cablerun->cablerun_id}}" 
                    data-name='ne_id' 
                    data-pk='{{$cablerun->cablerun_id}}'
            >
              {{{$cablerun->ne_id}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>NE Type</label>
          <div class='col-sm-3'>
            <a class='form-control edit'
                      data-url="/cablerun/{{$cablerun->cablerun_id}}"
                      data-name='ne_Type'
                      data-pk='{{$cablerun->cablerun_id}}'
             >{{{$cablerun->ne_type}}}</a>
          </div>

          <label class='control-label col-sm-1' for='desc'>Port</label>
          <div class='col-sm-3'>
            <a class='form-control edit'
                      data-url="/cablerun/{{$cablerun->cablerun_id}}"
                      data-name='ne_port'
                      data-pk='{{$cablerun->cablerun_id}}'
             >{{{$cablerun->ne_port}}}</a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='cablerunId'>NE ID</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/cablerun/{{$cablerun->cablerun_id}}" 
                    data-name='remote_ne_id' 
                    data-pk='{{$cablerun->cablerun_id}}'
            >
              {{{$cablerun->remote_ne_id}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>NE Type</label>
          <div class='col-sm-3'>
            <a class='form-control edit'
                      data-url="/cablerun/{{$cablerun->cablerun_id}}"
                      data-name='remote_ne_Type'
                      data-pk='{{$cablerun->cablerun_id}}'
             >{{{$cablerun->remote_ne_type}}}</a>
          </div>

          <label class='control-label col-sm-1' for='desc'>Port</label>
          <div class='col-sm-3'>
            <a class='form-control edit'
                      data-url="/cablerun/{{$cablerun->cablerun_id}}"
                      data-name='remote_ne_port'
                      data-pk='{{$cablerun->cablerun_id}}'
             >{{{$cablerun->remote_ne_port}}}</a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='cablerunId'>Cable</label>
          <div class='col-sm-8'>
            <a  class='form-control'
                    id='cable'
                    data-url="/cablerun/{{$cablerun->cablerun_id}}" 
                    data-name='cable_id' 
                    data-pk='{{$cablerun->cablerun_id}}'
            >
              {{{$cablerun->cable->type}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Notes</label>
          <div class='col-sm-8'>
            <textarea class='form-control'
                      id='desc'
                      rows=2
                      data-url="/cablerun/{{$cablerun->cablerun_id}}"
                      data-name='notes'
                      data-pk='{{$cablerun->cablerun_id}}'
             >{{{$cablerun->notes}}}
            </textarea>
          </div>
        </div>

      </form>
    </div>
  </div>
  <div class='row'>
    <div class="panel panel-default">
      <div class="panel-heading">
        Used In
      </div>
      {!! HTML::table('cablerunBoms',
          'table table-bordered table-hover text-center',
          array(
            'BOM Name'    => 'bom_name',
            'Description' => 'bom_desc',
          ),
          $cablerun->boms,
          'Bom'
        )
      !!}
    </div>
  </div>
</div>

@stop
