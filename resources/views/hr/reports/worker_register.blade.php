@extends('hr.layout')
@section('title', '')
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
                    <a href="#"> Reports </a>
                </li>
                <li class="active">Worker Register </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content"> 
            <?php $type='worker_register'; ?>
            @include('hr/reports/attendance_radio')
            <div class="page-header">
                <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Worker Register </small></h1>
            </div>
            <div class="row">
                        
                    <div class="col-sm-12">
                        <div class="col-sm-6 no-padding-left">
                            <div class="form-group">
                                <div class="col-sm-6" style="padding-bottom: 10px;">
                                    {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit','id'=>'unitselect','class'=> 'form-control', 'data-validation'=>'required']) }}
                                </div>
                                <div class="col-sm-6">
                                    {{ Form::select('associate', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_list', 'class'=> 'associates_list no-select form-control']) }}
                                </div>
                               
                            </div>
                        </div>

                       <div class="col-sm-4">
                         <div class="form-group">
                            <button type="submit" id="search"class="btn btn-primary btn-sm">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                             <button type="button" onClick="printMe('PrintArea')" class="showprint btn btn-warning btn-sm" style="display: none;" title="Print">
                                   <i class="fa fa-print"></i> 
                            </button>
                              <!--<button type="button" onclick="generate()" class="showprint btn btn-info btn-sm">
                                    <i class="fa fa-file-pdf-o" style="font-size:14px"></i>
                                    </button> -->
                            <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" style="display: none;"><i class="fa fa-file-excel-o" style="font-size:14px;"></i>
                                </button>
                            
                        </div>
                     </div>
                   </div>
                
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12" id="PrintArea">
                    <!-- PAGE CONTENT BEGINS -->
              

                 <!--10 here--->
               <div id="html-2-pdfwrapper">    
                <div  id="form-element">
                <!--Table here---> 
   
           </div>
              </div>   
                <div id="loading" class="col-md-offset-3 text-center col-sm-4" style="margin-top:10%;">
                                 
                   {{-- <i class="fa fa-spinner fa-pulse fa-5x" ></i> --}}
                   <img src="{{URL::asset('assets/rubel/img/loader.gif')}}">

                </div>
                    
                    
                <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div> 
        </div><!-- /.page-content -->
    </div>
</div>
<!--  <script src="{{ asset('assets/js/dist/jspdf.min.js') }}"></script>  -->
 <!--   <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="http://www.jqueryscript.net/demo/jQuery-Plugin-To-Convert-HTML-Table-To-CSV-tabletoCSV/jquery.tabletoCSV.js"></script> -->
<script type="text/javascript">
function printMe(divName)
{ 
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write(document.getElementById(divName).innerHTML); 
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

$(document).ready(function(){ 
   $('.showprint').hide(); //Hide print button
   function formatState (state) {
      //console.log(state.element);
      if (!state.id) {
          return state.text;
      }
      var baseUrl = "/user/pages/images/flags";
      var $state = $(
      '<span><img /> <span></span></span>'
      );
      // Use .text() instead of HTML string concatenation to avoid script injection issues
      var targetName = state.name;
      $state.find("span").text(targetName);
      // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
      return $state;
  };

$('select.associates').select2({
    templateSelection:formatState,
    placeholder: 'Select Associate\'s ID',
    ajax: {
        url: '{{ url("hr/reports/worker_register_employee") }}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return { 
                keyword: params.term,
                unit_id: $("#unitselect").val()
            }; 
        },
        processResults: function (data) { 
            return {
                results:  $.map(data, function (item) {
                    return {
                        text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                        id: item.associate_id,
                        name: item.associate_name
                    }
                }) 
            };
      },
      cache: true
    }
});


    /// Form Based on Emloyee Id
    var basedon = $("#search");  
    var action_element = $("#form-element");

    basedon.on("click", function(){ 

        var un_id = $("#unitselect").val();  
        var assoc_id = $("#associate").val();

      // check if #work-register div already exist then remove 

      if(un_id == "" || assoc_id == "") {

          alert("Please Select Both Unit and Associate ID");
        }
        

     else{

          if($('#work-register').length)   
          {
            $('#work-register').remove(); 
          }
 
      // Worker Register list
        $.ajax({
            url : "{{ url('hr/reports/worker_register_table') }}",
            type: 'get',
            data: {unit_id :un_id, associate_id:assoc_id},
            beforeSend: function(){
                   $('#loading').show();
                  },
            complete: function(){
                    $('#loading').hide();
                   }, 
            success: function(data)
            {
                action_element.html(data);
                $('.showprint').show(); 
            },
            error: function()
            {
                alert('Not Found...');
            }
        });
      }
    });

// excel conversion -->
   $('#excel').click(function(){
    var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
    location.href=url
    return false
      })    

});
  function attLocation(loc){
    window.location = loc;
   }
</script>


@endsection
