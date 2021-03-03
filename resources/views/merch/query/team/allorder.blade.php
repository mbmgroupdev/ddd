
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
                    <a href="#" class="search_team" data-unit="{{ $request1['unit'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['team']))
                @if(!isset($request1['unit']))
                <li>
                    <a href="#" class="search_team"> All Team </a>
                </li>
                @endif
                <li>
                     <a href="#" class="search_executive" data-team="{{ $request1['team'] }}">
                        {{ $data['teaminfo']->team_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['executive']))
                <li>
                     <a href="#" class="search_order" data-executive="{{ $request1['executive'] }}">
                        {{ $data['executive']->as_name }}
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
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Order</th>
                                <th>Product Type</th>   
                                <th>Team</th>
                                <th>Buyer</th>
                                <th>Style</th>
                                <th>Status</th>
                                <th>Delivery Date</th>
                                <th>Qty</th>
                                <th>Total FOB</th>
                                <th>Unit</th>
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
        var searchable = [1,3,4,5];
        var selectable = [2,6]; //use 4,5,6,7,6,9,10,11,....and * for all
        var dropdownList = {
            '2': ['Bottom','Top', 'Overall', 'Outerwear'],
            '6': ['Active','Approval Pending','Costed','Completed','Inactive']
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
                url: '{!! url("merch/query/merch_team_query_listorder") !!}',
                data: {
                    unit: '{{ isset($request1['unit'])?$request1['unit']:'' }}',
                    team: '{{ isset($request1['team'])?$request1['team']:'' }}',
                    executive: '{{ isset($request1['executive'])?$request1['executive']:'' }}',
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
                { data: 'order_code',  name: 'order_code' },
                { data: 'prdtype', name: 'prdtype'},
                { data: 'team',  name: 'team' },
                { data: 'buyer_name',  name: 'buyer_name' },
                { data: 'style',  name: 'style' },
                { data: 'order_status',  name: 'order_status' },
                { data: 'order_delivery_date',  name: 'order_delivery_date' },
                { data: 'order_qty',  name: 'order_qty' },
                { data: 'total_fob', name: 'total_fob' },
                { data: 'unit',  name: 'unit' },
                { data: 'created_by',  name: 'created_by' }

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