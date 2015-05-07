@extends('generic_table')

@section('addMenu')
<li><a class='btn-modal' data-modal='user' href="#">User</a></li>
@stop

@section('table')
{!! HTML::table('userTable',
      'data-table table table-hover table-bordered text-center',
      array(
        'Email'       => 'email',
        'Created'     => 'created_at',
        'Last Login'  => 'last_login'
      ),
      $data,
      'User',
      true)
!!}

{!! HTML::modal('user', '/user', 
  array(
    array(
      'type'  => 'text',
      'label' => 'Email',
      'name'  => 'email',
      'id'    => 'Email',
      'placeholder' => '@level3.com'
    )
  )
)
!!}
@stop
