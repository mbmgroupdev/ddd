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
                  
                <li class="active">Operation Edit</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content"> 
              
          <!---Form -->
            
          <!-- -Form 1---------------------->
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6>Operation Edit
                                     <a class="pull-right healine-panel" href="{{ url('merch/setup/operation') }}" rel="tooltip" data-tooltip="Operation Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                    <!-- Display Erro/Success Message -->
                                    @include('inc/message')
                                    <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/operationupdate')}}" enctype="multipart/form-data">
                                    {{ csrf_field() }} 

                                      <div class="form-group">
                                          <label class="col-sm-3 control-label no-padding-right" for="op_name" >Operation Name <span style="color: red">&#42;</span> </label>

                                            <div class="col-sm-9">
                                               <input type="text"  name="op_name" value="{{ $operation->opr_name }}" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" />
                                            </div>
                                      </div>
                                      {{Form::hidden('op_id', $value=$operation->opr_id)}}
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
});
</script>
@endsection