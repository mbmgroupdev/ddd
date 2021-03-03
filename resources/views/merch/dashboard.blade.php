@extends('merch.layout')
@section('title', 'Merchandising Dashboard')
@section('main-content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="col-sm-12">
                <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="#">Merchandising</a>
                        </li>
                        
                        <li class="active">Dashboard</li>
                    </ul><!-- /.breadcrumb -->
         
                </div>

                <div class="panel panel-success h-min-400">
                    <div class="panel-body">
                        <div class="">
                            
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
   @push('js')
      
   @endpush 
@endsection