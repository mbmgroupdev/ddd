@push('css')
	<style>	
	.nav-search .nav-search-input { width: 100%;}
	</style>
@endpush

@if(isset($_REQUEST['search']))
	@php $value = $_REQUEST['search']; @endphp
@else
	@php $value = ''; @endphp
@endif

<div class="iq-search-bar">
    <form action="{{ url('/search') }}" method="get" class="searchbox" id="form-seach">
       <input type="text" name="search" placeholder="Search Employee..." class="text search-input typeahead seach-employee" placeholder="Type here to search..." value="{{ $value }}" id="nav-search-input1" autocomplete="off" required data-type="employee">
       <a class="search-link" href="#"><i class="las la-user-circle"></i></a>
    </form>
    <div id="search-suggestion" style="position: relative">
    	
    </div>
 </div>
@push('js')
	
	<script>

		$(document).on('keyup', '.seach-employee', function (e) {
			var keyword = $('.seach-employee').val();
			if(keyword !== '' && keyword !== null){
				if (e.which == 13) {
						$('form#form-seach').submit();
				    	return false; 
				}else{
					$.ajax({
			            url: '{{ url("search/suggestion") }}',
			            data: {
			                keyword: keyword,
			                _token : "{{ csrf_token() }}",
			            },
			            type: 'post',
			            success: function(result)
			            {  
			                $('#search-suggestion').html(result);
			            },
			            error:function(xhr)
			            {
			            	$('#search-suggestion').html('');
			            }
			        });
				}
			}else{
				$('#search-suggestion').html('');
			}
			
		  
		});
	</script>
@endpush