@extends('hr.layout')
@section('title', 'Bonus Set')
@section('main-content')
@push('css')
    <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        .close-button {
            content: "X";
            background-color: rgb(229 25 4);
            transform: scale(1);
            display: block;
            border-radius: 50%;
            border: 1px solid rgb(8 155 171);
            position: absolute;
            top: -12px;
            right: 0px;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 22px;
            transition-duration: 0.4s;
            color: #fff;
            cursor: pointer;
        }
        /*.form-section{
            height: calc(100vh - 275px);
        }*/
        /*#appendType{
            position: absolute;
            overflow: auto;
            background: #fff;
            height: 300px;
            padding: 15px;
            margin-bottom: 30px; 
        }
        .process-btn{
            position: absolute;
            background: #fff;
            width: 100%;
            bottom: 0;
            padding-top: 15px;
        }*/
    </style>
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
                            <div class="offset-sm-2 col-sm-8">
        
                                <div class="form-section">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('type_id', $bonusType, null, ['placeholder'=>'Select Bonus', 'id'=>'bonus_for', 'class'=> 'form-control', 'required'=>'required']) }}
                                        <label for="bonus_for">Bonus for </label>
                                    </div>
                                    <input type="hidden" name="eligible_month" id="eligible-month" value="0">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group has-float-label">
                                                <input type="text" name="bonus_amount" id="bonus_amount" placeholder="Enter" class="form-control" >
                                                <label for="bonus_amount">Amount </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group  has-float-label">
                                                <input type="text" name="bonus_percent" id="bonus_percent" placeholder="% of Basic"  class="form-control" >
                                                <label for="bonus_percent">OR, % of Basic </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" required>
                                                <label for="cut_date">Cut of Date </label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="number" name="eligible_month" id="eligible_month" placeholder="Enter Number of Eligible Month" value="" min="0" class="form-control" required>
                                                <label for="eligible_month">Eligible Month </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" required>
                                                <label for="cut_date">Cut of Date </label>
                                            </div>
                                        </div>
                                        
                                    </div> --}}
                                    <div class="row">
                                      <div class="col">
                                        <div class="custom-control custom-switch">
                                          <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                          <label class="custom-control-label" for="specialCheck">Advanced</label>
                                        </div>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="specialsection" id="special-section">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="form-group has-required has-float-label select-search-group">
                                                        <select name="" id="type-for" class="form-control">
                                                            <option value=""> - Select - </option>
                                                            <option value="department"> Department</option>
                                                            <option value="designation"> Designation</option>
                                                            <option value="section"> Section</option>
                                                            <option value="sub_section"> Sub Section</option>
                                                            <option value="employee"> Employee</option>
                                                        </select>
                                                        <label for="type-for">Type </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="syncBtn" style="display: none">
                                                    <div class="form-group">
                                                        <button class="btn btn-outline-primary" type="button" id="sync-type">
                                                            <i class="las la-sync"></i>
                                                        </button> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="appendType"></div>
                                        
                                        </div>
                                      </div>
                                    </div>
                                    <div id="targettype"></div>
                                </div>
                                <div class="process-btn">
                                    <div class="form-group">
                                        <button class="btn btn-primary pull-right" type="submit">
                                            <i class=" fa fa-check"></i> Process
                                        </button>
                                            
                                    </div>
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
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/bonus.js')}}"></script>
<script type="text/javascript">
    var bonus_type = @json(bonus_type_by_id());
    $(document).on('change', '#bonus_for', function(event) {
        var bonus_for = $(this).val();
        var eligibleMonth = 0;
        if(bonus_for !== '' && bonus_for !== null){
            if(bonus_type[bonus_for]){
                eligibleMonth = bonus_type[bonus_for].eligible_month;
            }
        }
        $('#eligible-month').val(eligibleMonth);
    });
</script>
@endpush
@endsection