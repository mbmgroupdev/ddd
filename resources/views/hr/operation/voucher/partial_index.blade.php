@extends('hr.layout')
@section('title', 'Partial Salary')

@section('main-content')
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
                <li class="active">Partial Salary</li>
            </ul>
        </div>
        @include('inc.message')
        <div class="panel"> 
            <div class="panel-body">
                <form id="approval-form" action=""  class="needs-validation " novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group has-float-label  select-search-group has-required">
                                {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates employee ','style', 'required'=>'required']) }}
                                <label  for="associate"> Associate's ID </label>
                            </div> 
                        </div>
                        <div class="col-sm-3">
                            
                            <div class="form-group has-required has-float-label">
                                <input type="date" name="slary_date" class="form-control" id="slary_date" value="{{date('Y-m-d')}}" required>
                                <label for="slary_date">Salary Last Date</label>
                                
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button type="submit" id="generate" class="btn btn-primary">Generate</button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
        <div class="panel"> 
            <div class="panel-body">
                <div id="voucher">
                    
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">

    $("#approval-form").submit(function(e){
        e.preventDefault();
        var curMeInputs = $(this).find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
            isValid = true;
        for (var i = 0; i < curMeInputs.length; i++) {
           if (!curMeInputs[i].validity.valid) {
              isValid = false;
           }
        }
        console.log($('#associate').val());
        if(!$('#associate').val()){
            isValid = false;
        }
        if(isValid){
            $('.app-loader').show();
            var data = $(this).serialize(); 
            $.ajax({
                type: "POST",
                url: '{{ url("hr/operation/partial-salary") }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: data, 
                success: function(response)
                {
                    $('#voucher').html(response.view);
                    $('.app-loader').hide();
                }
            });
        }else{
            $.notify('Please fill all required fields!','error');
        }
    });
</script>
@endpush
@endsection