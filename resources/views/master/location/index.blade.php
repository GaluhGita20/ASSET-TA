@extends('layouts.lists')

@section('filters')
	<div class="row">
		<div class="ml-4 pb-2" style="width: 350px">
			<input type="text" class="form-control filter-control" data-post="name"
				placeholder="{{ __('Nama Ruang') }}">
		</div>
		<div class="ml-4 pb-2" style="width: 350px">
			<select class="form-control filter-control base-plugin--select2-ajax"
			data-url="{{ rut('ajax.selectStruct', 'alls') }}" data-post="departemen_id"
			data-placeholder="{{ __('Unit Organisasi') }}">
		</select>
	</div>
</div>
@endsection 
{{-- @section('filters')
	<div class="row">
		<div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
			<input type="text" class="form-control filter-control" data-post="name" placeholder="{{ __('Provinsi') }}">
		</div>
	</div>
@endsection 

@endsection --}}

@section('buttons')
	@if (auth()->user()->hasRole('Administrator'))
	{{-- @if (auth()->user()->checkPerms($perms.'.create')) --}}
		@include('layouts.forms.btnAdd')
	@endif
@endsection
