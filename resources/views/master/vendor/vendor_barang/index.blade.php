@extends('layouts.lists')
@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="name" placeholder="{{ __('Vendor') }}">
        </div>
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="getJenisUsahaName" placeholder="{{ __('Jenis Usaha') }}">
        </div> --}}

      
    </div>
@endsection

@section('buttons')
	{{-- @if (auth()->user()->checkPerms($perms.'.create')) --}}
    @if(auth()->user()->hasRole('Sarpras') || auth()->user()->hasRole('PPK'))
		@include('layouts.forms.btnAdd')
	@endif
@endsection
