@extends('user.layout')
@section('title', 'User Dashboard')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">ESS</a>
                </li>  
                <li class="active">Change Password</li>
            </ul><!-- /.breadcrumb -->

        </div>

        @include('inc/message')
        <div class="panel h-min-400"> 
            <div class="panel-body"> 
                <div class="row justify-content-center">
                    <div class="col-sm-3">
                        {!! Form::open(['url'=>['user/change-password'], 'class'=>'form-horizontal']) !!}
                        <div class="form-group">
                            <label for="password" > Password </label>
                            <input type="password" id="password" name="password" placeholder="Password"  value="{{ old('password') }}" class="form-control" data-validation-length="min6" data-validation="required length"/>
                        </div> 

                        <div class="form-group">
                            <label for="password_confirmation" >Confirm Password </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirm Password" class="form-control" />
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i> Update
                            </button>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
@endsection