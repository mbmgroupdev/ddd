
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
                     <a href="#" class="search_pi" data-buyer="{{ $request1['buyer'] }}">
                        {{ $data['buyerinfo']->b_name }}
                    </a>
                </li>
            @endif
            <li class="active"> Proforma Invoice </li>
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
                                <th>Sl</th>
                                <th>PI No</th>
                                <th>Supplier</th>
                                <th>Booking Ref</th>
                                <th>PI Qty</th>
                                <th>Catrgory</th>
                                <th>Ship Mode</th>
                                <th>PI Date</th>
                                <th>PI Last Date</th>
                                <th>Status</th>
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
        var searchable = [1,2,3,4,5];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {};

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
                url: '{!! url("merch/query/merch_pi_query_listpi") !!}',
                data: {
                    unit: '{{ isset($request1['unit'])?$request1['unit']:'' }}',
                    buyer: '{{ isset($request1['buyer'])?$request1['buyer']:'' }}',
                    supplier: '{{ isset($request1['supplier'])?$request1['supplier']:'' }}'
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
                { data: 'pi_no', name: 'pi_no' },
                { data: 'supplier', name: 'supplier' },
                { data: 'booking', name: 'booking' },
                { data: 'total_pi_qty', name: 'total_pi_qty'},
                { data: 'pi_category', name: 'pi_category' },
                { data: 'ship_mode', name: 'ship_mode' },
                { data: 'pi_date', name: 'pi_date' },
                { data: 'pi_last_date', name: 'pi_last_date' },
                { data: 'pi_status', name: 'pi_status' }
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