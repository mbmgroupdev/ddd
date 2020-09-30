@extends('hr.layout')
@section('title', 'Voucher')

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
                <li class="active">Voucher</li>
            </ul>
        </div>
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
                            <div class="form-group has-float-label has-required select-search-group">
                                @php 
                                    $voucher_type = [
                                        'Maternity Payment' => 'Maternity Payment',
                                        'Partial Salary' => 'Partial Salary',
                                        'Normal Voucher' => 'Normal Voucher'
                                    ];
                                @endphp
                                {{ Form::select('type', $voucher_type, null , ['placeholder'=>'Select type', 'id'=>'type', 'class'=> 'types no-select col-xs-12','style', 'required'=>'required']) }}
                                <label  for="type"> Select Voucher </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="date" name="slary_date" class="form-control" id="slary_date">
                                <label for="slary_date">Salary Last Date</label>
                                
                            </div>
                            
                            <div class="form-group has-required has-float-label">
                                <textarea id="description" class="form-control h-80" name="description" required placeholder="Enter Description"></textarea>
                                <label for="description">Description</label>
                            </div>
                            <div class="form-group">
                                <button type="submit" id="generate" class="btn btn-primary">Generate</button>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            
                            <div class="form-group has-required has-float-label">
                                <input id="amount" class="form-control" name="amount" required placeholder="Enter amount" />
                                <label for="amount">Amount</label>
                            </div>
                            <div class="form-group has-float-label  select-search-group has-required">
                                {{ Form::select('manager', [Request::get('manager') => Request::get('manager')], null, ['placeholder'=>'Select managers ID', 'id'=>'manager', 'class'=> 'associates ','style', 'required'=>'required']) }}
                                <label  for="manager"> Manager's ID </label>
                            </div> 
                            
                        </div>
                        <div class="col-sm-6 text-center">
                            <div class="user-details-block" style="border-left: 1px solid #d1d1d1;">
                                <div class="user-profile text-center mt-0">
                                    <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                </div>
                                <div class="text-center mt-3">
                                 <h4><b id="name">-------------</b></h4>
                                 <p class="mb-0" >
                                    <span id="designation">--------------------------</span> <span id="department"></span></p>
                                 <p class="mb-0" id="unit"> --------------------------</p>
                                 
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </form>

                <div id="voucher">
                    
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
    $('.employee').on('change', function(){
        var emp_id = $(this).val();
        if(emp_id != ""){
            $('.app-loader').show();
            var url = '{{url('')}}';
            $.ajax({
                url : "{{ url('hr/associate') }}",
                type: 'get',
                dataType : 'json',
                data: { 
                    associate_id : emp_id
                },
                success: function(data)
                {
                    $('#name').text(data['as_name']);
                    $('#name').text(data['as_name']);
                    $('#unit').text(data['hr_unit_name']);
                    $('#department').text(','+data['hr_department_name']);
                    $('#designation').text(data['hr_designation_name']);
                    
                    $('#avatar').attr('src', url+data['as_pic']);
                    $('.app-loader').hide();
                },
                error: function(data)
                {
                    $.notify('failed...','error');
                    $('.app-loader').hide();
                }
            });
        }
        else{
            $('#name').text('-------------');
            $('#unit').text('------------------------');
            $('#department').text('------------------------');
            $('#designation').text('------------------------');
            $('#avatar').attr('src','/assets/images/user/09.jpg'); 
            $('.app-loader').hide();
        }

    });

    $("#approval-form").submit(function(e){
        e.preventDefault();
        var curMeInputs = $(this).find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
            isValid = true;
        for (var i = 0; i < curMeInputs.length; i++) {
           if (!curMeInputs[i].validity.valid) {
              isValid = false;
           }
        }
        if(isValid){
            $('.app-loader').show();
            var data = $(this).serialize(); 
            $.ajax({
                type: "POST",
                url: '{{ url("hr/operation/voucher") }}',
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