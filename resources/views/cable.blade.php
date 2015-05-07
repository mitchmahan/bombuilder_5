@extends('main')
@section('content')
<div class='container vertical-align'>
  <div class='row'>
  <!-- Part Information -->
  <div class='row'>
    <div class='col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='partId'>Type</label>
          <div class='col-sm-8'>
            <a  class='form-control edit'
                    data-url="/cable/{{$cable->cable_id}}" 
                    data-name='type' 
                    data-pk='{{$cable->cable_id}}'
            >
              {{{$cable->type}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Bandwidth</label>
          <div class='col-sm-8'>
            <a class='form-control edit'
                      data-url="/cable/{{$cable->cable_id}}"
                      data-name='bandwidth'
                      data-pk='{{$cable->cable_id}}'
             >{{{$cable->bandwidth}}}
            </a>
          </div>
        </div>
    
        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Max Length</label>
          <div class='col-sm-8'>
            <a class='form-control edit'
                      data-url="/cable/{{$cable->cable_id}}"
                      data-name='maxlength'
                      data-pk='{{$cable->cable_id}}'
             >{{{$cable->maxlength}}}
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>
  <div class='row'>
    <div class="panel panel-default">
      <div class="panel-heading">
        Cableruns
      </div>
      {!! HTML::table('cableRuns',
          'table table-bordered table-hover text-center',
          array(
            'Run Name'    => 'run_name',
            'NE ID'       => 'ne_id',
            'Description' => 'notes',
          ),
          $cable->cableruns,
          'Cablerun'
        )
      !!}
    </div>
  </div>
</div>

@stop
