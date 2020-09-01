@extends('layouts.app')
@include('hr.menu')

@section('content')
	<div class="container-fluid no-padding">
		@yield('main-content')
	</div>

	@push('js')
		@toastr_render
	@endpush
@endsection