@extends('layouts.lists')

@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="trans_name" placeholder="{{ __('Nama Transaksi') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="vendor_id" data-url="{{ route('ajax.selectVendor', 'all') }}"
            data-placeholder="{{ __('Nama Vendor') }}" data-post="vendor_id">
            </select>
        </div>
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input  class="form-control base-plugin--datepicker receipt_date" name="receipt_date" data-post="receipt_date" placeholder="{{ __('Tanggal Penerimaan') }}">
        </div> --}}
        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
			<select class="form-control base-plugin--select2-ajax filter-control"
				data-post="status"
				data-placeholder="{{ __('Status') }}">
				<option value="" selected>{{ __('Status') }}</option>
				<option value="Draft">Draft</option>
				<option value="waiting.approval">Waiting Approval</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
			</select>
		</div>
        <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
            <input type='text' class="form-control filter-control  base-plugin--datepicker" name="receipt_date" 
                placeholder="{{ __('Tanggal Penerimaan') }}" data-post="receipt_date">
        </div>
    </div>
@endsection

