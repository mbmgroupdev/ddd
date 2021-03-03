@extends('merch.layout')
@section('title', 'Size Group')
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
@section('main-content')
<div class="main-content">

  @php
    if(Request()->buyer == ''){
      $buyerId = '';
    }else{
      $buyerId = Request()->buyer;
    }
  @endphp
  @php
    if(Request()->p_type == ''){
      $ptype = '';
    }else{
      $ptype = Request()->p_type;
    }
  @endphp
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
          <li class="active"> Size Group </li>
      </ul><!-- /.breadcrumb --> 
    </div>
    
    <div class="page-content">
      <div class="row">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="col-sm-12 col-xs-12 no-padding">
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h6>Size Group Add</h6>
                </div>
                <div class="panel-body">
                  @include('inc/message')
                  <div class="row">
                    <div class="offset-sm-2 col-sm-8">
                      <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/productsizestore')}}" enctype="multipart/form-data">
                          {{ csrf_field() }}
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('buyer', $buyer, $buyerId, ['placeholder'=>'Select Buyer','id'=>'buyer','class'=> 'form-control', 'required']) }}
                                <label for="buyer"> Buyer Name  </label>
                              </div>
                              <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('brand', [], null, ['placeholder'=>'Select Brand','id'=>'brand','class'=> 'form-control', 'required']) }}
                                <label for="brand"> Brand Name  </label>
                              </div>
                              
                              <div class="form-group has-required has-float-label select-search-group">
                                <select name="gender" class="form-control" id="gender" required>
                                  <option>Select</option>
                                  <option value="Men's">Men's</option>
                                  <option value="Ladies">Ladies</option>
                                  <option value="Boys/Girls">Boys/Girls</option>
                                  <option value="Girls">Girls</option>
                                  <option value="Women's">Women's</option>
                                  <option value="Men's & Ladies">Men's & Ladies</option>
                                  <option value="Baby Boys/Girls">Baby Boys/Girls</option>
                                </select>
                                <label for="gender"> Gender  </label>
                              </div>
                              <div class="form-group">
                                  <button class="btn btn-outline-success" type="submit">
                                      <i class="fa fa-save"></i> Save
                                  </button>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('product_type', $productType, $ptype, ['placeholder'=>'Select Product Type','id'=>'product_type','class'=> 'form-control', 'required']) }}
                                <label for="product_type"> Product Type  </label>
                              </div>
                              <div class="form-group has-required has-float-label">
                                <input type="text" id="sg_name" name="sg_name" placeholder="Enter Size Group Name " class="form-control" autocomplete="off" />
                                <label for="sg_name" > Size Group Name </label>
                              </div>

                              <div class="form-group has-required has-float-label">
                                <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#sizeModal" style="width: 100%">Select Size Group</button>
                                {{-- <label class="col-12 control-label no-padding-right" for="sino" >Size Group<span style="color: red">&#42;</span></label> --}}
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div id="show_selected_sizes" style="padding-top: 10px; margin: 0px; padding-left: 0px; padding-right: 0px;">
                                  </div>
                          </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-12 col-xs-12 no-padding">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h6>Product Size List</h6>
                </div>
                <div class="panel-body">
                  <table id="dataTables" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th>SL</th>
                        <th width="10%">Buyer name</th>
                        <th width="10%">Size Group</th>
                        <th width="10%">Product Type </th>
                        <th>Gender</th>
                        <th>Sizes</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $i=0; @endphp
                      @foreach($Prodsizegroup as $prod)
                      @can("mr_setup")
                     <tr>
                      <td>{{ ++$i }}</td>
                      <td>{!! $prod->buyer['b_name'] !!}</td>
                      <td>{!! $prod->size_grp_name !!}</td>
                      <td>{{ $prod->size_grp_product_type }}</td>
                      <td>{{ $prod->size_grp_gender }}</td>
                      <td>
                        @php
                          $sizes = $getProductSize[$prod->id]??[];
                        @endphp
                        {{ implode(', ', $sizes) }}

                      </td>

                      <td>
                        <div class="btn-group">

                          <a type="button" href="{{ url('merch/setup/productsizedit/'.$prod->id) }}"class='btn btn-sm btn-success' disabled><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                          <a href="{{ url('merch/setup/productsizedelete/'.$prod->id) }}" type="button" class='btn btn-sm btn-danger bigger-120' onclick="return confirm('Are you sure you want to delete this Product Size?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                      </td>
                    </tr>
                    @endcan
                    @endforeach
                  </tbody>
                </table>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div><!--- /. Row Form 1---->



    </div><!-- /.page-content -->
  </div>
</div>


<!-- Select Size Items  -->
<div class="modal fade" id="sizeModal" tabindex="-1" role="dialog" aria-labelledby="sizeLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-body" style="">
        @foreach($sizeModalData AS $modalData)
        {!! $modalData !!}
        @endforeach
      </div>
      <div class="modal-footer" style="background-color:#fff;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="sizeModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>
@push('js')
<script type="text/javascript">
  $(document).ready(function(){
    ///Data TAble Color
    $('#dataTables').DataTable({
      responsive: true,
      dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
    });

    //Show Selected sizes from modal
    var modal = $("#sizeModal");
    $("body").on("click", "#sizeModalDone", function(e) {

      var data="";
        //-------- modal actions ------------------
        modal.find('.modal-body input[type=checkbox]').each(function(i,v) {
          if ($(this).prop("checked") == true)
          {
            console.log($(this).next().text());
            data+= '<button type="button" class="btn btn-sm btn-success" style="margin:2px; padding:2px;">'+$(this).next().text()+'</button>';
            data+= '<input type="hidden" name="seleted_sizes[]" value="'+$(this).next().text()+'"></input>';

          }
        });
        modal.modal('hide');
        $("#show_selected_sizes").html(data);
      });



/// Generate TNA

@if(!empty($buyerId))
  $.ajax({
    url : "{{ url('merch/setup/productsize_brand_generate') }}",
    type: 'get',
    data: {
      b_id: {{$buyerId}},

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
@endif

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

@endpush
@endsection