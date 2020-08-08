@extends('layouts.app')
@include('hr.menu')
@push('css')
	@toastr_css
@endpush
@section('content')
	<div class="container-fluid">
		@yield('main-content')
	</div>

	@push('js')
		@toastr_js
		@toastr_render
	@endpush
@endsection