@extends('hr.layout')
@section('title', 'Assign Permission')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="panel">
            <div class="panel-heading">
                <h6>Assign Permission
                	<div class="pull-right">
                		
	                	<a class="btn btn-primary" href="{{url('hr/adminstrator/user/create')}}"><i class="las la-user-tie f-18"></i> Add User</a> 
	                	<a class="btn btn-primary" href="{{url('hr/adminstrator/role/create')}}"><i class="las la-shield-alt f-18"></i> Add Role</a>
                	</div>
                </h6>
            </div>
            <div class="panel-body"> 
                <div class="row justify-content-md-center mb-3">
                	<div class="col-4">
	                    {{ Form::select('user_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'user_id', 'class'=> 'users form-control',]) }}
                	</div>
                </div>
                <div class="row">
                	<div id="permission-gallery" class="col-12">
                		<ul class="nav nav-tabs"  role="tablist">
                			@foreach($permissions as $key => $module)
                          	<li class="nav-item">
                             	<a class="nav-link @if($key == 'HR') active @endif" id="{{$key}}-tab" data-toggle="tab" href="#{{$key}}" role="tab" aria-controls="{{$key}}" aria-selected="true">{{$key}}</a>
                          	</li>
                          	@endforeach
                       </ul>
                       <div class="tab-content" >
                       		@foreach($permissions as $key => $module)
                            <div class="tab-pane fade @if($key == 'HR')active show @endif" id="{{$key}}" role="tabpanel" aria-labelledby="{{$key}}-tab">
                                <div id="accordion-{{$key}}">
                                	@php $count = 0; @endphp
		                            @foreach($module as $key1 => $group)
		                                @php $count++; @endphp
							  			<div class="card">
							    			<div class="card-header">{{-- 
							    				<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
					                              	<input type="checkbox" class="custom-control-input bg-success" id="Sl-{{$key}}-{{$count}}" >
					                              	<label class="custom-control-label" for="Sl-{{$key}}-{{$count}}"></label>
					                           	</div> --}}
					                           	{{-- <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
					                              <input type="checkbox" class="custom-control-input bg-success" id="Sl-{{$key}}-{{$count}}" >
					                              <label class="custom-control-label" for="Sl-{{$key}}-{{$count}}"></label>
					                           </div> --}}
					                           <input type="checkbox" id="Sl-{{$key}}-{{$count}}" style="transform: scale(1.5);" disabled title="Select user first!"> &nbsp;
							      				<a class="permission-item card-link @if($count != 1) collapsed @endif" data-toggle="collapse" href="#{{$key}}-{{$count}}">
							        				{{$key1}} 
							      				</a>
							    			</div>
							    			<div id="{{$key}}-{{$count}}" class="collapse @if($count == 1)show @endif" data-parent="#accordion-{{$key}}">
							      				<div class="card-body">
							      					<div class="row permissions_{{$key1}}">
		                                                @foreach($group as $key2 => $permission)
		                                                <div class="col-sm-4">
		                                                	<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
								                              	<input type="checkbox" class="custom-control-input bg-success" id="perm-{{$permission->id}}" value="{{$permission->name}}" name="permissions[]" disabled title="Select user first!">
								                              	<label class="custom-control-label" for="perm-{{$permission->id}}">{{$permission->name}}</label>
								                           	</div>
		                                                </div>
		                                                @endforeach
		                                            </div>
							        				
							      				</div>
							    			</div>
							  			</div>
							  		@endforeach

                                </div>
                            </div>
                            @endforeach
                        </div>
                	</div>
                </div>
                
            </div>
         </div>
      </div>
   </div>
   @push('js')
   	<script type="text/javascript">
   		$(document).on('change', 'select.users', function(){
   			$('.app-loader').show();
	        $.ajax({
	            url : "{{ url('hr/adminstrator/user/get-permission') }}",
	            type: 'get',
	            data: {
	                id : $(this).val()
	            },
	            success: function(data)
	            {
	               $('#permission-gallery').html(data);
	               $('.perm-group').each(function() {
	                    if($(this).parent().next().find('input:checkbox').not(':checked').length == 0){
	                        $(this).prop('checked', true);
	                    }
	                });
	               $('.app-loader').hide();
	            },
	            error: function()
	            {
	            	$('.app-loader').hide();
	                alert('failed...');

	            }
	        });
		});
		$(document).on('change', '.permissions', function(){
        if($('select.users').val() != ''){

            var type = 'revoke';
            if($(this).is(':checked')){
                type = 'assign';
            }
            var data = {
                type : type,
                permission : $(this).val(),
                id : $('select.users').val()
            }
            syncPermission(data);
        }else{
            alert('Please select user first!')
        }

        /*$('.perm-group').each(function() {
            if($(this).parent().parent().next().find('input:checkbox').not(':checked').length == 0){
                $(this).prop('checked', true);
            }
        });*/
        
    });

    function syncPermission(data){
        $.ajax({
            url : "{{ url('hr/adminstrator/user/sync-permission') }}",
            type: 'get',
            data: data,
            success: function(res)
            {
                toastr.options.progressBar = true ;
                toastr.options.positionClass = 'toast-top-right';
                toastr.success('Permission '+res+' user!');
            },
            error: function()
            {
                alert('failed...');
            }
        });

    }
   	</script>
   @endpush
@endsection