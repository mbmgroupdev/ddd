
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" > MBM Group </a>
            </li>
            @if(isset($request1['buyer']))
                <li>
                    <a href="#" class="search_buyer"> All Buyer </a>
                </li>
                <li>
                     <a href="#" class="search_style" data-buyer="{{ $request1['buyer'] }}">
                        {{ $data['buyerinfo']->b_name }}
                    </a>
                </li>
            @endif
            <li class="active"> Style </li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
                <div class="col-sm-12 worker-list">
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block; overflow-x: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th width="15%">SL.</th>
                                <th width="15%">Style</th>
                                <th width="15%">Type</th>
                                <th width="15%">Buyer</th>
                                <th width="15%">Sewing</th>
                                <th width="15%">Gender</th>
                                <th width="15%">Season</th>
                                <th width="15%">Status</th>
                                <th width="15%">Total Order</th>
                                <th width="15%">FOB </th>
                            </tr>
                        </thead>
                        <tbody>
                           
                    </table>
                </div>

        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
        var searchable = [1,2,3,5];
        var selectable = [4,6]; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {
            '4' :['Male', 'Female'],
            '6' :['Created', 'Approved', 'Pending']
        };

        var dTable =  $('#dataTables').DataTable({
           order: [], //reset auto order
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            ajax: {
                url: '{!! url("merch/query/merch_style_query_liststyle") !!}',
                data: {
                    buyer: '{{ isset($request1['buyer'])?$request1['buyer']:'' }}',
                    ptype: '{{ isset($request1['ptype'])?$request1['ptype']:'' }}',
                    product: '{{ isset($request1['product'])?$request1['product']:'' }}'
                },
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info text-center',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    className: 'btn-sm btn-default print',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: false
                    }

                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'stl_no',  name: 'stl_no' },
                { data: 'stl_type', name: 'stl_type'},
                { data: 'buyer_name',  name: 'buyer_name' },
                { data: 'stl_smv',  name: 'stl_smv' },
                { data: 'gender',  name: 'gender' },
                { data: 'season',  name: 'season' },
                { data: 'stl_status', name: 'stl_status' },
                { data: 'total_order', name: 'total_order' },
                { data: 'fob', name: 'fob'} 

            ],
            initComplete: function () {
                var api =  this.api();

                // Apply the search
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });

                // each column select list
                api.columns(selectable).every( function (i, x) {
                    var column = this;

                    var select = $('<select><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? val : '', true, false ).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                });
            }
        });
        
    });


    
</script>