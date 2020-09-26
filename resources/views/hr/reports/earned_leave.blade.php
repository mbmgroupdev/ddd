@extends('hr.layout')
@section('title', 'Earned Leave Payment')
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
                    <a href="#"> Operations </a>
                </li>
                <li class="active">Earned Leave Payment</li>
            </ul><!-- /.breadcrumb -->
        </div>
        @include('inc/message')
        <div class="panel">
            <div class="panel-heading">
                <h6>Earned Leave Payment</h6>
            </div>
            <div class="panel-body">
                <div class="row justify-content-center">
                    <div class="col-sm-3">
                        <div class="form-group has-required has-float-label select-search-group">
                            
                            {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unitselect','class'=> 'form-control', 'required'=>'required']) }}
                            <label>Unit</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group has-required has-float-label select-search-group">
                            <select name="floor" id="floor" class="form-control" required="required" >
                                <option value="">Select Unit First</option> 
                            </select>
                            <label>Floor</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group has-required has-float-label select-search-group">
                            {{ Form::select('department', $departmentlist, null, ['placeholder'=>'Select Department','id'=>'department','class'=> 'form-control', 'required'=>'required']) }}
                            <label>Department</label>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-3">
                        <div class="form-group has-required has-float-label ">
                            <input type="number" placeholder="YYYY" min="1980" max="{{date('Y')}}" name="fromyear" id="fromyear" class="form-control" required="required"  />
                            <label>Year From</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group has-required has-float-label ">
                            <input type="number" placeholder="YYYY" min="1980" max="{{date('Y')}}" name="toyear" id="toyear" class="form-control" required="required" placeholder="To" /> 
                            <label>Year To</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" id="search"class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i>
                            Search
                        </button>
                     
                        <button type="button" onClick="printMe1('PrintArea')" class="showprint btn btn-warning btn-sm" title="Print">
                       <i class="fa fa-print"></i>
                       </button>
                        <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                       </button>
                             
                    </div>
                       
                </div>
            </div>
        </div>

        <div class="panel" id="PrintArea">
            <div class="panel-body" id="html-2-pdfwrapper"> 
                <div  id="form-element">

                </div> 
            </div> 
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
function printMe1(divName)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<style>.store_button{display:none;}</style>');
    myWindow.document.write(document.getElementById(divName).innerHTML); 
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

$(document).ready(function(){ 
    $('.showprint').hide();
// excel conversion -->
$('#excel').click(function(){

        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
        location.href=url
        return false
    })

   
 // Floor Based On Unit
     var floor_element = $("#floor");
     var basedonunit = $("#unitselect");
     basedonunit.on("change", function(){ 

        // Action Element list
        $.ajax({
            url : "{{ url('hr/reports/earnleavepayment_floor') }}",
            type: 'get',
            data: {un_id : $(this).val()},
            success: function(data)
            {
                floor_element.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });

    });

    //year validation------------------
    $('#fromyear').on('dp.change',function(){
        $('#toyear').val($('#fromyear').val());    
    });

    $('#toyear').on('dp.change',function(){
        var end     = $(this).val();
        var start   = $('#fromyear').val();
        if(start == '' || start == null){
            alert("Please enter From-Year first");
            $('#toyear').val('');
        }
        else{
            if(parseInt(end) < parseInt(start)){
                alert("Invalid!!\n From-Year is latest than To-Year");
                $('#toyear').val('');
            }
        }
    });
    //year validation end---------------

// Form Based on Search Element
    var basedon = $("#search");  
    var action_element = $("#form-element");

    basedon.on("click", function(){ 
        var un_id = $("#unitselect").val();  
        var deprt_id = $("#department").val();
        var floor_id = $("#floor").val();
        var from = $("#fromyear").val();
        var to = $("#toyear").val();

      // check if #work-register div already exist then remove 

        if(un_id == "" || deprt_id == "" || floor_id == "" || from == "" || to == "") {

          alert("Please fill all the fields");
        }

        else{

          if($('#work-register').length)   
          {
            $('#work-register').remove(); 
          }
       
 
      // Worker Register list
        $.ajax({
            url : "{{ url('hr/reports/earnleavepayment_table') }}",
            type: 'get',
            data: {
                unit_id :un_id, 
                dept_id:deprt_id, 
                flr_id:floor_id, 
                fromyr:from, 
                toyear:to
            },
            beforeSend: function(){
               $('.app-loader').show();
            },
            complete: function(){
                $('.app-loader').hide();
            }, 

            success: function(data)
            { 
                $('#wait').show();
                action_element.html(data);
                $('#wait').hide();
                $('.showprint').show(); //show print button
            },
            error: function()
            {
                alert('No Data Found...');
            }
        });
      }
    });


});
</script>
@endpush
@endsection
