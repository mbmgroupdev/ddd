<div class="panel panel-info col-sm-12 col-xs-12">
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
                    <a href="#" class="search_floor" data-unit="{{ $request1['unit'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['floor']))
                <li>
                    <a href="#" class="search_line" data-floor="{{ $request1['floor'] }}">
                        {{ $data['floor']->hr_floor_name }}
                    </a>
                </li>
            @endif
            <li class="active"> Line </li>
        </ul><!-- /.breadcrumb -->
         <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($line_data)}},"{{$showTitle}}")'>Print</a>

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Line Name</th>
                                <th>Unit</th>
                                <th>Floor</th>
                                <th>Employee</th>
                                <th>Line Change</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $count=0; @endphp
                        @foreach($line_data as $line)
                        @php $count++; @endphp
                            <tr class="line_change" data-line="{{$line->hr_line_id}}" style="cursor:pointer;">
                                <th>{{$count}}</th>
                                <th>{{$line->hr_line_name}}</th>
                                <th>{{$line->unit}}</th>
                                <th>{{$line->floor}}</th>
                                <th>{{$line->emp}}</th>
                                <th>{{$line->line_change}}</th>
                            </tr>
                         @endforeach
                            
                        </tbody>
                    </table>
                </div>

        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>

<script>
    
    function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_line_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                type: 'Line',
                title: pagetitle,
                unit: "{{ isset($request1['unit'])?$request1['unit']:'' }}",
                floor: "{{ isset($request1['floor'])?$request1['floor']:'' }}",
            },
            success: function(data) {
                $('#printOutputSection').html(data);
                var divToPrint=document.getElementById('printOutputSection');
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
            }
        });
    }
    


</script>

