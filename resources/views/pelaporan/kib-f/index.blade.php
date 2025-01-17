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
												<h5 class="jumlahs" id="jumlahs" >{{number_format($jumlah, 0, ',', ',')}}</h5>
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
												<h5 class="biaya" id="biaya">Rp. {{number_format($value, 0, ',', ',')}}</h5>
											</span>
										</div>
									</div>
								</div>
								<div class="d-flex flex-column w-100 mt-5">
									<div class="text-dark mr-2 font-size-lg font-weight-bolder pb-3">
										<div class="d-flex justify-content-between">
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

	$(function () {
        // const yearSelect = document.getElementById('yearSelect');
        var org = null;
        var ruang = null;

        $('.content-page').on('change', 'select.location_id', function (e) {
            org = $(this);
            console.log(org.val());
			$.ajax({
					type: 'POST',
					url: '/ajax/getLapAsetKIBB',
					data: {
						_token: BaseUtil.getToken(),
						val1: org.val(),
						val2: null,
						val3:'KIB F',
					},
					success: function(resp) {
						var jumlah = resp.jumlah;
						var biaya = resp.value;
						
						
						$('.jumlahs').text(jumlah);
						$('.biaya').text(biaya.toLocaleString('id-ID', {
							style: 'currency',
							currency: 'IDR'
						}));
	
						console.log(resp);
					},
					error: function(resp) {
						console.log(resp)
						console.log('error')
					},
				});
        });

        $('.content-page').on('change', 'select.room_location', function (e) {
            ruang = $(this);
			console.log(ruang.val());

			$.ajax({
					type: 'POST',
					url: '/ajax/getLapAsetKIBB',
					data: {
						_token: BaseUtil.getToken(),
						val1: null,
						val2: ruang.val(),
						val3:'KIB F',
					},
					success: function(resp) {
						var jumlah = resp.jumlah;
						var biaya = resp.value;
						
						
						$('.jumlahs').text(jumlah);
						$('.biaya').text(biaya.toLocaleString('id-ID', {
							style: 'currency',
							currency: 'IDR'
						}));
	
						console.log(resp);
					},
					error: function(resp) {
						console.log(resp)
						console.log('error')
					},
				});
        });

        $('.content-page').on('change', 'select.location_id, select.room_location', function (e) {
			// console.log(org.val, ruang.val)
            if (org.val() != null && org.val() != null && ruang != null && ruang.val() != null) {
					$.ajax({
					type: 'POST',
					url: '/ajax/getLapAsetKIBB',
					data: {
						_token: BaseUtil.getToken(),
						val1: org.val(),
						val2: ruang.val(),
						val3:'KIB F',
					},
					success: function(resp) {
						var jumlah = resp.jumlah;
						var biaya = resp.value;
						
						
						$('.jumlahs').text(jumlah);
						$('.biaya').text(biaya.toLocaleString('id-ID', {
							style: 'currency',
							currency: 'IDR'
						}));
	
						console.log(resp);
					},
					error: function(resp) {
						console.log(resp)
						console.log('error')
					},
				});
            }
        });



    });

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
