@extends('layouts.lists')

@section('filters')
	<div class="row">
		<div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
			<input type="text" class="form-control filter-control" data-post="name" placeholder="{{ __('Provinsi') }}">
		</div>
	</div>
@endsection

@section('buttons')
	@if (auth()->user()->hasRole('Administrator'))
	{{-- @if (auth()->user()->checkPerms($perms.'.create')) --}}
		@include('layouts.forms.btnAdd')
	@endif
@endsection
