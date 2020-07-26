@extends('layouts.app')
@include('user.menu')
@section('content')
	<div class="container-fluid">
		@yield('main-content')
	</div>
@endsection