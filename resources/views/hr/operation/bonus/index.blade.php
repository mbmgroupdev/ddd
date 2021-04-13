@extends('hr.layout')
@section('title', 'Bonus Set')
@section('main-content')
@push('css')
    
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li class="active">Bonus</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <form>
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Bonus</h6></div> 
                    <div class="panel-body">
                        <div class="row">
                            <div class="offset-sm-3 col-sm-6">
        
                                <div class="form-group has-required has-float-label select-search-group">
                                    
                                    {{ Form::select('type_id', $bonusType, null, ['placeholder'=>'Select Bonus', 'id'=>'bonus_for', 'class'=> 'form-control', 'required'=>'required']) }}
                                    <label for="bonus_for">Bonus for </label>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group has-float-label">
                                            <input type="text" name="bonus_amount" id="bonus_amount" placeholder="Enter" class="form-control" >
                                            <label for="bonus_amount">Amount </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group  has-float-label">
                                            <input type="text" name="bonus_percent" id="bonus_percent" placeholder="% of Basic"  class="form-control" >
                                            <label for="bonus_percent">OR, % of Basic </label>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group has-float-label">
                                            <input type="number" name="eligible_month" id="eligible_month" placeholder="Enter Eligible Month" value="" min="0" class="form-control" >
                                            <label for="eligible_month">Eligible Month </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group  has-float-label">
                                            <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" >
                                            <label for="cut_date">Cut of Date </label>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary pull-right" type="submit">
                                        <i class=" fa fa-check"></i> Process
                                    </button>
                                        
                                </div>
                            </div>
                           
                        </div>
                        
                        
                    </div>
                </div>
            </form>
            
        </div> {{-- Page-Content-end --}}
    </div> {{-- Main-content-inner-end --}}
</div> {{-- Main-content --}}
@push('js')
<script type="text/javascript">
    

</script>
@endpush
@endsection