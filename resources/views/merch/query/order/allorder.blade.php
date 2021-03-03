
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            
            @if(isset($request1['unit']))
                <li>
                    <a href="#" class="search_unit"> All Unit </a>
                </li>
                <li>
                    <a href="#" class="search_buyer" data-unit="{{ $request1['unit'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['buyer']))
                @if(!isset($request1['unit']))
                <li>
                    <a href="#" class="search_buyer"> All Buyer </a>
                </li>
                @endif
                <li>
                     <a href="#" class="search_order" data-buyer="{{ $request1['buyer'] }}">
                        {{ $data['buyerinfo']->b_name }}
                    </a>
                </li>
            @endif
            <li class="active"> Order </li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
                <div class="col-sm-12 worker-list">
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block; overflow-x: auto; white-space: nowrap; width: 100%;">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Order</th>
                                <th>FNO</th>
                                <th>Ref #1</th>
                                <th>Ref #2</th>
                                <th>Gdes</th>
                                <th>Season</th>
                                <th>Product Type</th>
                                <th>Unit</th>
                                <th>Buyer</th>
                                <th>Style</th>
                                <th>Qty</th>
                                <th>SMV</th>
                                <th>SAH</th>
                                <th>CM</th>
                                <th>CM ERN</th>
                                <th>CM/SMV</th>
                                <th>WSH</th>
                                <th>WSH ERN</th>
                                <th>Total FOB</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>Created By</th>
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
        var searchable = [1,2,6,7,8,9];
        var selectable = [21]; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {
            '21': ['Active','Approval Pending','Costed','Completed','Inactive']
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
                url: '{!! url("merch/query/merch_order_query_listorder") !!}',
                data: {
                    unit: '{{ isset($request1['unit'])?$request1['unit']:'' }}',
                    buyer: '{{ isset($request1['buyer'])?$request1['buyer']:'' }}',
                    product: '{{ isset($request1['product'])?$request1['product']:'' }}',
                    status: '{{ isset($request1['status'])?$request1['status']:'' }}'
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
                    orientation: 'landscape',
                    pageSize:'A4',
                    className: 'btn-sm btn-primary',
                    exportOptions: {
                        // columns: ':visible'
                        columns: [1,2,3,4,5,6,9,10,11,12,13,14,15,16,17]
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    orientation: 'landscape',
                    pageSize:'A4',
                    className: 'btn-sm btn-default print',
                    exportOptions: {
                        // columns: ':visible',
                        columns: [1,2,3,4,5,6,9,10,11,12,13,14,15,16,17],
                        stripHtml: false
                    }

                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'order_code',  name: 'order_code' },
                { data: 'file_no',  name: 'file_no' },
                { data: 'ref_1',  name: 'ref_1' },
                { data: 'ref_2',  name: 'ref_2' },
                { data: 'gdes',  name: 'gdes' },
                { data: 'season',  name: 'season' },
                { data: 'prdtype', name: 'prdtype'},
                { data: 'unit',  name: 'unit' },
                { data: 'buyer_name',  name: 'buyer_name' },
                { data: 'style',  name: 'style' },
                { data: 'order_qty',  name: 'order_qty' },
                { data: 'smv',  name: 'smv' },
                { data: 'sah',  name: 'sah' },
                { data: 'cm',  name: 'cm' },
                { data: 'cm_ern',  name: 'cm_ern' },
                { data: 'cm_by_smv',  name: 'cm_by_smv' },
                { data: 'wsh',  name: 'wsh' },
                { data: 'wsh_ern',  name: 'wsh_ern' },
                { data: 'total_fob', name: 'total_fob' },
                { data: 'order_delivery_date',  name: 'order_delivery_date' },
                { data: 'order_status',  name: 'order_status' },
                { data: 'created_by', name: 'created_by' }

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