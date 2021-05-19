<div class="panel">
    <div class="panel-body">
        <div id="report_section" class="report_section">
            
            @php
                $unit = unit_by_id();
                $designation = designation_by_id();
                $fromDate = date('d-m-Y', strtotime($input['from_date']));
                $toDate = date('d-m-Y', strtotime($input['to_date']));
            @endphp
            <div class="top_summery_section">
                
                <div class="page-header">
                    <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Bill Report </h2>
                    <h4  style="text-align: center;">
                        @if($input['date_type'] == 'range')
                        Date: {{ $fromDate }} To {{ ($toDate) }}
                        @elseif($input['date_type'] == 'month')
                        Month: {{ ($input['month_year']) }}
                        @endif
                    </h4>
                </div>
                
                
            </div>
            <table class="table table-bordered table-hover table-head" border="1" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" cellpadding="5">
                <!-- custom design for all-->
                
                    <thead>
                        <tr class="text-center">
                            <th>Name</th>
                            <th>DOJ</th>
                            <th>Designation</th>
                            <th>ID</th>
                            <th>Account No.</th>
                            <th>Total Days</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($getBillList as $value)
                        <tr>
                            <td>{{ $value->as_name }}</td>
                            <td>{{ $value->as_doj }}</td>
                            <td>{{ $designation[$value->as_designation_id]['hr_designation_name'] }}</td>
                            <td>{{ $value->associate_id }}</td>
                            <td>{{ ($value->ben_bank_amount > 0)?$value->bank_no:'' }}</td>
                            <td>{{ $value->totalDay }}</td>
                            <td>{{ $value->totalAmount }}</td>
                        </tr>
                        @endforeach
                        
                    </tbody>
            </table>
            
        </div>

        {{-- modal --}}
        
    </div>
</div>
