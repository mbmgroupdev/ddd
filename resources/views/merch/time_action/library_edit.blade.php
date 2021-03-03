@extends('merch.index')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Time & Action </a>
                </li>
                  
                <li class="active">Library & TNA Template</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content"> 
          
            <div class="row">
              <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6> Time & Action Library Edit
                                     <a class="pull-right healine-panel" href="{{ url('merch/time_action/library') }}" rel="tooltip" data-tooltip=" Time & Action Library Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                <!-- Display Erro/Success Message -->
                                @include('inc/message')
                                <form class="form-horizontal" role="form" method="post" action="{{ url('merch/time_action/library_update')}}" enctype="multipart/form-data">
                                {{ csrf_field() }} 

                                  <div class="form-group">
                                      <label class="col-sm-3 control-label no-padding-right" for="lib_action" >Action<span style="color: red">&#42;</span> </label>

                                        <div class="col-sm-9">
                                           <input type="text" id="action" name="lib_action" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" value="{{$library->tna_lib_action}}"  />
                                       </div>
                                  </div>
                                  <div class="form-group">
                                      <label class="col-sm-3 control-label no-padding-right" for="tna_code" >TNA Code <span style="color: red">&#42;</span> </label>

                                        <div class="col-sm-9">
                                           <input type="text" id="tna_code" name="tna_code" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" value="{{$library->tna_lib_code}}"  />
                                           
                                        </div> 
                                         <div id="msg" class="col-sm-9 pull-right" style="color: red">
                                         </div>
                                  </div>
                                
                                  <input type="hidden" name="libid"  value="{{$library->id}}" />
                                 
                                  @include('merch.common.update-btn-section')                        
                                </form>
                              </div>
                         </div>
                </div>     
              
            </div><!--- /. Row Form 1---->
        
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

///Data TAble Color        
    $('#dataTables').DataTable({
        responsive: true, 
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
    });

///Is Buyer name sample type exists
  $("#sample_name").keyup(function(){  
  
        var msg = $("#msg");
        var bid = $("#bid").val();

        // Action Element list
        $.ajax({
            url : "{{ url('merch/setup/sampletypecheck') }}",
            type: 'get',
            data: {keyword : $(this).val(),b_id: bid},
            success: function(data)
            {
                if(data==1){ 
                     msg.html("This Sample Type Already exists");
                     $("#sample_name").val("");
                }
               else{ msg.html("");}

            },
            error: function()
            {
                alert('failed...');
            }
        });

    });
/// 
});
</script>
@endsection