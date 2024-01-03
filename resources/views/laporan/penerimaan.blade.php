@extends('layouts.lists')


@section('filters')
<div class="row">
    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <input type="text" class="form-control filter-control" data-post="no_spk" placeholder="{{ __('No SPK') }}">
    </div>
    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <select class="form-control filter-control base-plugin--select2-ajax" name="vendor_id" data-url="{{ route('ajax.selectVendor', 'all') }}"
            data-placeholder="{{ __('Nama Vendor') }}" data-post="vendor_id">
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
