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
                
                <li class="active"> PI To File </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">

                  <!-- Display Erro/Success Message -->
                @include('inc/message')


            <div class="row">
                <div class="space-20"></div>
                <form class="form-horizontal" role="form" method="post" action="{{ url('merch/pi_to_file/update')  }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                    <div class="col-sm-6 col-sm-offset-3">
                       <div class="panel panel-success">
                         <div class="panel-heading page-headline-bar"><h5> PI to File</h5> </div>
                            <div class="panel-body">
                                
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-1 control-label no-padding-right" for="cm_file_id" > File No <span style="color: red; vertical-align: top;">&#42;</span></label>
                                    <div class="col-sm-8">
                                        {{ Form::select('cm_file_id', $fileList, null, ['id'=>'cm_file_id', 'placeholder' =>'Select File', 'class' => 'col-xs-12', 'data-validation'=>'required'])}}
                                    </div>
                                </div>

                         
                                <div class="space-30"></div>

                                <div class="row" id="show_hide" hidden="hidden">
                                    <div class="col-sm-12">
                                    {{-- PI box --}}
                                        <div class="col-xs-12 panel panel-default" style="padding-top: 10px; padding-bottom: 10px;">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>PI No</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pi_bom_table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                            <!-- SUBMIT -->
                                    <div class="col-sm-12">
                                        <div class="space-4"></div>
                                        <div class="space-4"></div>
                                        <div class="space-4"></div>
                                        <div class="space-4"></div>
                                        <div class="space-4"></div>
                                        <div class="clearfix form-actions">
                                            <div class="col-md-offset-4 col-md-8">
                                                <button class="btn btn-sm btn-success" type="submit">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                                </button>
                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-sm" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form>

            </div>
                <div class="space-20"></div>
                <!--file to pi list-->
                <div class="col-sm-8 col-sm-offset-2 no-padding">
                    <div class="panel panel-info">
                      <div class="panel-heading">
                        <h6>PI to File Info List</h6>
                      </div>
                      <div class="panel-body">
                        <table id="dataTables" class="table table-striped responsive table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>PI No</th>
                                    <th>File No</th>
                                    <th>Supplier Name</th>
                                    <th>PI Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($tableData as $td)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$td->pi_no}}</td>
                                        <td>{{$td->file_no}}</td>
                                        <td>{{$td->sup_name}}</td>
                                        <td>{{$td->total_pi_qty}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                </div>
            
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){

    //get PI Informations on select payment type
    $("#cm_file_id").on('change',function(){
 //console.log($(this).html());

            var file= $("#cm_file_id").val();
            if(file==""){
                $('#show_hide').attr('hidden','hidden');
                alert("Please Select File");
                $(this).val("");
            }
            else{
                $.ajax({
                    url: '{{ url("merch/pi_to_file/pi_bom_info") }}',
                    data: {file: file},
                    success: function(data)
                    {
                        // console.log(data);
                        $('#show_hide').removeAttr('hidden');
                        $("#pi_bom_table").html(data);
                    },
                    error: function(xhr)
                    {
                        alert('failed');
                    }
                });
            }
    });

    // var checked_id = [];
    // var unchecked_id = [];

    // $('body').on('change','.check_val', function(){
    //         if(this.checked){
    //             // console.log($(this).val());
    //             checked_id.push($(this).val());
    //             if(jQuery.inArray($(this).val(), unchecked_id)){
    //                 unchecked_id.pop($(this).val());
    //             }
    //         }
    //         else{
    //             unchecked_id.push($(this).val());
    //             if(jQuery.inArray($(this).val(), checked_id)){
    //                 checked_id.pop($(this).val());
    //             }
    //         }

    //         console.log("Checked: ",checked_id,"Unchecked: ",unchecked_id );
    // }).change();


    $('#dataTables').DataTable( {
        
    });
});
</script>
@endsection
