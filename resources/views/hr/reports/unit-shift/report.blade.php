<div class="change-section">
    <div class="iq-card">
        <div class="iq-card-body">
            <div class="row justify-content-center">
                @foreach($unitShift as $key => $value)
                <div class="col-6">
                    <div class="panel">
                        <div class="panel-heading"><h6>{{ $key }} </h6></div>
                        <div class="panel-body">
                            <table id="" class="table table-striped table-bordered table-head">
                                <thead>
                                    <tr>
                                        <th>Shift Name</th>
                                        <th>Start Time</th>
                                        <th>Break Time</th>
                                        <th>Out Time</th>
                                        <th>Default (No. Employee)</th>
                                        <th>Changed (No. Employee)</th>
                                    </tr>
                                </thead>
                                <tbody id="shifttablerow">
                                    {!! $value !!}
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>