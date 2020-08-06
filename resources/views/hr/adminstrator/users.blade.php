@extends('hr.layout')
@section('title', 'All Users')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">All Users</h4>
               </div>
            </div>
            <div class="iq-card-body">
               <table id="users" class="table table-hover table-borderd">
                  <thead>
                     <tr>
                        <th>Associate ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Unit Permission</th>
                        <th>Roles</th>
                        <th>Buyer Permission</th>
                        <th>Management Permission</th>
                        <th>Action</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   @push('js')
   <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
   <script type="text/javascript">
   $(document).ready(function(){ 
      var searchable = [0,1,2];
       $('#users').DataTable({
           order: [], //reset auto order
           processing: true,
           responsive: true,
           serverSide: true,
           pagingType: "full_numbers", 
           ajax: {
               url: '{!! url("hr/adminstrator/user/list") !!}',
               type: "POST",
               headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
               } 
           }, 
           dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
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
               {data: 'associate_id', name: 'associate_id'}, 
               {data: 'name', name: 'name'}, 
               {data: 'email',  name: 'email'}, 
               {data: 'units', name: 'units'},  
               {data: 'roles', name: 'roles'}, 
               {data: 'buyer', name: 'buyer'},  
               {data: 'management', name: 'management'},  
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