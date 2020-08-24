@extends('hr.layout')
@section('title', 'Yearly Employee Activity')
@section('main-content')
@push('css')
<style>
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
   }
</style>
@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Human Resource</a>
        </li>
        <li>
          <a href="#">Reports</a>
        </li>
        <li class="active"> Yearly Employee Activity</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col">
                <form class="widget-container-col" role="form" id="activityReport" method="get" action="#">
                    <div class="panel">
                        <div class="panel-heading">
                            <h6>Yearly Employee Activity Report</h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="year" class="report_date form-control" id="year" name="year" placeholder="Y" required="required" value="{{ date('Y') }}" autocomplete="off" />
                                        
                                        <label  for="year"> Year </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    <div id="print_pdf" class="custom-control-inline" style="display: none;">
                                        <button type="button" onclick="printMe('result-data')" title="Print" class="btn btn-warning">
                                            <i class="fa fa-print"></i> 
                                        </button> 
                                        
                                        {{-- <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" title="PDF" class="btn btn-danger text-white">
                                            <i class="fa fa-file-pdf-o"> </i> 
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col">
                <div class="result-data" id="result-data"></div>
            </div>
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript">
    function printMe(el){ 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:8px;">');
        myWindow.document.write(document.getElementById(el).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    $('#activityReport').on('submit', function(e) {
        $("#print_pdf").hide();
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
            $('html, body').animate({
                scrollTop: $("#result-data").offset().top
            }, 2000);
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
                        $("#print_pdf").show();
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