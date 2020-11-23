@extends('hr.layout')
@section('title', 'Eligible')
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
					<a href="#"> Payroll </a>
				</li>
				<li class="active"> Increment </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/payroll/increment-list')}}" class="btn btn-primary pull-right">Eligible List</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>

        @include('inc/message')
        <div class="panel panel-success">
            <div class="panel-body pb-0">
                <form class="" role="form" id="activityReport" method="get" action="#" > 
                    <div class="row">
                      <div class="col-3">
                        <div class="form-group has-float-label has-required select-search-group">
                            <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                <option selected="" value="">Choose...</option>
                                @foreach($unitList as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                          <label for="unit">Unit</label>
                        </div>
                        <div class="form-group has-float-label has-required select-search-group">
                            <select name="area" class="form-control capitalize select-search" id="area" required="">
                                <option selected="" value="">Choose...</option>
                                @foreach($areaList as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <label for="area">Area</label>
                        </div>
                        <div class="form-group has-float-label select-search-group">
                            <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                <option selected="" value="">Choose...</option>
                            </select>
                            <label for="department">Department</label>
                        </div>
                      </div>
                      <div class="col-3">
                        
                        <div class="form-group has-float-label select-search-group">
                            <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                <option selected="" value="">Choose...</option>
                            </select>
                            <label for="section">Section</label>
                        </div>
                        <div class="form-group has-float-label select-search-group">
                            <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                <option selected="" value="">Choose...</option> 
                            </select>
                            <label for="subSection">Sub Section</label>
                        </div>
                        <div class="row">
                          <div class="col-5 pr-0">
                            <div class="form-group has-float-label has-required">
                              <input type="number" class="report_date min_sal form-control" id="min_salary" name="min_salary" placeholder="Min Salary" required="required" value="0" min="0" max="{{$data['salaryMax']}}" autocomplete="off" />
                              <label for="min_salary">Range From</label>
                            </div>
                          </div>
                          <div class="col-1 p-0">
                            <div class="c1DHiF text-center">-</div>
                          </div>
                          <div class="col-6">
                            <div class="form-group has-float-label has-required">
                              <input type="number" class="report_date max_sal form-control" id="max_salary" name="max_salary" placeholder="Max Salary" required="required" value="{{$data['salaryMax']}}" min="{{$data['salaryMin']}}" max="{{$data['salaryMax']}}" autocomplete="off" />
                              <label for="max_salary">Range To</label>
                            </div>
                          </div>
                        </div>
                      </div> 
                      <div class="col-3">
                        <div class="form-group has-float-label select-search-group">
                            <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                <option selected="" value="">Choose...</option>
                            </select>
                            <label for="floor_id">Floor</label>
                        </div>
                        <div class="form-group has-float-label select-search-group">
                            <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                <option selected="" value="">Choose...</option>
                            </select>
                            <label for="line_id">Line</label>
                        </div>
                        
                        
                        
                      </div>  
                      <div class="col-3">
                            <div class="form-group has-float-label has-required">
                              <input type="month" class="report_date datepicker form-control" id="report_from" name="report_from" placeholder="Y-m" required="required" value="{{ date('Y-m') }}" autocomplete="off" />
                              <label for="report_from">Month</label>
                            </div>
                        <div class="form-group has-float-label select-search-group">
                            <select name="as_ot" class="form-control capitalize select-search" id="line_id"  >
                                <option selected="" value="">Choose...</option>
                                <option  value="OT">OT</option>
                                <option  value="NonOT">NonOT</option>
                            </select>
                            <label for="line_id">OT</label>
                        </div>
                        
                        
                        
                        <div class="form-group">
                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" id="attendanceReport"><i class="fa fa-save"></i> Generate</button>
                        </div>
                      </div>
                      
                      
                    </div>
                </form>
                
            </div>
        </div>

		<div class="page-content"> 
            @can('Manage Increment')
            
            @endcan
      
		</div><!-- /.page-content -->
	</div>
</div>
@push('js')
<script type="text/javascript"> 
$(document).ready(function(){
    // change unit
    $('#unit').on("change", function(){
        $.ajax({
            url : "{{ url('hr/attendance/floor_by_unit') }}",
            type: 'get',
            data: {unit : $(this).val()},
            success: function(data)
            {
                $('#floor_id').removeAttr('disabled');
                
                $("#floor_id").html(data);
            },
            error: function(reject)
            {
               console.log(reject);
            }
        });

        //Load Line List By Unit ID
        $.ajax({
           url : "{{ url('hr/reports/line_by_unit') }}",
           type: 'get',
           data: {unit : $(this).val()},
           success: function(data)
           {
                $('#line_id').removeAttr('disabled');
                $("#line_id").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });
    //Load Department List By Area ID
    $('#area').on("change", function(){
        $.ajax({
           url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
           type: 'get',
           data: {area_id : $(this).val()},
           success: function(data)
           {
                $('#department').removeAttr('disabled');
                
                $("#department").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });

    //Load Section List By department ID
    $('#department').on("change", function(){
        $.ajax({
           url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
           type: 'get',
           data: {area_id: $("#area").val(), department_id: $(this).val()},
           success: function(data)
           {
                $('#section').removeAttr('disabled');
                
                $("#section").html(data);
           },
           error: function(reject)
           {
             console.log(reject);
           }
        });
    });
    //Load Sub Section List by Section
    $('#section').on("change", function(){
       $.ajax({
         url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
         type: 'get',
         data: {
           area_id: $("#area").val(),
           department_id: $("#department").val(),
           section_id: $(this).val()
         },
         success: function(data)
         {
            $('#subSection').removeAttr('disabled');
            
            $("#subSection").html(data);
         },
         error: function(reject)
         {
           console.log(reject);
         }
       });
    });



    $('#activityReport').on('submit', function(e) {
          e.preventDefault();
          activityProcess();
    });
    //Show increment list
    $('#increment_list_button').on('click', function(){
        $('#increment_list_div').removeAttr('hidden');
        $('#arear_salary_list_div').attr('hidden','hidden');
        $(this).attr('style','background : linear-gradient(45deg, #8a041a, transparent)!important; border-radius: 5px;');
        $('#arear_salary_list_button').removeAttr('style','background : linear-gradient(45deg, #8a041a, transparent) !important; border-radius: 5px;');

        $('html,body').animate({
            scrollTop: $(".dv").offset().top},
            'slow');
    });
    //Show arear salary list
    $('#arear_salary_list_button').on('click', function(){
        $('#arear_salary_list_div').removeAttr('hidden');
        $('#increment_list_div').attr('hidden','hidden');
        $(this).attr('style','background : linear-gradient(45deg, #8a041a, transparent)!important; border-radius: 5px;');
        $('#increment_list_button').removeAttr('style','background : linear-gradient(45deg, #8a041a, transparent) !important; border-radius: 5px;');
        $('html,body').animate({
            scrollTop: $(".dv").offset().top},
            'slow');
    });
    //Filter User
    $("body").on("keyup", "#AssociateSearch", function() {
        var value = $(this).val().toLowerCase();
        // $('#AssociateTable tr input:checkbox').prop('checked', false);
        $('#AssociateTable tr').removeAttr('class');
        $("#AssociateTable #user_info tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            if($(this).text().toLowerCase().indexOf(value) > -1) {
                $(this).attr('class','add');
                var numberOfChecked = $('#AssociateTable tr.add input:checkbox:checked').length;
                var numberOfCheckBox = $('#AssociateTable tr.add input:checkbox').length;
                if(numberOfChecked == numberOfCheckBox) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            }
        });
    });


    var userInfo = $("#user_info");
    var userFilter = $("#user_filter");
    var emp_type = $("select[name=emp_type]");
    var unit     = $("select[name=unit]");
    var date     = $('input[name=effective_date]'); 
    $(".filter").on('change', function(){ 
        userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color: green;\">Searching Please Wait...</th></tr>');
        $.ajax({
            url: '{{ url("hr/payroll/get_associate") }}',
            data: {
                emp_type: emp_type.val(),
                unit: unit.val(),
                // date: date.val(),
            },
            success: function(data)
            { 
                // console.log(data);
                totalempcount = 0;
                totalemp = 0;
                if(data.result == ""){
                    $('#totalEmp').text('0');
                    $('#selectEmp').text('0');
                    userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th></tr>');    
                }
                else{
                    userInfo.html(data.result);
                    totalemp = data.total;
                    $('#selectEmp').text(totalempcount);
                    $('#totalEmp').text(data.total);
                }
                userFilter.html(data.filter);
            },
            error:function(xhr)
            {
                console.log('Employee Type Failed');
            }
        });
    }); 

    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        var selectemp = 0;
        if(!checked) {
            selectemp = $('#AssociateTable tr.add input:checkbox:checked').length;
            selectemp = totalempcount - selectemp;
            totalempcount = 0;
        } else {
            selectemp = $('#AssociateTable tr.add input:checkbox:not(:checked)').length;
        }
        $('#AssociateTable tr.add input:checkbox').prop('checked', checked);
        totalempcount = totalempcount+selectemp;
        $('#selectEmp').text(totalempcount);
    });

    $('body').on('click', 'input:checkbox', function() {
        if(!this.checked) {
            $('#checkAll').prop('checked', false);
        }
        else {
            var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
            var numTotal = $('input:checkbox:not(#checkAll)').length;
            if(numTotal == numChecked) {
                $('#checkAll').prop('checked', true);
            }
        }
        if($(this).prop('checked')) {
            if(typeof $(this).attr('id') === "undefined"){
                totalempcount += 1;
            }
        } else {
            if(typeof $(this).attr('id') === "undefined"){
                totalempcount -= 1;
            }
        }
        $('#selectEmp').text(totalempcount);
    });

    $('#formSubmit').on("click", function(e){
        var checkedBoxes= [];
        $('input[type="checkbox"]:checked').each(function() {
            if(this.value != "on")
            checkedBoxes.push($(this).val());
        });
    });

    //date range validation
    $('#applied_date, #effective_date').on('dp.change', function(){
        var elligible = $('#applied_date').val();
        var effective = $('#effective_date').val();
        if(elligible != '' && effective != ''){
            if(new Date(elligible) > new Date(effective) ){
                alert('Elligible Date can not be greater than Effective Date');
                $('#applied_date').val($('#effective_date').val());
            }

        }
    });

});
</script>
@endpush
@endsection