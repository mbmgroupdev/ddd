<div class="modal right fade" id="right_modal_lg" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg" >
  <div class="modal-dialog modal-lg right-modal-width" role="document" style="width:500px!important;"> 
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
            <i class="las la-chevron-left"></i> Back
        </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right"><b>Materity Leave Application</b></h5>
        <button type="button" class="f-16" data-dismiss="modal" aria-label="Close" style="border: 0;background: #fff;line-height: 15px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result" id="content-result">
        
            {{Form::open(['url'=>'hr/operation/maternity-leave', 'class'=>'form-horizontal needs-validation', 'novalidate', "enctype" => "multipart/form-data"])}}
                <div class="row">
                    <input type="hidden" name="leave_id" value="{{$leave->id}}">
                    <input type="hidden" name="associate" value="{{$leave->associate_id}}">
                    <div class="col-sm-6">
                        <div class="form-group has-required has-float-label ">
                            <input id="applied_date" type="date" name="applied_date" class="form-control" required placeholder="Enter baby no" value="{{$leave->applied_date}}">
                            <label for="applied_date">Applied Date</label>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group has-float-label ">
                            <input id="edd" type="date" name="edd" class="form-control" required placeholder="Enter EDD" value="{{$leave->edd}}">
                            <label for="applied_date">EDD</label>
                        </div>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group has-required has-float-label ">
                            <input id="no_of_son" type="text" name="no_of_son" class="form-control" required placeholder="Enter no of son" value="{{$leave->no_of_son}}">
                            <label for="no_of_son">Son</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group has-required has-float-label ">
                            <input id="no_of_daughter" type="text" name="no_of_daughter" class="form-control" required placeholder="Enter no of daughter" value="{{$leave->no_of_daughter}}">
                            <label for="no_of_daughter">Daughter</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group has-required has-float-label ">
                            <input id="husband_name" type="text" name="husband_name" class="form-control" required placeholder="Enter husban name" value="{{$leave->husband_name}}">
                            <label for="husband_name">Husband Name</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group has-required has-float-label ">
                            <input id="husband_occupasion" type="text" name="husband_occupasion" class="form-control" required placeholder="Enter husband occupation" value="{{$leave->husband_occupasion}}">
                            <label for="husband_occupasion">Occupation</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group has-float-label ">
                            <input id="husband_age" type="text" name="husband_age" class="form-control"  placeholder="Enter husband age" value="{{$leave->husband_age}}" >
                            <label for="husband_age">Age</label>
                        </div>
                    </div>
                </div>    
                <div class="row">
                    <div class="col-12">
                        <div class="form-group  has-float-label">
                            <input id="usg_report" type="file"  name="usg_report"  >
                            <label for="usg_report" >USG Report</label><br>
                        </div>
                    </div>
                    @if($leave->usg_report)
                        <a href="{{ asset($leave->usg_report) }}" style="vertical-align: text-bottom;">view USG report</a>
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" >Update</button>
                    </div>
                </div>
                
            {{Form::close()}}
       
        </div>
    </div>
</div>
{{-- @push('js')
<script type="text/javascript">
    $(document).on('click','.edit_',function(){
        $('#right_modal_lg').modal('show');

    });
</script>
@endpush --}}