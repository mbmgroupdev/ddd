@extends('hr.layout')
@section('title', 'Assign Training')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li> 
                <li> 
                    <a href="#">Training</a>   
                </li>
                <li class="active">Assign Training</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
                    @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>Assign Training<a href="{{ url('hr/training/assign_list')}}" class="pull-right btn  btn-primary">Assign List</a></h6>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url'=>'hr/training/assign_training', 'class'=>'form-horizontal']) }}
                        <div class=" col-sm-offset-3 col-sm-6">
                                <div class="form-group">
                                    <label for="training_list"> Training <span style="color: red; vertical-align: top;">&#42;</span> </label> <br>
                                    {{ Form::select('tr_as_tr_id', $trainingList, null, ['placeholder'=>'Select Training', 'id'=>'tr_as_tr_id', 'class'=> 'form-control', 'required'=>'required']) }}   
                                </div>

                                <div class="form-group"> 
                                    <label for="tr_as_ass_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label> <br>
                                    {{ Form::select('tr_as_ass_id[]', [], null, ['id'=>'tr_as_ass_id', 'class'=> 'associates form-control', 'required'=>'required','multiple' => 'multiple']) }}
                                    <p class="text-success">
                                        Search by Associate ID or Associate Name! 
                                    </p>
                                </div>

                                <div class="form-group responsive-hundred">
                                    <button class="btn  btn-primary" type="submit">
                                        <i class=" fa fa-check bigger-110"></i> Assign
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection














