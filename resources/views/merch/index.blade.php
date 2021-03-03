
@extends('layouts.master')

@section('title', 'MERCHANDISING')

@section('main-container')
	@push('css')
	  	<style>
	    	.form-actions {margin-bottom: 0px;  margin-top: 0px;}
			td input[type=text], input[type=number] {font-size: 11px;}
			table.dataTable thead th, table.dataTable thead td, table.dataTable tbody td {border-bottom: 1px solid #c1aaaa !important;}

	  	</style>

	@endpush
	@include('merch.top_navbar')
	<div class="main-container ace-save-state" id="main-container">
		<script type="text/javascript">
			try{ace.settings.loadState('main-container')}catch(e){}
		</script>
		<div id="sidebar" class="sidebar responsive ace-save-state">
		@include('merch.menu')
		</div>
		@yield('content')

		@push('js')
		<script src="{{ asset('assets/js/custom.js') }}"></script>

			<script>
				$(document).ready(function(){
				    $( document ).on( 'focus', ':input', function(){
				        $( this ).attr( 'autocomplete', 'off' );
				    });
				});
				$(document).ready(function () {

			      $('input').keyup(function (e) {
			        if (e.which == 39) { // right arrow
			          $(this).closest('td').next().find('input').focus();

			        } else if (e.which == 37) { // left arrow
			          $(this).closest('td').prev().find('input').focus();

			        } else if (e.which == 40) { // down arrow
			          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();

			        } else if (e.which == 38) { // up arrow
			          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
			        }
			      });


			    });
			</script>
		@endpush
@endsection
