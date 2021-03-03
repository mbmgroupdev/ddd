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
                <li class="active"> Product Type </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 

            <div class="row">
                  <!-- Display Erro/Success Message -->
                <div class="col-sm-6 col-sm-offset-3">
                    @include('inc/message')
                    <div class="panel panel-success">
                        <div class="panel-heading">
                          <h6>Product Type Edit <a class="pull-right healine-panel" href="{{ url('merch/setup/product_type') }}" rel="tooltip" data-tooltip="Product Type List/Create" data-tooltip-location="top"><i class="fa fa-list"></i></a></h6>
                        </div>
                        <div class="panel-body">
                            <!-- PAGE CONTENT BEGINS --> 
                            {{ Form::open(["url"=>"merch/setup/product_type_update", "class"=>"form-horizontal"]) }}
                                <input type="hidden" name="prd_type_id" value="{{ $product->prd_type_id }}">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="prd_type_name" > Product Type<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-9">
                                        <input type="text" name="prd_type_name" id="prd_type_name" placeholder="Product Type" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" value="{{ $product->prd_type_name }}" />
                                    </div>
                                </div>  
         
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                <div class="space-4"></div>
                                
                                @include('merch.common.update-btn-section')
                                <!-- /.row --> 
                            </form> 
                            <!-- PAGE CONTENT ENDS -->
                        </div>
                    </div>
                </div> 
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){  
    $('#dataTables').DataTable({
        pagingType: "full_numbers" , 
        "sDom": '<"f"tp>' 
    }); 
});
</script>
@endsection