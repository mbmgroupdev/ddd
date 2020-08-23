@extends('hr.index')
@push('css')
<style>
   .panel{
    border: 1px solid #ccc;
   }
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
   }
</style>
@endpush
@section('content')
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
            <li class="active"> Yearly Employee Activity Report </li>
         </ul>
         <!-- /.breadcrumb -->
      </div>
      <div class="page-content">
         <div class="page-header">
            <h1>Reports<small> <i class="ace-icon fa fa-angle-double-right"></i> Yearly Employee Activity Report </small></h1>
         </div>
         <form class="widget-container-col" role="form" id="activityReport" method="get" action="#">
            <div class="widget-box ui-sortable-handle">
               <div class="widget-body">
                  <div class="row" style="padding: 10px 20px">
                     <div class="col-sm-6">
                          <div class="form-group row required">
                            <label class="col-sm-3 control-label no-padding-right" for="associates"> Employee ID/Name: </label>
                            <div class="col-sm-9 no-padding-right">
                              {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'data-validation'=>'required']) }}
                            </div>
                          </div>
                      </div>
                      <div class="col-sm-2">
                          <div class="form-group row required" >
                             <label class="col-sm-4 control-label no-padding-right" for="year"> Year: </label>
                             <div class="col-sm-8 no-padding-right" >
                                <input type="text" class="yearpicker form-control" id="year" placeholder="" value="{{ date('Y') }}">
                             </div>
                          </div>
                      </div>
                      <div class="col-sm-offset-1 col-sm-2 text-right">
                         <button type="submit" class="btn btn-primary btn-sm activityReportBtn">
                         <i class="fa fa-search"></i>
                         Search
                         </button>
                      </div>
                  </div>
               </div>
            </div>
         </form>
         <div class="row">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="col-sm-12">
                <div class="result-data" id="result-data"></div>
            </div>
            <!-- /.col -->
         </div>
         <!-- div for summary -->
      </div>
      <!-- /.page-content -->
   </div>
</div>
@push('js')
<script type="text/javascript">
  var loader = '<img src=\'{{ asset("assets/img/loader-box.gif")}}\' class="center-loader">';
  $('#activityReport').on('submit', function(e) {
    $("#result-data").html(loader);
    e.preventDefault();
    var associate = $('select[name="associate"]').val();
    var year = $('input[name="year"]').val();
    var form = $("#activityReport");
    var flag = 0;
    if(associate === '' || year === ''){
      flag = 1;
    }
    if(flag === 0){
      $.ajax({
        url: '/hr/reports/employee-yearly-activity-report',
        type: "GET",
        data: {
            as_id: associate
        },
        success: function(response){
            if(response !== 'error'){
              setTimeout(function(){
                $("#result-data").html(response);
              }, 1000);
            }else{
              console.log(response);
            }
        }
      });
    }else{
      console.log('required');
      $("#result-data").html('');
    }
  });

  $(document).ready(function(){
    function formatState (state) {
     //console.log(state.element);
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span><img /> <span></span></span>'
        );

        var targetName = state.text;
        $state.find("span").text(targetName);
        return $state;
    };
    $('select.associates').select2({
        templateSelection:formatState,
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results:  $.map(data, function (item) {
                        // var oCode = '';
                        // if(item.as_oracle_code !== null){
                        //     oCode = item.as_oracle_code + ' - ';
                        // }
                        return {
                            text: item.associate_name,
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    })
                };
          },
          cache: true
        }
    });
  });
</script>
@endpush
@endsection
