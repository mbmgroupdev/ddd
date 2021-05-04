@extends('merch.layout')
@section('title', 'Material Color')
@section('main-content')
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
                <li>
                    <a href="#"> Materials </a>
                </li>
                <li class="active"> Color </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
          <div class="col-lg-2 pr-0">
               <!-- include library menu here  -->
               @include('merch.setup.materials')
          </div>
          <div class="col-lg-10 mail-box-detail">
            <div class="row">
              <div class="col-sm-4">
                  
                  <div class="panel panel-info">
                      <div class="panel-heading">
                          <h6>Color</h6>
                      </div>
                      <div class="panel-body">
                          <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/colorstore')}}" enctype="multipart/form-data">
                              {{ csrf_field() }} 
                              
                              <div class="form-group has-required has-float-label">
                                <input type="text" id="march_color" name="march_color" placeholder="Enter Main Reference" class="form-control" autocomplete="off" />
                                <label for="march_color" > Main Reference </label>
                              </div>
                              <div class="form-group has-float-label">
                                <input type="text" id="march_color_code" name="march_color_code" placeholder="Enter Second Reference" class="form-control" autocomplete="off" />
                                <label for="march_color_code" >Second Reference </label>
                              </div>
                              {{-- <div id="addmoreAttach"> 
                                <div class="form-group">
                                  <label class="col-sm-3 control-label no-padding-right" for="march_file" > Attach File</label>
                                      <div class="col-sm-9">                                       
                                        <input type="file" name="march_file[]" class="form-control-file col-xs-6 imgInp" data-validation="mime size" data-validation-allowing="docx,doc,pdf,jpeg,png,jpg" data-validation-max-size="1M"                                    data-validation-error-msg-size="You can not upload images larger than 512kb" data-validation-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file">
                                         <div class="form-group col-xs-6 col-sm-6">
                                          <!--<img class="colorimage" src="#" alt="Color image" name="colorimagefile[]" />-->
                                               <button type="button" class="btn btn-sm btn-success AddBtn">+</button>
                                               <button type="button" class="btn btn-sm btn-danger RemoveBtn" style="width: 33px;">-</button> 
                                         </div> 
                                  </div>
                                </div> 
                              </div>  --}}
                              <div class="form-group">
                                  <button class="btn btn-outline-success" type="submit">
                                      <i class="fa fa-save"></i> Save
                                  </button>
                              </div>                                 
                          </form>  
                      </div>
                  </div>
              </div>
              <div class="col-sm-8">
                  <div class="panel panel-info">
                      <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                          <table id="global-datatable" class="table table-bordered  table-hover">
                              <thead>
                                  <tr>
                                      <th>SL.</th>
                                      <th width="30%">Main Reference</th>
                                      <th width="30%">Second Reference</th>
                                      {{-- <th width="30%">Color Image</th>  --}}
                                      <th width="30%">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @php $i=0; @endphp
                                  @foreach($color as $col)
                                    <tr id="row-{{ $col->clr_id }}">
                                      <td>{{ ++$i }}</td>
                                      <td>{{ $col->clr_name }}</td>
                                      <td>{{ $col->clr_code }}</td>
                                      {{-- <td>
                                        @foreach($col->attached_files AS $file)
                                        <img src="{{url($file->col_attach_url )}}" width="30" height="30">
                                      
                                        @endforeach
                                      </td> --}}

                                      <td width="20%">
                                          <div class="btn-group">
                                              {{-- <a type="button" class='btn btn-sm btn-primary text-white type-update' title="Update" data-producttype="{{ $product->prd_type_name }}" data-id="{{ $product->clr_id }}" id="producttype-click-{{ $product->clr_id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a> --}}
                                              <a href="{{ url('merch/setup/colordelete/'.$col->clr_id) }}" type="button" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure you want to delete this Color?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                          </div>
                                      </td>
                                    </tr>
                                  @endforeach
                              </tbody>
                          </table>

                      </div>
                      <div class="popover bs-popover-left content-section-popover popover-left" id="content-section-popover" role="tooltip" id="popover" x-placement="left" style="display:none;position:absolute;z-index:1; width: 100%;">
                          <div class="arrow" style="top: 37px;"></div>
                          <h3 class="popover-header"><span id="popover-header"></span>  <i class="fa fa-close popover-close"></i></h3>
                          <div class="popover-body">
                              <br>
                              <input type="hidden" id="producttypeid" value="">
                              <div class="form-group has-required has-float-label">
                                <input type="text" id="producttype" name="prd_type_name" placeholder="Enter Color" class="form-control" autocomplete="off"/>
                                <label for="producttype" > Color </label>
                                <div class="msg" style="color: red"></div>
                              </div>
                          </div>
                          <div class="popover-footer">
                              <button type="button" class="btn btn-outline-success btn-sm sample-change-btn" data-status="2" style="font-size: 13px; margin-left: 7px; margin-bottom: 8px;"><i class="fa fa-save"></i> Save</button>
                              <button type="button" class="btn btn-outline-danger btn-sm sample-close-btn" data-status="2" style="font-size: 13px; margin-left: 7px; margin-bottom: 8px;"><i class="fa fa-close"></i> Cancel</button>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.page-content -->
    </div>
</div>

@push('js')
    <script type="text/javascript">
    $(document).ready(function(){
      $('#dataTables').DataTable({
          responsive: true,
          dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
      });
      var data = $("#addmoreAttach").html();
        $('body').on('click', '.AddBtn', function(){
            $("#addmoreAttach").append(data);
        });

        $('body').on('click', '.RemoveBtn', function(){
            $(this).parent().parent().parent().remove();
        });
    });

    $(document).on("click", ".type-update", function(e) {

      var top = e.pageY-165;
      var left = e.pageX-1000;
      var id = $(this).data('id');
      var name = $("#producttype-click-"+id).attr('data-producttype');

      $("#popover-header").html(name);
      $("#producttype").val(name);
      $("#producttypeid").val(id);

      
      // Show context menu
      $("#content-section-popover").toggle(100).css({
         top: top + "px",
         left: left + "px"
      });
      
      return false;
    });
    $(document).on("click", ".popover-close, .sample-close-btn", function(e) {
        $(".content-section-popover").hide();
    });

    $(document).on("click", ".sample-change-btn", function(e) {
        $('.app-loader').show();
        var clr_id = $("#producttypeid").val();
        $.ajax({
          url : "{{ url('merch/setup/product_type_update-ajax') }}",
          type: 'post',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: {
            clr_id: $("#producttypeid").val(),
            prd_type_name: $("#producttype").val(),
          },
          success: function(data)
          {
            $('.app-loader').hide();
            $.notify(data.msg, data.type);
            if(data.type === 'success'){
              $("#producttype-name-"+clr_id).html($("#producttype").val());
              $("#producttype-click-"+clr_id).attr('data-producttype', $("#producttype").val());
              
              setTimeout(function() {
                $("#row-"+clr_id).addClass('highlight');
                $(".content-section-popover").hide();
              }, 200);
            }
          },
          error: function(reject)
          {
             $.notify(reject, 'error');
          }
      });
    });
    $(document).mouseup(function(e) 
    {
        var content = $(".content-section-popover");
        if (!content.is(e.target) && content.has(e.target).length === 0) 
        {
            content.hide();
        }
        
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('.colorimage').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $(".imgInp").change(function(){
        readURL(this);
    });
    </script>
@endpush
@endsection
