@extends('hr.layout')
@section('title', 'Recruitment List')
@push('css')
  
  <link rel="stylesheet" href="{{ asset('assets/css/recruitment.css')}}">
@endpush
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">All Recruitment List</h4>
               </div>
            </div>
            <div class="iq-card-body">
               <table id="recruit" class="table table-hover table-borderd table-responsive">
                  <thead>
                     <tr>
                        <th width="5%">Sl</th>
                        <th>Employee Type</th>
                        <th>Designation</th>
                        <th width="5%">Unit</th>
                        <th>Area</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>DOJ</th>
                        <th>Medical</th>
                        <th>IE</th>
                        <th>Action</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   @push('js')
   <script type="text/javascript">
   $(document).ready(function(){ 
      var searchable = [1,2,3,4,5,6,7];
       $('#recruit').DataTable({
          order: [], //reset auto order
          processing: true,
          responsive: true,
          serverSide: true,
          pagingType: "full_numbers", 
          ajax: {
               url: '{!! url("hr/recruitment/recruit-data-list") !!}',
               type: "GET",
               headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
               } 
          }, 
          dom: "<'row'<'col-sm-3'l><'col-sm-5 text-center'B><'col-sm-4'f>>tip",
          buttons: [  
             {
                 extend: 'copy', 
                 className: 'btn-sm btn-info',
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
                 className: 'btn-sm btn-default',
                 exportOptions: {
                     columns: ':visible'
                 } 
             } 
          ], 
          columns: [  
               {data: 'DT_RowIndex', name: 'DT_RowIndex'}, 
               {data: 'hr_emp_type_name', name: 'hr_emp_type_name'}, 
               {data: 'hr_designation_name', name: 'hr_designation_name'}, 
               {data: 'hr_unit_short_name', name: 'hr_unit_short_name'}, 
               {data: 'hr_area_name', name: 'hr_area_name'}, 
               {data: 'worker_name', name: 'worker_name'}, 
               {data: 'worker_contact', name: 'worker_contact'}, 
               {data: 'worker_doj', contact: 'worker_doj'},
               {data: 'medical_info', contact: 'medical_info'},
               {data: 'ie_info', contact: 'ie_info'},
               {data: 'action', name: 'action', orderable: false, searchable: false}
            ],

            initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 120px; height:25px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });
         } 
       }); 

   }); 
   </script>
   @endpush
@endsection