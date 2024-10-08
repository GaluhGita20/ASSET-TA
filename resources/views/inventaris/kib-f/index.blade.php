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
    </div>

@endsection
@section('buttons')
<a href="{{ route($routes . '.export') }}" target="_blank" class="btn btn-info ml-2 export-excel-kib text-nowrap">
    <i class="far fa-file-excel mr-2"></i> Excel
</a>
<a href="{{ route($routes . '.kib-pdf') }}" target="_blank" class="btn btn-danger ml-2 export-pdf-kib text-nowrap">
    <i class="far fa-file-pdf mr-2"></i> Cetak KIB
</a>
@endsection

<script>
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

</script>


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
