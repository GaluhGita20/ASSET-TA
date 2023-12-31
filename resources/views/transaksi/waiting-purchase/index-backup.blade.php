@extends('layouts.lists')
@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="no_spk" placeholder="{{ __('No SPK') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="ref_vendor" data-url="{{ route('ajax.selectVendor', 'all') }}"
                data-placeholder="{{ __('Nama Vendor') }}" data-post="ref_vendor">
            </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <div class="input-group">
                <input name="spk_start_date"
                    class="form-control base-plugin--datepicker spk_start_date"
                    placeholder="{{ __('Mulai') }}"
                    data-orientation="bottom"
                    data-post="spk_start_date"
                    >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="la la-ellipsis-h"></i>
                    </span>
                </div>
                <input name="spk_end_date"
                    class="form-control filter-control base-plugin--datepicker spk_end_date"
                    placeholder="{{ __('Selesai') }}"
                    data-orientation="bottom"
                    data-post="spk_end_date"
                    >
            </div>
        </div>
    </div>
@endsection

@section('buttons')
	@if (auth()->user()->checkPerms($perms.'.create'))
		{{-- @include('layouts.forms.btnAdd') --}}
        <a href="{{ $urlAdd ?? (\Route::has($routes.'.store') ? rut($routes.'.store') : 'javascript:;') }}"
            class="btn btn-info ml-2">
            <i class="fa fa-plus"></i> Data
        </a>
	@endif
@endsection
@section('buttons')
@endsection
