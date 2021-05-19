@push('css')
  <style>
    .navbar-modal{width: 260px !important; box-shadow: -2px 0px 6px 1px; }
    .custom-control-label {line-height: 18px;}
    .group-checkbox{margin-top: 5px; margin-left: 5px;}
  </style>
@endpush
<div class="modal right fade" id="right_modal_navbar" tabindex="-1" role="dialog" aria-labelledby="right_modal_navbar">
  <div class="modal-dialog modal-lg navbar-modal" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back">
          <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="navbar-title-right"> &nbsp; </h5>
      {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button> --}}
    </div>
    <div class="modal-body">
      <div class="modal-content-result" id="content-result">
        <form id="filterForm">
          <div class="filter-section">
            <div class="form-group mb-2">
              <label for="" class="m-0 fwb"><h5>Unit <input type='checkbox' id="unit" class="unit-group group-checkbox bg-primary" checked onclick="checkAllGroup(this)" /></h5></label>
              <hr class="mt-2">
              <div class="row">
                @foreach(unit_by_id()->chunk(2) as $unitList)
                  <div class="col pr-0">
                    @foreach($unitList as $unit)
                    <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                      <input type="checkbox" name="unit[]" class="custom-control-input bg-primary unit" value="{{ $unit['hr_unit_id'] }}" id="unit-{{ $unit['hr_unit_id'] }}" checked>
                      <label class="custom-control-label" for="unit-{{ $unit['hr_unit_id'] }}"> {{ $unit['hr_unit_short_name'] }}</label>
                    </div>
                    @endforeach
                  </div>  
                @endforeach
              </div>
            </div>
            <div class="form-group mb-2">
              <label for="" class="m-0 fwb"><h5>Location <input type='checkbox' id="location" class="location-group group-checkbox bg-primary" checked onclick="checkAllGroup(this)" /></h5></label>
              <hr class="mt-2">
              <div class="row">
                @foreach(location_by_id()->chunk(3) as $locationList)
                  <div class="col pr-0">
                    @foreach($locationList as $location)
                    <div class="custom-control custom-checkbox custom-checkbox-color-check location-group">
                      <input type="checkbox" name="location[]" class="custom-control-input bg-primary location" value="{{ $location['hr_location_id'] }}" id="location-{{ $location['hr_location_id'] }}" checked>
                      <label class="custom-control-label" for="location-{{ $location['hr_location_id'] }}"> {{ $location['hr_location_short_name'] }}</label>
                    </div>
                    @endforeach
                  </div>  
                @endforeach
              </div>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="area" class="form-control capitalize select-search" id="area">
                  <option selected="" value="">Choose Area...</option>
                  @foreach(area_by_id() as $key => $area)
                  <option value="{{ $key }}">{{ $area['hr_area_name'] }}</option>
                  @endforeach
              </select>
              <label for="area">Area</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="department" class="form-control capitalize select-search" id="department">
                  <option selected="" value="">Choose Department...</option>
                  @foreach(department_by_id() as $key => $department)
                  <option value="{{ $key }}">{{ $department['hr_department_name'] }}</option>
                  @endforeach
              </select>
              <label for="department">Department</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="section" class="form-control capitalize select-search " id="section">
                  <option selected="" value="">Choose Section...</option>
                  @foreach(section_by_id() as $key => $section)
                  <option value="{{ $key }}">{{ $section['hr_section_name'] }}</option>
                  @endforeach
              </select>
              <label for="section">Section</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="subSection" class="form-control capitalize select-search" id="subSection">
                  <option selected="" value="">Choose Sub Section...</option> 
                  @foreach(subSection_by_id() as $key => $subSection)
                  <option value="{{ $key }}">{{ $subSection['hr_subsec_name'] }}</option>
                  @endforeach
              </select>
              <label for="subSection">Sub Section</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="floor_id" class="form-control capitalize select-search" id="floor_id" >
                  <option selected="" value="">Choose Floor...</option>
                  @foreach(floor_by_id() as $key => $floor)
                  <option value="{{ $key }}">{{ $floor['hr_floor_name'] }}</option>
                  @endforeach
              </select>
              <label for="floor_id">Floor</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="line_id" class="form-control capitalize select-search" id="line_id" >
                  <option selected="" value="">Choose Line...</option>
                  @foreach(line_by_id() as $key => $line)
                  <option value="{{ $key }}">{{ $line['hr_line_name'] }}</option>
                  @endforeach
              </select>
              <label for="line_id">Line</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                  <option selected="" value="">Choose...</option>
                  <option value="0">Non-OT</option>
                  <option value="1">OT</option>
              </select>
              <label for="otnonot">OT/Non-OT</label>
            </div>
            <hr class="mt-2">
            <div class="form-group mb-2">
              <label for="" class="m-0 fwb">Salary</label>
              <hr class="mt-2">
              <div class="row">
                <div class="col-5 pr-0">
                  <div class="form-group has-float-label has-required">
                    <input type="number" class="report_date min_sal form-control" id="min_sal" name="min_sal" placeholder="Min Salary" required="required" value="0" min="0" max="{{ $salaryMax }}" autocomplete="off" />
                    <label for="min_sal">Min</label>
                  </div>
                </div>
                <div class="col-1 p-0">
                  <div class="c1DHiF text-center">-</div>
                </div>
                <div class="col-6">
                  <div class="form-group has-float-label has-required">
                    <input type="number" class="report_date max_sal form-control" id="max_sal" name="max_sal" placeholder="Max Salary" required="required" value="{{ $salaryMax }}" min="0" max="{{ $salaryMax }}" autocomplete="off" />
                    <label for="max_sal">Max</label>
                  </div>
                </div>
              </div>
            </div>
            @yield('right-nav')
            <hr class="mt-2">
            <div class="form-group has-float-label has-required select-search-group">
              <?php
                $status = ['1'=>'Active','25' => 'Left & Resign','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left', '6'=>'Maternity'];
              ?>
              {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus', 'required']) }}
              <label for="estatus">Status</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <?php
                $payType = ['all'=>'All', 'cash'=>'Cash', 'rocket'=>'Rocket', 'bKash'=>'bKash', 'dbbl'=>'Duch-Bangla Bank Limited.'];
              ?>
              {{ Form::select('pay_status', $payType, 'all', ['placeholder'=>'Select Payment Type', 'class'=>'form-control capitalize select-search', 'id'=>'paymentType']) }}
              <label for="paymentType">Payment Type</label>
            </div>
            <hr class="mt-2">
            <div class="form-group">
              
              <button class="btn btn-primary nextBtn btn-lg pull-right filterBtnSubmit" type="button" ><i class="fa fa-filter"></i> Filter</button>
            </div>
            
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
    $(document).on('click', '.filterBtnSubmit', function(e) {
      e.preventDefault();
      advFilter();
    });
    let afterLoader = '<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>';
    function advFilter(){
      $(".prev_btn").click();
      $("#result-section-btn").show();
      $("#report_section").html(loaderContent);
      $("#single-employee-search").hide();
      var flag = 0;
      var data = $("#filterForm").serialize() + '&' + $("#formReport").serialize();
      if(flag === 0){
        $('html, body').animate({
            scrollTop: $("#result-data").offset().top
        }, 2000);
        $.ajax({
            type: "POST",
            url: '/hr/reports/salary-report',
            data: data, // serializes the form's elements.
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            success: function(response)
            {
              // console.log(response);
              if(response !== 'error'){
                $("#report_section").html(response);
              }
            },
            error: function (reject) {
              console.log(reject);
            }
        });
      }else{
        console.log('required');
      }
    }
    
    //Load Department List By Area ID
    $('#area').on("change", function(){
      if($(this).val() !== ''){
        $.ajax({
          url : "{{ url('hr/area-wise-department') }}"+'/'+$(this).val(),
          type: 'get',
          success: function(data)
          {
            if(data.status === 'success'){
              departmentLoad(data.value);  
            }
          },
          error: function(reject)
          {
            console.log(reject);
          }
        });
      }else{
        departmentLoad('all');
      }
      sectionLoad('all');
      subSectionLoad('all');
    });

    //Load Section List By department ID
    $('#department').on("change", function(){
      if($(this).val() !== ''){
        $.ajax({
          url : "{{ url('hr/department-wise-section') }}"+'/'+$(this).val(),
          type: 'get',
          success: function(data)
          {
            if(data.status === 'success') sectionLoad(data.value);
          },
          error: function(reject)
          {
            console.log(reject);
          }
        });
      }else{
        sectionLoad('all');
      }
      subSectionLoad('all');
    });
    //Load Sub Section List by Section
    $('#section').on("change", function(){
      if($(this).val() !== ''){
        $.ajax({
          url : "{{ url('hr/section-wise-subsection') }}"+'/'+$(this).val(),
          type: 'get',
          success: function(data)
          {
            if(data.status === 'success') subSectionLoad(data.value);
          },
          error: function(reject)
          {
            console.log(reject);
          }
        });
      }else{
        subSectionLoad('all');
      }
    });
    
    function checkAllGroup(val){
      var id = $(val).attr('id')
      if($(val).is(':checked')){
        $('.'+id).each(function() {
            $(this).prop("checked", true);
        });
      }else{
        $('.'+id).each(function() {
            $(this).prop("checked", false);
        });
      }
    }
    $(document).on('click', '.custom-control-input', function(event) {
      let id = $(this).attr('id');
      let idsplit = id.split('-');
      let name = idsplit[0];
      let checkLength = $('.'+name+':checkbox').length;
      let selectLength = $('.'+name+':checkbox:checked').length;
      if(checkLength === selectLength){
        $('#'+name).prop("checked", true);
      }else{
        $('#'+name).prop("checked", false);
      }
    });
    function departmentLoad(data){
      $('#department').empty().attr('disabled', true).after(afterLoader);
      if(data === 'all'){
        data = @json(department_by_id());
      }
      $('#department').append('<option value=""> - Choose Department - </option>');
      $.each(data, function(index, el) {
        $('#department').append('<option value="'+el.hr_department_id+'">'+el.hr_department_name+'</option>');
      });
      removeEndLoad('department');
    }

    function sectionLoad(data){
      $('#section').empty().attr('disabled', true).after(afterLoader);;
      if(data === 'all'){
        data = @json(section_by_id());
      }
      $('#section').append('<option value=""> - Choose Section - </option>');
      $.each(data, function(index, el) {
        $('#section').append('<option value="'+el.hr_section_id+'">'+el.hr_section_name+'</option>');
      });
      removeEndLoad('section');
    }
    function subSectionLoad(data){
      $('#subSection').empty().attr('disabled', true).after(afterLoader);;
      if(data === 'all'){
        data = @json(subSection_by_id());
      }
      $('#subSection').append('<option value=""> - Choose Sub Section - </option>');
      $.each(data, function(index, el) {
        $('#subSection').append('<option value="'+el.hr_subsec_id+'">'+el.hr_subsec_name+'</option>');
      });
      removeEndLoad('subSection');
    }
    function removeEndLoad(attr){
      setTimeout(function(){
        $('.loading-select').remove();
        $('#'+attr).removeAttr('disabled');
      }, 500);
    }
</script>
@endpush