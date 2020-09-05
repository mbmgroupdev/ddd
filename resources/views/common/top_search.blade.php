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
 </div>
@push('js')
	
	<script>

		$('.seach-employee').keypress(function (e) {
			var search = $('.seach-employee').val();
			if (e.which == 13) {
				if(search !== '' && search !== null){
					$('form#form-seach').submit();
			    	return false; 
				}else{
					return false;
				}    
			}
			
		  
		});

		// $(document).jQuery(document).ready(function($) {
		// 	$('input').attr('autocomplete', 'off');
		// });
	</script>
@endpush