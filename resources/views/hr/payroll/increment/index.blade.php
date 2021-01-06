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
                    <a href="{{url('hr/payroll/increment-list')}}" class="btn btn-sm btn-primary pull-right">Increment List</a>
                </li>
			</ul><!-- /.breadcrumb --> 
		</div>

        <div class="iq-accordion career-style mat-style  ">
            <div class="iq-card iq-accordion-block mb-3">
               <div class="active-mat clearfix">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;border-radius: 50%;"> Employee Wise </span> </a></div>
                     </div>
                  </div>
               </div>
               <div class="accordion-details">
                  <div class="row1">
                      <div class="col-12">
                          <form class="" role="form" id="employeeWiseSalary">
                              <div class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <div class="form-group has-float-label has-required select-search-group">
                                                    {{ Form::select('as_id[]', [],'', ['id'=>'as_id', 'class'=> 'associates form-control select-search no-select', 'multiple'=>"multiple",'style', 'data-validation'=>'required']) }}
                                                    <label for="as_id">Employees</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <button onclick="individual()" type="button" class="btn btn-primary btn-sm" id="individualBtn"><i class="fa fa-save"></i> Generate</button>
                                                
                                            </div>
                                        </div>
                                    </div>
                              </div>
                          </form>
                      </div>
                  </div>
               </div>
            </div>
            <div class="iq-card iq-accordion-block mb-3  accordion-active">
               <div class="active-mat clearfix">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;border-radius: 50%;"> Unit Wise </span> </a></div>
                     </div>
                  </div>
               </div>
               <div class="accordion-details">
                  <div class="row1">
                    <div class="col-12"> 
                        <div class="panel mb-0">
                            
                            <div class="panel-body pb-0">
                                <form class="" role="form" id="activityReport" method="get" action="#" > 
                                    <div class="row">
                                      <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            @php
                                                $mbmFlag = 0;
                                                $mbmAll = [1,4,5];
                                                $permission = auth()->user()->unit_permissions();
                                                $checkUnit = array_intersect($mbmAll,$permission);
                                                if(count($checkUnit) > 2){
                                                  $mbmFlag = 1;
                                                }
                                            @endphp
                                            <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                                <option selected="" value="">Choose...</option>
                                                @if($mbmFlag == 1)
                                                <option value="145">MBM + MBF + MBM 2</option>
                                                @endif
                                                @foreach($unitList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
                                        </div>
                                        <div class="form-group has-float-label  select-search-group">
                                            <select name="area" class="form-control capitalize select-search" id="area">
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
                                          <div class="col-6 pr-0">
                                            <div class="form-group has-float-label has-required">
                                              <input type="number" class="report_date min_sal form-control" id="min_salary" name="min_salary" placeholder="Min Salary" required="required" value="0" min="0" max="{{$data['salaryMax']}}" autocomplete="off" />
                                              <label for="min_salary">Range From</label>
                                            </div>
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
                                        <div class="form-group has-float-label select-search-group">
                                          {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control']) }} 
                                          <label  for="emp_type">Employee Type </label>
                                      </div>
                                        
                                        
                                        
                                      </div>  
                                      <div class="col-3">
                                            <div class="form-group has-float-label has-required">
                                              <input type="month" class="report_date datepicker form-control" id="month" name="month" placeholder="Y-m" required="required" value="{{ date('Y-m') }}" autocomplete="off" />
                                              <label for="month">Month</label>
                                            </div>
                                        <div class="form-group has-float-label select-search-group">
                                            <select name="as_ot" class="form-control capitalize select-search" id="as_ot"  >
                                                <option selected="" value="">Choose...</option>
                                                <option  value="1">OT</option>
                                                <option  value="0">Non OT</option>
                                            </select>
                                            <label for="as_ot">OT</label>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group">
                                          <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" id="attendanceReport"><i class="fa fa-save"></i> Generate</button>
                                        </div>
                                      </div>
                                      
                                      
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- PAGE CONTENT ENDS -->
                    </div>
                    <!-- /.col -->
                </div>
               </div>
            </div>
            
        </div>

        @include('inc/message')
        

		<div class="page-content"> 
            <div id="increment-data">
                
            </div>
            @can('Manage Increment')
            
            @endcan
      
		</div><!-- /.page-content -->
	</div>
</div>
@include('hr.common.activity_modal')
@push('js')
<script type="text/javascript"> 
    function activityProcess() {
              
        var unit = $('select[name="unit"]').val();
        var location = $('select[name="location"]').val();
        var area = $('select[name="area"]').val();
        var month = $('input[name="date"]').val();
        var format = $('input[name="report_format"]').val();
        if($('select[name="type"]').val() !== undefined){
          var type = $('select[name="type"]').val();
        }else{
          var type = 'running';
        }
        var form = $("#activityReport");
        var flag = 0;

        if(month === ''){
            flag = 1;
            $.notify('Select required field', 'error');
        }

        if(flag === 0){
            var data = form.serialize()+'&type='+type;
            $('.app-loader').show();
            $.ajax({
                type: "GET",
                url: '{{ url("hr/payroll/increment-eligible") }}',
                data: data, 
                success: function(response)
                {
                    if(response !== 'error'){
                        $("#increment-data").html(response);
                        $('html, body').animate({
                            scrollTop: $("#increment-data").offset().top
                        }, 2000);
                    }
                    $('.app-loader').hide();
                },
                error: function (reject) {
                }
            });

        }else{
            $("#result-data").html('');
        }
    }

    function individual() {
       
        $('.app-loader').show();
        $.ajax({
            type: "GET",
            url: '{{ url("hr/payroll/increment-employeewise") }}',
            data: {
                associate_id : $('#as_id').val()
            }, 
            success: function(response)
            {
                if(response !== 'error'){
                    $("#increment-data").html(response);
                    $('html, body').animate({
                        scrollTop: $("#increment-data").offset().top
                    }, 2000);
                }
                $('.app-loader').hide();
            },
            error: function (reject) {
            }
        });

    }


    $.fn.getForm2obj = function() {
      var _ = {};
      $.map(this.serializeArray(), function(n) {
        const keys = n.name.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
        if (keys.length > 1) {
          let tmp = _;
          pop = keys.pop();
          for (let i = 0; i < keys.length, j = keys[i]; i++) {
            tmp[j] = (!tmp[j] ? (pop == '') ? [] : {} : tmp[j]), tmp = tmp[j];
          }
          if (pop == '') tmp = (!Array.isArray(tmp) ? [] : tmp), tmp.push(n.value);
          else tmp[pop] = n.value;
        } else _[keys.pop()] = n.value;
      });
      return _;
    }


    
    $(document).on('submit','#increment-action', function(e) {
        e.preventDefault();
        $('.app-loader').show();
        var data = $('#increment-action').getForm2obj();

        const chunksize = 300;

        const increment = Object.keys(data.increment).reduce((c, k, i) => {
          if (i % chunksize == 0) {
            c.push(Object.fromEntries([[k, data.increment[k]]]));
          } else {
            c[c.length - 1][k] = data.increment[k];
          }
          return c;
        }, []);

        // apply promise for one after one request
        var promises = [];
        for (i = 0; i < increment.length; i++) {

            var request = $.ajax({
                type: "POST",
                url: '{{ url("hr/payroll/increment-action") }}',
                data : {
                    _token : data._token,
                    effective_date : data.effective_date,
                    increment_type : data.increment_type,
                    increment : increment[i],
                },
                success: function(res)
                {
                    if(res.status != 'success'){
                        $.notify(res.msg,'error');
                    }
                    
                },
                error: function (reject) {
                }
            });

            promises.push(request);

        }

        $.when.apply(null, promises).done(function() {
            $("#increment-data").html('');
            $('.app-loader').hide();
            $.notify('Increment saved successfully!','success');
        })
        
    });

    $(document).ready(function(){
        // change unit
        $('#activityReport').on('submit', function(e) {
              e.preventDefault();
              activityProcess();
        });
        $(document).on("change",'#report_type', function(){
            activityProcess();
        });




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
                    if(data.result == ""){
                        $('#totalEmp').text('0');
                        $('#selectEmp').text('0');
                        userInfo.html('<tr><th colspan="3" style=\"text-align: center; font-size: 14px; color:red;\">No Data Found</th></tr>');    
                    }
                    else{
                        userInfo.html(data.result);
                        totalemp = data.total;
                        //$('#selectEmp').text(totalempcount);
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
    });
</script>
<script type="text/javascript">
    function printDiv(divName)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }


    $(document).on('keypress','#AssociateSearch',function(e){
        if (e.keyCode === 13 || e.which === 13) {
            e.preventDefault();
            return false;
        }
    });

   
    

    $("body").on("keyup", "#AssociateSearch", function(e) {
        var v = $(this).val();
        if(v){
            if(e.keyCode == 13){
                $("#increment-table tbody tr").addClass("hide");
                $("tr[class*='"+v+"']").removeClass("hide"); 
                $("#check-all").addClass("hide"); 
            }
        }else{
            $("#increment-table tbody tr").removeClass("hide");
            $("#check-all").removeClass("hide"); 
        }
        
    });



    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });

    $('#checkAll').click(function(){
        var checked =$(this).prop('checked');
        var selectemp = 0;
        if(!checked) {
            selectemp = $('input:checkbox:checked').length;
            
        } else {
            selectemp = $('input:checkbox:not(:checked)').length;
        }
        $('input:checkbox').prop('checked', checked);
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
        
    });

    function checkSingle(as_id)
    {
        if($('#check_'+as_id).is(':checked')){
            $('#inc_'+as_id).data('checked',1);
        }else{
            $('#inc_'+as_id).data('checked',0);
        }
        getSum();
    }

    function calculateInc()
    {
        var per = $('#inc_percent').val(), total = 0, emp = 0;
        
        if(per){
            $('.increment-amount').each(function( index ) {
                if($(this).data('checked') == 1){
                    var t = Math.ceil($(this).data('salary')*(per/100));
                    $(this).val(t);
                    total += t;
                    emp++;
                }
            });
        }else{
            $('.increment-amount').each(function( index ) {
                $(this).val(0);
            });
        }
        $('.total-amount').text(total);
        $('.total-employee').text(emp);
        
    }

    function getSum()
    {
        var total = 0; 
        var emp = 0; 
        $('.increment-amount').each(function( index ) {
            if($(this).val() && $(this).data('checked') == 1){
                total += parseInt($(this).val()); 
                emp++; 
            }
        });

        $('.total-amount').text(total);
        $('.total-employee').text(emp);
    }

    $(document).on('keyup','#inc_percent', function(){
        calculateInc();
    });
     $(document).on('keyup','.increment-amount',function(){
        getSum();
    });

    function checkAllGroup(val){
        var id = '';
        console.log('hi');
      if($(val).is(':checked')){
        $('.checkbox-inc').prop("checked", true);
        $('.increment-amount').data('checked',1);
       
      }else{
        $('.checkbox-inc').prop("checked", false);
        $('.increment-amount').data('checked',0);
      }
      getSum();
    }

    $(document).ready(function() {
        $("input[type=number]").addClass('inputnumber');
        $("input[type=number]").on("focus", function() {
            $(this).on("keydown", function(event) {
                if (event.keyCode === 38 || event.keyCode === 40 || event.keyCode === 69) {
                event.preventDefault();
            }
        });
        $(this).on("mousewheel", function(event) {
            event.preventDefault();
        });
      });
    });
</script>
@endpush
@endsection