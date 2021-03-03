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
                    <a href="#"> Setup </a>
                </li>

                <li class="active">Sample Type Edit</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content">
          <!-- -Form 1---------------------->
            <div class="row">
                  <!-- Display Erro/Success Message -->
                <div class="col-sm-6 col-sm-offset-3">
                @include('inc/message')
                  <div class="panel panel-success">
                    <div class="panel-heading">
                      <h6>Sample Type Edit <a class="pull-right healine-panel" href="{{ url('merch/setup/sampletype') }}" rel="tooltip" data-tooltip="Sample Type List/Create" data-tooltip-location="top"><i class="fa fa-list"></i></a></h6>
                    </div>
                    <div class="panel-body">
                      <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/sampletypeupdate')}}" enctype="multipart/form-data">
                      {{ csrf_field() }}
                      <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="march_color" >Buyer Name<span style="color: red">&#42;</span> </label>
                          <div class="col-sm-9">
                             {{ Form::select('buyer', $buyer, $sample->b_id, ['placeholder'=>'Select Buyer','id'=>'bid','class'=> 'col-xs-12', 'data-validation' => 'required']) }}
                          </div>
                      </div>

                      <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="march_color" >Sample Type <span style="color: red">&#42;</span> </label>

                              <div class="col-sm-9">
                                 <input type="text" id="sample_name" name="sample_name" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" value="{{ $sample->sample_name}}" />
                              </div>
                              <div id="msg" class="col-sm-9 pull-right" style="color: red">
                               </div>
                        </div>
                        {{Form::hidden('sample_id', $value=$sample->sample_id)}}
                        
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
  // $("#sample_name").keyup(function(){

  //       var msg = $("#msg");
  //       var bid = $("#bid").val();

  //       // Action Element list
  //       $.ajax({
  //           url : "{{ url('merch/setup/sampletypecheck') }}",
  //           type: 'get',
  //           data: {keyword : $(this).val(),b_id: bid},
  //           success: function(data)
  //           {
  //               if(data==1){
  //                    msg.html("This Sample Type Already exists");
  //                    $("#sample_name").val("");
  //               }
  //              else{ msg.html("");}

  //           },
  //           error: function()
  //           {
  //               alert('failed...');
  //           }
  //       });

  //   });
///
});
</script>
@endsection
