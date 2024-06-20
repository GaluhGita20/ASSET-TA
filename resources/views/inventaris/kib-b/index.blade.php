@extends('layouts.lists')
@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="jenis_aset" placeholder="{{ __('Nama Aset') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
			<select class="form-control base-plugin--select2-ajax filter-control stat"
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
			<select class="form-control base-plugin--select2-ajax filter-control conds"
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
    
    function handleDepartemenChange(loc, objectId) {
        var urlOrigin = objectId.data('url-origin');
        var urlParam = $.param({ location_id: loc.value });
        objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        console.log(decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        objectId.val(null).prop('disabled', false);
        BasePlugin.initSelect2();
    }

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

	//fiter cetak KIR
	// $(function () {
	// 	var org = null;
	// 	var con = null;
	// 	var stat = null;
	// 	var room = null;
		
	// 	$('.content-page').on('click', 'select.stats', function (e) {
	// 		year = $(this);
	// 		// console.log(org.val());
	// 		$.ajax({
	// 				type: 'POST',
	// 				url: '/ajax/getLapPemeliharaan',
	// 				data: {
	// 					_token: BaseUtil.getToken(),
	// 					val1: year.val(),
	// 					val2: null,
	// 					val3:null,
	// 				},
	// 				success: function(resp) {
	// 					var jumlah = resp.jumlah;
	// 					var biaya = resp.value;
						
						
	// 					$('.jums').text(jumlah);
	// 					$('.biaya').text(biaya);
		
	// 					console.log(resp);
	// 				},
	// 				error: function(resp) {
	// 					console.log(resp)
	// 					console.log('error')
	// 				},
	// 			});
	// 	});
		
	// 	$('.content-page').on('change', 'select.org', function (e) {
	// 		org = $(this);
	// 		// console.log(ruang.val());
		
	// 		$.ajax({
	// 				type: 'POST',
	// 				url: '/ajax/getLapPemeliharaan',
	// 				data: {
	// 					_token: BaseUtil.getToken(),
	// 					val1: new Date().getFullYear(),
	// 					val2: org.val(),
	// 					val3: null,
		
	// 				},
	// 				success: function(resp) {
	// 					var jumlah = resp.jumlah;
	// 					var biaya = resp.value;
						
						
	// 					$('.jums').text(jumlah);
	// 					$('.biaya').text(biaya);
		
	// 					console.log(resp);
	// 				},
	// 				error: function(resp) {
	// 					console.log(resp)
	// 					console.log('error')
	// 				},
	// 			});
	// 	});
		
	// 	$('.content-page').on('change', 'select.month', function (e) {
	// 		mon = $(this);
	// 		// console.log(ruang.val());
		
			
	// 	});
		
		
	// 	$('.content-page').on('change', 'select.org, select.yearSelect', function (e) {
	// 		// console.log(org.val, ruang.val)
	// 		if (org != null && org.val() != null && year != null && year.val() != null) {
	// 				$.ajax({
	// 				type: 'POST',
	// 				url: '/ajax/getLapPemeliharaan',
	// 				data: {
	// 					_token: BaseUtil.getToken(),
	// 					val1: year.val(),
	// 					val2: org.val(),
	// 					val3: null,
	// 				},
	// 				success: function(resp) {
	// 					var jumlah = resp.jumlah;
	// 					var biaya = resp.value;
						
						
	// 					$('.jums').text(jumlah);
	// 					$('.biaya').text(biaya);
		
	// 					console.log(resp);
	// 				},
	// 				error: function(resp) {
	// 					console.log(resp)
	// 					console.log('error')
	// 				},
	// 			});
	// 		}
	// 	});
		
		
	// 	$('.content-page').on('change', 'select.month, select.yearSelect', function (e) {
	// 		// console.log(org.val, ruang.val)
	// 		if (mon != null && mon.val() != null && year != null && year.val() != null) {
	// 				$.ajax({
	// 				type: 'POST',
	// 				url: '/ajax/getLapPemeliharaan',
	// 				data: {
	// 					_token: BaseUtil.getToken(),
	// 					val1: year.val(),
	// 					val2: null,
	// 					val3: mon.val(),
	// 				},
	// 				success: function(resp) {
	// 					var jumlah = resp.jumlah;
	// 					var biaya = resp.value;
						
						
	// 					$('.jums').text(jumlah);
	// 					$('.biaya').text(biaya);
		
	// 					console.log(resp);
	// 				},
	// 				error: function(resp) {
	// 					console.log(resp)
	// 					console.log('error')
	// 				},
	// 			});
	// 		}
	// 	});
		
	// 	$('.content-page').on('change', 'select.month, select.yearSelect, select.org', function (e) {
	// 		// console.log(org.val, ruang.val)
	// 		if (mon != null && mon.val() != null && year != null && year.val() != null && org != null && org.val() != null) {
	// 				$.ajax({
	// 				type: 'POST',
	// 				url: '/ajax/getLapPemeliharaan',
	// 				data: {
	// 					_token: BaseUtil.getToken(),
	// 					val1: year.val(),
	// 					val2: org.val(),
	// 					val3: mon.val(),
	// 				},
	// 				success: function(resp) {
	// 					var jumlah = resp.jumlah;
	// 					var biaya = resp.value;
						
						
	// 					$('.jums').text(jumlah);
	// 					$('.biaya').text(biaya);
		
	// 					console.log(resp);
	// 				},
	// 				error: function(resp) {
	// 					console.log(resp)
	// 					console.log('error')
	// 				},
	// 			});
	// 		}
	// 	});
		
		
	// 	});
	// 
	</script>
@endpush



