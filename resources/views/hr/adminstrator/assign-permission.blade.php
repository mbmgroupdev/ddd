@extends('hr.layout')
@section('title', 'Assign Permission')
@section('main-content')
   <div class="row">
      <div class="col-sm-12 col-lg-12">
         <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">Assign Permission</h4>
               </div>
            </div>
            <div class="iq-card-body"> 
                <div class="row justify-content-md-center mb-3">
                	<div class="col-4">
	                    {{ Form::select('user_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'user_id', 'class'=> 'associates form-control',]) }}
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
					                           	<div class="custom-control custom-checkbox checkbox-icon custom-control-inline">
					                              <input type="checkbox" class="custom-control-input" id="Sl-{{$key}}-{{$count}}" >
					                              <label class="custom-control-label" for="Sl-{{$key}}-{{$count}}"><i class="fa fa-shield"></i></label>
					                           </div>
							      				<a class="card-link @if($count != 1) collapsed @endif" data-toggle="collapse" href="#{{$key}}-{{$count}}">
							        				{{$key1}} 
							      				</a>
							    			</div>
							    			<div id="{{$key}}-{{$count}}" class="collapse @if($count == 1)show @endif" data-parent="#accordion-{{$key}}">
							      				<div class="card-body">
							      					<div class="row permissions_{{$key1}}">
		                                                @foreach($group as $key2 => $permission)
		                                                <div class="col-sm-4">
		                                                	<div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
								                              	<input type="checkbox" class="custom-control-input bg-success" id="perm-{{$permission->id}}" value="{{$permission->name}}" name="permissions[]">
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
   			$(document).on('change', 'select.associates', function(){
		        $.ajax({
		            url : "{{ url('hr/adminstrator/user/get-permission') }}",
		            type: 'get',
		            data: {
		                id : $(this).val()
		            },
		            success: function(data)
		            {
		               $('.permission-gallery').html(data);
		               $('.perm-group').each(function() {
		                    if($(this).parent().parent().next().find('input:checkbox').not(':checked').length == 0){
		                        $(this).prop('checked', true);
		                    }
		                });
		            },
		            error: function()
		            {
		                alert('failed...');
		            }
		        });
		    });
   		</script>
   @endpush
@endsection