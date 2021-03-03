@extends('merch.index')
@section('content')
<div class="main-content">
  @push('css')
    <style>
      fieldset.group  {
        margin: 0;
        padding: 0;
        margin-bottom: 1.25em;
        padding: .125em;
        border-bottom: 1px solid lightgray;
        border-right: 1px solid lightgray;
        border-top: 1px solid lightgray;
      }

      fieldset.group legend {
        margin: 0;
        padding: 0;
        font-weight: bold;
        margin-left: 20px;
        color: black;
        text-align: center;
        margin-bottom: 15px;
        padding-bottom: 8px;
      }


      ul.checkbox  {
        margin: 0;
        padding: 0;
        margin-left: 20px;
        list-style: none;
      }

      ul.checkbox li input {
        margin-right: .25em;
      }

      ul.checkbox li {
        border: 1px transparent solid;
      }

      ul.checkbox li:hover,
      ul.checkbox li.focus  {
        background-color: lightyellow;
        border: 1px gray solid;
      }
      .checkbox label, .radio label {
        padding-left: 0px;
        font-size: 10px;
    }
    </style>
  @endpush
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

                <li class="active">Size Group Update</li>
            </ul><!-- /.breadcrumb -->
        </div>


        <div class="page-content">

          <!---Form 1---------------------->
            <div class="row">
                  <!-- Display Erro/Success Message -->

                <div class="col-sm-8 col-sm-offset-2">
                  @include('inc/message')
                  <div class="panel panel-success">
                    <div class="panel-heading">
                      <h6>Size Group Edit <a class="pull-right healine-panel" href="{{ url('merch/setup/productsize') }}" rel="tooltip" data-tooltip="Product Size List/Create" data-tooltip-location="top"><i class="fa fa-list"></i></a></h6>
                    </div>
                    <div class="panel-body">
                      <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/productsizeupdate')}}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                              <label class="col-sm-3 control-label no-padding-right" for="product_size_group" >Buyer<span style="color: red">&#42;</span> </label>

                              <div class="col-sm-9">
                                  <div class="" >
                                     {{ Form::select('buyer', $buyer, $Prodsizegroup_up->b_id, ['placeholder'=>'Select Buyer', 'id'=> 'buyer','class'=> 'col-xs-12','data-validation' => 'required']) }}
                                   </div>
                              </div>
                        </div>
                        <div class="form-group">
                              <label class="col-sm-3 control-label no-padding-right" for="product_size_group" >Brand<span style="color: red">&#42;</span> </label>

                              <div class="col-sm-9">
                                  <div class="" >
                                     <select  name='brand' id='brand' class='col-xs-12'data-validation ='required'>
                                        @foreach($brand as $brId=>$brName)
                                          <option value="{{$brId}}" <?php echo $brId==$Prodsizegroup_up->br_id?'selected="selected"':''; ?>>
                                             {{$brName}}
                                          </option>
                                        @endforeach
                                     </select>
                                   </div>
                              </div>
                        </div>

                        <div class="form-group">
                              <label class="col-sm-3 control-label no-padding-right" for="product_type" >Product Type <span style="color: red">&#42;</span> </label>
                              <div class="col-sm-9">

                                  {{ Form::select('product_type', $productType, $product_type_id, ['placeholder'=>'Select Product Type','class'=> 'col-xs-12', 'data-validation' => 'required']) }}
                              </div>
                        </div>

                        <div class="form-group">
                              <label class="col-sm-3 control-label no-padding-right" for="gender" >Gender <span style="color: red">&#42;</span> </label>
                              <div class="col-sm-9">
                                <select name="gender" class="col-xs-12" data-validation='required'>
                                  <option>{{$Prodsizegroup_up->size_grp_gender}}</option>
                                   <option value="Men's">Men's</option>
                                   <option value="Ladies">Ladies</option>
                                   <option value="Boys/Girls">Boys/Girls</option>
                                   <option value="Girls">Girls</option>
                                   <option value="Women's">Women's</option>
                                   <option value="Men's & Ladies">Men's & Ladies</option>
                                   <option value="Baby Boys/Girls">Baby Boys/Girls</option>
                                  </select>
                              </div>
                        </div>
                        <div class="form-group">
                              <label class="col-sm-3 control-label no-padding-right" for="sg_name" >Size Group Name <span style="color: red">&#42;</span> </label>
                              <div class="col-sm-9">

                                  <input type="text" id="sg_name" name="sg_name" placeholder="Enter Size Group Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-45"  value="{{$Prodsizegroup_up->size_grp_name}}" />

                              </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="sino" >Sizes<span style="color: red">&#42;</span></label>
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-primary btn-xs size-modal" data-toggle="modal" data-target="#sizeModal" style="display: block; width: 100%">Select Size</button>
                                <div class="col-xs-10" id="show_selected_sizes" style="padding-top: 10px; margin: 0px; padding-left: 0px; padding-right: 0px;">
                                    @foreach($sizeGroups as $sz)
                                        <button type="button" class="btn btn-sm" style="margin:2px; padding:2px;">{{$sz['mr_product_pallete_name']}}</button>
                                        <input type="hidden" name="seleted_sizes[]" value="{{$sz['mr_product_pallete_name']}}"></input>
                                    @endforeach
                                </div>
                              </div>
                        </div>

                        <input type="hidden" name="prod_id" value="{{$Prodsizegroup_up->id}}">
                        @include('merch.common.update-btn-section')
                      </form>
                    </div>
                  </div>
                </div>
                <!-- /.col -->

            </div><!--- /. Row Form 1---->



        </div><!-- /.page-content -->
    </div>
</div>

<!-- Select Size Items  -->
<div class="modal fade" id="sizeModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title">Size Group</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
        @foreach($sizeModalData AS $modalData)
        {!! $modalData !!}
        @endforeach
      </div>
      <div class="modal-footer" style="background-color: #fff;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="sizeModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
$(document).ready(function(){

///Data TAble Color
    $('#dataTables').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
    });


    //Show Selected Sizes from Modal
    var modal = $("#sizeModal");
    $("body").on("click", "#sizeModalDone", function(e) {

        var data="";
        //-------- modal actions ------------------
        modal.find('.modal-body input[type=checkbox]').each(function(i,v) {
            if ($(this).prop("checked") == true)
            {
            console.log($(this).next().text());
            data+= '<button type="button" class="btn btn-sm" style="margin:2px; padding:2px;">'+$(this).next().text()+'</button>';
            data+= '<input type="hidden" name="seleted_sizes[]" value="'+$(this).next().text()+'"></input>';

            }
        });
        modal.modal('hide');
        $("#show_selected_sizes").html(data);
    });
///

/// Generate TNA

     var basedon = $("#buyer");
     var action_place=$("#brand");
      basedon.on("change", function(){
        $.ajax({
            url : "{{ url('merch/setup/productsize_brand_generate') }}",
            type: 'get',
            data: {
              b_id: $(this).val(),

            },
            success: function(data)
            {
                action_place.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });

    });
});
</script>

@endsection
