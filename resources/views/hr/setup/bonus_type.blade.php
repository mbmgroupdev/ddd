@extends('hr.layout')
@section('title', 'Add Bonus')
@section('main-content')
@push('css')
    <style type="text/css">
        .in_h{
            height: 32px !important;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Settings </a>
                </li>
                <li class="active">Bonus Library</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            

            @include('inc/message')
            <div class="row">
                <div class="col-sm-5">
                    {{Form::open(['url'=>'hr/setup/bonus_type_save', 'class'=>'form-horizontal']) }}
                        <div class="panel panel-info">
                            <div class="panel-heading"><h6>Bonus Library</h6></div> 
                            <div class="panel-body">
                                
                                <div class="form-group has-required has-float-label">
                                </label>
                                    <input type="text" name="bonus_type_name" id="bonus_type_name" class="form-control" required="required" placeholder="Enter Bonus for">
                                    <label for="bonus_type_name">Bonus Title </label>
                                </div>
                                <div class="form-group has-required has-float-label">
                                    <input type="number" name="eligible_month" id="eligible_month" placeholder="Enter Number of  Month" value="" min="0" max="12" class="form-control" required>
                                    <label for="eligible_month">Provison Period (No. of Month)</label>
                                    <p class="text-muted">Employee will eligible for bonus after provision period</p>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">
                                        <i class=" fa fa-check"></i> Save
                                    </button>
                                        
                                </div>
                                
                            </div>
                        </div>
                    {{Form::close()}}
                </div>
                <div class="col-sm-7">
                    
                    <div class="panel panel-info pb-3">
                        <div class="panel-body">
                            <table id="global-datatable" class="table table-striped table-bordered tale-responsive" style="display: block;overflow-x: auto;width: 100%;" >
                                <thead>
                                    <th width="10%">SL.</th>
                                    <th width="20%">Bonus Title</th>
                                    <th>Rules</th>
                                    <th width="20%">Action</th>
                                </thead>
                                <tbody>
                                    @php $i = 0; @endphp
                                    @if($bonus_types)
                                        @foreach($bonus_types as $bt)
                                        
                                            <tr>
                                                <td>{{ ++$i }}</td>
                                                <td>{{$bt->bonus_type_name}}</td>
                                                <td>Provision Period: {{$bt->eligible_month}} Month</td>
                                                
                                                <td>
                                                    <div class="button-group">
                                                        
                                                        <input type="hidden" id="edit_data_id" value="{{$bt->id}}">
                                                        <button class="btn btn-sm btn-success edit_modal_button" data-toggle="modal" data-target="#edit-modal" data-toggle="tooltip" title="Edit" style="padding: 0px 4px 0px 4px;">
                                                            <i class="fa fa-pencil"></i>
                                                            </button>
                                                        <a href="{{url('hr/setup/bonus_type_delete/'.$bt->id)}}" style="padding: 0px 4px 0px 4px;" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this?');"><i class="fa fa-trash" ></i></a>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                    No Data
                                    @endif
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- edit Modal --}}
            <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edit-modal-label">Edit Bonus</h5>
                        </div>
                        <div class="modal-body" id="attachment-body-content">
                            <div class="" style="padding: 4px;">
                            {{Form::open(['url'=>'hr/setup/bonus_type_update', 'class'=>'form-horizontal']) }}
                                <input type="hidden" name="id" id="edit_id">
                                <div class="">
                
                                    
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group has-required has-float-label">
                                            </label>
                                                <input type="text" name="bonus_type_name" id="edit_bonus_type_name" class="form-control" required="required" placeholder="Enter Bonus title">
                                                <label for="edit_bonus_type_name">Bonus Title </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group has-required has-float-label">
                                                <input type="number" name="eligible_month" id="edit_eligible_month" placeholder="Enter Number of Month" value="" min="0" class="form-control" required>
                                                <label for="edit_eligible_month">Provision Period (No. of Month) </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">
                                            <i class=" fa fa-check"></i> Update
                                        </button>
                                            
                                    </div>
                                </div>
                            {{Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div> {{-- Page-Content-end --}}
    </div> {{-- Main-content-inner-end --}}
</div> {{-- Main-content --}}
@push('js')
<script type="text/javascript">
    $(document).ready(function(){
        $('body').on('click', '.edit_modal_button', function(){
            var bt_id = $(this).parent().find('#edit_data_id').val();
            // console.log(bt_id );
            // var months = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
            // var selectedMonthName = months[value['month']];
            $.ajax({
                url: "{{url('hr/setup/bonus_type_edit')}}",
                type: 'GET',
                dataType: 'json',
                data: {bt_id: bt_id},
                success: function(data){
                      $("#edit_id").val(data.id);
                      $("#edit_bonus_type_name").val(data.bonus_type_name);
                      $("#edit_eligible_month").val(data.eligible_month); 

                }
            });     
        });
    });

</script>
@endpush
@endsection