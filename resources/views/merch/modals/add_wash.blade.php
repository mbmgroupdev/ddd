<!-- Wash Type Modal-->
<div class="modal fade" id="newWashModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newWashFrm']) }}
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Add New Wash
                </h2>
            </div>
            <div class="modal-body">
                <div class="message"></div>
                <div class="form-group row">
                    <label class="col-sm-3 control-label no-padding-right" for="wash_name" >Wash Name<span style="color: red">&#42;</span> </label>

                    <div class="col-sm-9">
                        <input type="text" name="wash_name" id="wash_name" placeholder="Wash Name"  class="col-xs-12" value="{{ old('wash_name') }}" data-validation="required length custom" data-validation-length="1-45"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="wash_rate" >Rate<span style="color: red">&#42;</span> </label>
                    <div class="col-sm-9">
                        <input type="text" name="wash_rate" id="wash_rate" placeholder="Wash Rate"  class="col-xs-12" value="{{ old('wash_rate') }}" data-validation="required length custom" data-validation-length="1-45"/>
                    </div>
                </div>

                <!-- /.row -->
            </div>
            <div class="modal-footer" style="margin-top: 20px;">
                <div class="col-md-8">
                   <!--<button class="btn btn-info btn-sm" type="submit">
                        <i class="ace-icon fa fa-check bigger-110"></i> ADD
                    </button>
                    <button class="btn btn-sm" type="reset">
                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                    </button> -->
                    <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-info btn-sm wash-add-modal" type="submit" id="wash-add-modal" >
                      DONE
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>