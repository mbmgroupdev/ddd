@extends('hr.layout')
@section('title', 'Salary Restriction')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="panel">
            <div class="panel-heading">
                <h6>Salary Restriction
                	
                </h6>
            </div>
            <div class="panel-body"> 
                <div class="row justify-content-md-center mb-3">
                	<div class="col-sm-4">
	                    {{ Form::select('associate_id[]', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates form-control', multiple]) }}
                	</div>
                    <div class="col-sm-4">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection