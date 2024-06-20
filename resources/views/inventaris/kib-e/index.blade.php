@extends('layouts.lists')
@section('filters')

<div class="row">
    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <input type="text" class="form-control filter-control" data-post="jenis_aset" placeholder="{{ __('Nama Aset') }}">
    </div>
    <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
        <select class="form-control base-plugin--select2-ajax filter-control"
            data-post="status"
            data-placeholder="{{ __('Status') }}">
            <option value="" selected>{{ __('Status') }}</option>
            <option value="actives">Active</option>
            <option value="maintenance">Dalam Pemeliharaan</option>
            <option value="in repair">Dalam Perbaikan</option>
            <option value="in deletion">Dalam Penghapusan</option>
            <option value="notactive">Not active</option>
            <option value="clean">Diputihkan</option>
        </select>
    </div>

    <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
        <select class="form-control base-plugin--select2-ajax filter-control"
            data-post="condition"
            data-placeholder="{{ __('Kondisi') }}">
            <option value="" selected>{{ __('Kondisi') }}</option>
            <option value="baik">Baik</option>
            <option value="rusak berat">Rusak Berat</option>
            <option value="rusak sedang">Rusak Sedang</option>
        </select>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6" id="location_id">
        <select name="location_id" id="location_id" class="form-control filter-control base-plugin--select2-ajax location_id"
            data-url="{{ route('ajax.selectStruct', ['search' => 'all']) }}"
            data-post="location_id"
            
            data-placeholder="{{ __('Struktur Organisasi') }}">
        </select>
    </div>

    <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
        <select name="room_location" class="form-control filter-control base-plugin--select2-ajax room_location"
            data-url="{{ rut('ajax.selectRooms', ['all']) }}"
            data-post="room_location"
            data-url-origin="{{ rut('ajax.selectRooms', ['all']) }}"
            placeholder="{{ __('Pilih Ruangan') }}">
            <option value="">{{ __('Pilih Salah Satu') }}</option>
        </select>
    </div>
</div>

<div class="alert alert-custom alert-light-primary fade show py-4" style="left:-30pt; margin-right:35px;" role="alert">
    <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
    <div class="alert-text text-primary">
        <div class="text-bold">{{ __('Informasi') }}:</div>
        <div class="mb-10px" style="white-space: pre-wrap;">Penghapusan Aset dapat dilakukan Ketika Kondisi Aset Rusak Berat dan Status Aset Actives</div>
        {{-- <div class="mb-3px" style="white-space: pre-wrap;">Perbaikan Aset dapat dilakukan Ketika Kondisi Awal Aset Baik dan Status Aset Actives</div> --}}
    </div>
</div>
@endsection
@section('buttons')
    <a href="{{ route($routes . '.export') }}" target="_blank" class="btn btn-info ml-2 export-excel-kib text-nowrap">
        <i class="far fa-file-excel mr-2"></i> Excel
    </a>
    <a href="{{ route($routes . '.kib-pdf') }}" target="_blank" class="btn btn-danger ml-2 export-pdf-kib text-nowrap">
        <i class="far fa-file-pdf mr-2"></i> Cetak KIB
    </a>
    <a href="{{ route($routes . '.kir-pdf') }}" target="_blank" class="btn btn-danger ml-2 export-pdf-kir text-nowrap">
        <i class="far fa-file-pdf mr-2"></i> Cetak KIR
    </a>
@endsection

{{-- @section('buttons')
	@if (auth()->user()->checkPerms($perms.'.create'))
        <a href="{{ $urlAdd ?? (\Route::has($routes.'.create') ? route($routes.'.create') : 'javascript:;') }}"
            class="btn btn-info base-modal--render"
            data-modal-size="{{ $modalSize ?? 'modal-lg' }}"
            data-modal-backdrop="false"
            data-modal-v-middle="false">
            <i class="fa fa-plus"></i> Data
        </a>
	@endif
@endsection --}}

@push('scripts')

<script>
    $('.content-page').on('click', ' .export-pdf-kir', function (e) {
        e.preventDefault();
        var me = $(this);
        var url = me.attr('href');
        var filters = {
            jenis_aset: $('.filter-control[data-post="jenis_aset"]').val(),
            status: $('.filter-control[data-post="status"]').val(),
            condition: $('.filter-control[data-post="condition"]').val(),
            location_id: $('.filter-control[data-post="location_id"]').val(),
            room_location: $('.filter-control[data-post="room_location"]').val(),
        }

        filters = $.param(filters);
        url = url+'?'+filters;
        console.log(url);

        window.open(url);
    });

    $('.content-page').on('click', ' .export-pdf-kib', function (e) {
        e.preventDefault();
        var me = $(this);
        var url = me.attr('href');
        var filters = {
            jenis_aset: $('.filter-control[data-post="jenis_aset"]').val(),
            status: $('.filter-control[data-post="status"]').val(),
            condition: $('.filter-control[data-post="condition"]').val(),
            location_id: $('.filter-control[data-post="location_id"]').val(),
            room_location: $('.filter-control[data-post="room_location"]').val(),
        }

        filters = $.param(filters);
        url = url+'?'+filters;
        console.log(url);

        window.open(url);
    });

    $('.content-page').on('click', ' .export-excel-kib', function (e) {
		e.preventDefault();
		var me = $(this);
		var url = me.attr('href');
		var filters = {
			jenis_aset: $('.filter-control[data-post="jenis_aset"]').val(),
			status: $('.filter-control[data-post="status"]').val(),
			condition: $('.filter-control[data-post="condition"]').val(),
			location_id: $('.filter-control[data-post="location_id"]').val(),
			room_location: $('.filter-control[data-post="room_location"]').val(),
		}

		filters = $.param(filters);
		url = url+'?'+filters;
		console.log(url);

		window.open(url);
	});
    
</script>
@endpush
