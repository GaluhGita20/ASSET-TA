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
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6" id="location_id">
			<select name="location_id" id="location_id" class="form-control filter-control base-plugin--select2-ajax"
				data-url="{{ route('ajax.selectStruct', ['search' => 'all']) }}"
				data-post="location_id"
				
				data-placeholder="{{ __('Struktur Organisasi') }}">
			</select>
		</div> --}}

		{{-- <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
			<select name="room_location" class="form-control filter-control base-plugin--select2-ajax locations"
				data-url="{{ rut('ajax.selectRooms', ['all']) }}"
				data-post="room_location"
				data-url-origin="{{ rut('ajax.selectRooms', ['all']) }}"
				placeholder="{{ __('Pilih Ruangan') }}">
				<option value="">{{ __('Pilih Salah Satu') }}</option>
			</select>
		</div> --}}
    </div>
	{{-- <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6"> --}}
	
	{{-- </div> --}}

<div class="card-body p-8">
	<div class="col-12">
		<div class="row card-progress-wrapper">
			<div class="col-xl-6 col-md-6 col-sm-12">
				<div class="card card-custom gutter-b card-stretch wave wave-primary"
				data-name="Perbaikan">
					<div class="card-body">
						<div class="d-flex flex-wrap align-items-center py-1">
							<div class="symbol symbol-40 symbol-light-warning mr-5">
								<span class="symbol-label shadow d-flex justify-content-center align-items-center" style="width: 100%; height: 100%; justify-content:center; align-items:center;">
									<i class="fa fa-box text-primary font-size-h5"></i>
								</span>
							</div>
							
							<div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
								<div class="text-dark font-weight-bolder font-size-h5" style="width: 100%; height: 100%;">
									Jumlah Aset Active
								</div>
								<div class="text-muted font-weight-bold font-size-lg" style="margin-top:3px;">
									<div class="d-flex justify-content-center align-items-center">
										<span class="text-nowrap">
											<h5 class="jums" id="jums" >{{number_format($jumlah, 0, ',', ',')}}</h5>
										</span>
									</div>
								</div>
							</div>

							<div class="d-flex flex-column w-100 mt-5">
								<div class="text-dark mr-2 font-size-lg font-weight-bolder pb-3">
									<div class="d-flex justify-content-between">
										{{-- <span class="percent-text">0%</span> --}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-6 col-md-4 col-sm-12">
				<div class="card card-custom gutter-b card-stretch wave wave-success"
				data-name="Perbaikan">
					<div class="card-body">
						<div class="d-flex flex-wrap align-items-center py-1">
							<div class="symbol symbol-40 symbol-light-warning mr-5">
								<span class="symbol-label shadow d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
									<i class="fa fa-money-bill text-success font-size-h5"></i>
								</span>
							</div>
							
							<div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
								<div class="text-dark font-weight-bolder font-size-h5" style="text-align: center">
									Total Nilai Aset
								</div>
								<div class="text-muted font-weight-bold font-size-lg" style="margin-top:3px;">
									<div class="d-flex justify-content-center align-items-center">
										<span class="text-nowrap">
											<h5 class="real" id="real">Rp. {{number_format($value, 0, ',', ',')}}</h5>
										</span>
									</div>
								</div>
							</div>
							<div class="d-flex flex-column w-100 mt-5">
								<div class="text-dark mr-2 font-size-lg font-weight-bolder pb-3">
									<div class="d-flex justify-content-between">
										{{-- <span class="percent-text">0%</span> --}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
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
