@extends('main')
@section('content')
<div class='container vertical-align'>
  <div class='row'>
    <div class='col-lg-8 col-md-8 col-sm-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-2 col-xs-12'>
      <form class='form-horizontal' role='form'>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='partId'>Email</label>
          <div class='col-sm-8'>
            <a  class='form-control'
                    id="email" 
                    data-url="/user/{{$user->id}}" 
                    data-name='email' 
                    data-pk='{{$user->id}}'
            >
              {{{$user->email}}}
            </a>
          </div>
        </div>
    
        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>First Name</label>
          <div class='col-sm-8'>
            <a class='form-control edit'
                      data-url="/user/{{$user->id}}"
                      data-name='first_name'
                      data-pk='{{$user->id}}'
             >{{{$user->first_name}}}
            </a>
          </div>
        </div>

        <div class='form-group'>
          <label class='control-label col-sm-2' for='desc'>Last Name</label>
          <div class='col-sm-8'>
            <a class='form-control edit'
                      data-url="/user/{{$user->id}}"
                      data-name='last_name'
                      data-pk='{{$user->id}}'
             >{{{$user->last_name}}}
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>

@stop
