@extends('layouts.lists')

{{-- <div class="alert alert-custom alert-light-primary fade show py-4" role="alert">
    <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
    <div class="alert-text text-primary">
        <div class="text-bold">{{ __('Informasi') }}:</div>
        <div class="mb-10px" style="white-space: pre-wrap;">Silahkan Checklist Aset Sejenis Untuk Dibuatkan Transaksi Pembelian, Gunakan Filter Untuk Memudahkan Menemukan Aset Sejenis</div>
    </div>
</div> --}}
@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="trans_name" placeholder="{{ __('Nama Transaksi') }}">
        </div>

        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="no_spk" placeholder="{{ __('Nomor Kontrak') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="vendor_id" data-url="{{ route('ajax.selectVendor', 'all') }}"
                data-placeholder="{{ __('Nama Vendor') }}" data-post="vendor_id">
            </select>
        </div>
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
            <input type='text' class="form-control filter-control  base-plugin--datepicker spk_start_date" name="spk_start_date" 
                placeholder="{{ __('Tanggal Mulai Kontrak') }}" data-post="spk_start_date">
        </div>
        <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
            <input type='text' class="form-control filter-control  base-plugin--datepicker spk_end_date" name="spk_end_date" 
                placeholder="{{ __('Tanggal Selesai Kontrak') }}" data-post="spk_end_date">
        </div>

        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
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
        </div> --}}
    </div>

    <div class="alert alert-custom alert-light-primary fade show py-3" style="left:-30pt;" role="alert">
        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
        <div class="alert-text text-primary">
            <div class="text-bold">{{ __('Informasi') }}:</div>
            <div class="mb-10px" style="white-space: pre-wrap;">Silahkan Lengkapi Laporan Data Transaksi Aset , Ketika Aset Telah Diterima Oleh Rumah Sakit</div>
        </div>
    </div>
    {{-- @if(auth()->user()->hasRole('PPK','Keuangan')) --}}
    {{-- @endif --}}
@endsection
@section('buttons')
@endsection
