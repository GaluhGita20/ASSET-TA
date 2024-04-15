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
				<option value="notactive">Not active</option>
                <option value="diputihkan">Diputihkan</option>
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
			<select name="location_id" id="location_id" class="form-control filter-control base-plugin--select2-ajax"
				data-url="{{ route('ajax.selectStruct', ['search' => 'all']) }}"
				data-post="location_id"
				
				data-placeholder="{{ __('Struktur Organisasi') }}">
			</select>
		</div>

		<div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
			<select name="room_location" class="form-control filter-control base-plugin--select2-ajax locations"
				data-url="{{ rut('ajax.selectRooms', ['all']) }}"
				data-post="room_location"
				data-url-origin="{{ rut('ajax.selectRooms', ['all']) }}"
				placeholder="{{ __('Pilih Ruangan') }}">
				<option value="">{{ __('Pilih Salah Satu') }}</option>
			</select>
		</div>
    </div>
	{{-- <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6"> --}}
	<div class="alert alert-custom alert-light-primary fade show" style="left:-30pt; margin-right:35px;" role="alert">
        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
        <div class="alert-text text-primary">
            <div class="text-bold">{{ __('Informasi') }}:</div>
            <div class="mb-10px" style="white-space: pre-wrap;">Penghapusan Aset dapat dilakukan Ketika Kondisi Aset Rusak Berat dan Status Aset Actives</div>
			{{-- <div class="mb-3px" style="white-space: pre-wrap;">Perbaikan Aset dapat dilakukan Ketika Kondisi Awal Aset Baik dan Status Aset Actives</div> --}}
        </div>
    </div>
	{{-- </div> --}}
@endsection


@push('scripts')

	<script>
	var $loc;
    var objectId = $('select.locations');
    
	// $loc = document.getElementById('location_id');
	if ($('#departemen_id').length > 0) {
        $loc = document.getElementById('location_id');
    }
	if ($loc) {
		console.log($loc);
	$('.content-page').on('change', 'select.location_id', function (e) {
		handleDepartemenChange($loc, objectId);
	});
	}
    
    // if ($loc) {
    //     $('.content-page').on('change', 'select.location_id', function (e) {
    //         handleDepartemenChange($loc, objectId);
    //     });
    // }

	//console.log($loc);
    function handleDepartemenChange(loc, objectId) {
        var urlOrigin = objectId.data('url-origin');
        var urlParam = $.param({ location_id: loc.value });
        objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        console.log(decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        objectId.val(null).prop('disabled', false);
        BasePlugin.initSelect2();
    }

	</script>
@endpush
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
