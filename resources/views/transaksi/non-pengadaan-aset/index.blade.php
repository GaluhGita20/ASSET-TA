@extends('layouts.lists')

@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="vendor_id" data-url="{{ route('ajax.selectVendor', 'all') }}"
                data-placeholder="{{ __('Nama Vendor') }}" data-post="vendor_id">
            </select>
        </div>

        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input class="form-control base-plugin--datepicker receipt_date" data-post="receipt_date" placeholder="{{ __('Tanggal Penerimaan') }}">
        </div>  --}}
    </div>
@endsection
@section('buttons')
    @if (auth()->user()->checkPerms($perms.'.create'))
		@include('layouts.forms.btnAdd')
	@endif
@endsection
