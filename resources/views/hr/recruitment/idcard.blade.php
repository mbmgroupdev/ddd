@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
	@media only screen and (max-width: 771px) {
		
		.id_info{margin-bottom: 10px;}
		.id_card_table{padding-left: 22px; padding-right: 22px;}
	}
</style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">Employer</a>
                </li>
                <li class="active">ID CARD</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Recruitment<small> <i class="ace-icon fa fa-angle-double-right"></i> ID CARD</small></h1>
            </div>

			<div class="row"> 
                {{ Form::open(['url'=>'', 'class'=>'form-horizontal', 'id'=>'IdCard']) }}
				<div class="col-sm-6">
					<div class="row" style="margin:10px 0px">
						<div class="col-sm-6 id_info">
                            {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control filter']) }}  
						</div>
						<div class="col-sm-6">
                            {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'class'=> 'form-control filter']) }}  
						</div>
 					</div>

					<div class="row" style="margin:10px 0px">
						<div class="col-sm-6 id_info">
    						{{ Form::select('floor', [], null, ['placeholder'=>'Select Floor', 'class'=>'form-control filter']) }}  
						</div>
						<div class="col-sm-6">
							{{ Form::select('line', [], null, ['placeholder'=>'Select Line', 'class'=>'form-control filter']) }}   
						</div>  
					</div>

					<div class="row" style="margin:10px 0px">
						<div class="col-sm-6 id_info">
							<input type="text" name="doj_from" id="doj_from"  placeholder="Y-m-d (Date of Joining From)" class="datepicker form-control filter" placeholder="Date of Join From" >
						</div>
						<div class="col-sm-6">
							<input type="text" name="doj_to" id="doj_to" placeholder="Y-m-d (Date of Joining To)" class="datepicker form-control filter" placeholder="Date of Join To" >
						</div>  
					</div>

					<div class="row" style="margin:10px 0px"> 
						<div class="col-sm-6">
    						{{ Form::radio('type', 'en',true, ['id'=>'en']) }}  
							<label for="en">English</label>
    						{{ Form::radio('type', 'bn', false, ['id'=>'bn']) }}
							<label for="bn">Bengali</label>  
						</div>
						<div class="col-sm-6">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-info btn-sm ck" type="button" onclick="scroll_to()">
                                    <i class="ace-icon fa fa-search"></i> Search
                                </button> 
                                &nbsp
								<div id="printBtn" style="display:inline-block;"></div>
                            </div>
						</div>
					</div>
				</div>

				<div class="col-sm-6 id_card_table" style="padding-top: 10px;"> 
                    <table id="AssociateTable" class="table header-fixed table-compact table-bordered">
                        <tehad>
                            <tr>
                                <th><input type="checkbox" id="checkAll"/></th>
                                <th>Associate ID</th>
                                <th>Name</th>
                            </tr>
                            <tr>
                                <th colspan="3" id="user_filter"></th>
                            </tr>
                        </tehad>
                        <tbody id="associateList">
							
                        </tbody>
                    </table>
				</div>
				{{ Form::close() }}

				<div class="col-sm-offset-1 col-sm-10" id="idCardPrint" style="overflow-y: scroll; height:500px; border: 1px solid whitesmoke; " hidden></div> 
				{{-- <div class="col-xs-6" id="idCardPrint"></div>  --}}
			</div><!-- /.row -->
		</div><!-- /.page-content -->
	</div>
</div>          
<script type="text/javascript">
$(document).ready(function(){
	//date validation------------------------------------------

	$('#doj_from').on('dp.change',function(){
        $('#doj_to').val($(this).val());    
    });
    $('#doj_to').on('dp.change',function(){
        var end     = $(this).val();
        var start   = $('#doj_from').val();
        if(start == '' || start == null){
            alert("Please enter From-Date first");

            $('#doj_to').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n From-Date is latest than To-Date");

                $('#doj_to').val('');
            }
        }
    });
    //date validation end------------------------------------------


    $('body').on('click','.ck',function(){
    	  $('#idCardPrint').removeAttr('hidden');	
    	  $('html, body').animate({
          scrollTop: $('#idCardPrint').offset().top
          }, 700);
    });
	//check - uncheck
	$('#checkAll').click(function(){
	   var checked =$(this).prop('checked');
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

    //Filter User
    $("body").on("keyup", "#AssociateSearch", function() {
        var value = $(this).val().toLowerCase(); 
        $("#AssociateTable #associateList tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

	// emp_type 
	var associateList = $("#associateList");
	var user_filter   = $("#user_filter");
	var emp_type = $("select[name=emp_type]");
	var unit     = $("select[name=unit]");
	var floor    = $("select[name=floor]");
	var line     = $("select[name=line]");
	// floor list by unit
	unit.on('change', function(){
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/floor_list_by_unit") }}',
			data: {
				unit: unit.val(),
			},
			success: function(data)
			{
				floor.html(data.floorList);   
				line.html('');   
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Unit Failed');
			}
		});
	});

	// line list by floor & unit id
	floor.on('change', function(){
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/line_list_by_unit_floor") }}',
			data: {
				unit: unit.val(),
				floor: floor.val(),
			},
			success: function(data)
			{
				line.html(data.lineList); 
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Employee Type Failed');
			}
		});
	});

	// find_associate
	$("body").on('change', ".filter", function(){
		$('#idCardPrint').attr('hidden','hidden');
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/filter") }}',
			data: {
				emp_type: $("select[name=emp_type]").val(),
				unit: $("select[name=unit]").val(),
				floor: $("select[name=floor]").val(),
				line: $("select[name=line]").val(), 
				doj_from: $("input[name=doj_from]").val(), 
				doj_to: $("input[name=doj_to]").val() 
			},
			success: function(data)
			{
				associateList.html(data.result); 
				user_filter.html(data.filter); 
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Failed');
			}
		});
	});

	//submit 
	var IdCard = $("#IdCard");
	var idCardPrint = $("#idCardPrint");
	var printBtn = $("#printBtn");
	IdCard.on('submit', function(e){
		e.preventDefault();

    	var formdata = new FormData($(this)[0]);
    	idCardPrint.html('<center><table class"col-sm-12"><thead><th><h4>Please Wait...</th></h4></thead></table></center>');

		$.ajax({
			url  : '{{ url("hr/recruitment/employee/idcard/search") }}',
			type : $(this).attr('method'),
			dataType : 'json',
	        processData: false,
	        contentType: false,
			data : formdata,
			success:function(data)
			{
				printBtn.html(data.printbutton);
				idCardPrint.html(data.idcard); 
			},
			error:function()
			{
				console.log('faild')
			}
		});
	});
});


function printContent(el)
{
	var data = document.getElementById(el).innerHTML;
	var mywindow = window.open('', 'ID CARD', 'height=400,width=600');
	mywindow.document.write('<html><head><title></title>');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.focus();
	mywindow.print();
	mywindow.close();  
}
</script>
@endsection