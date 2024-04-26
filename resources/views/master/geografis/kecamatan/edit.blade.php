@extends('layouts.modal')
@section('action', rut($routes.'.update', $record->id))
@section('modal-body')
@method('PUT')
<div class="form-group row">
	<label class="col-md-12 col-form-label">{{ __('Provinsi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
	<div class="col-md-12 parent-group">
		<select name="province_id" class="form-control base-plugin--select2-ajax province_id"
			data-url="{{ rut('ajax.selectProvince', [
				'search'=>'all'
			]) }}"
			data-url-origin="{{ rut('ajax.selectProvince', [
				'search'=>'all'
			]) }}"
			placeholder="{{ __('Pilih Salah Satu') }}" required>
			<option value="">{{ __('Pilih Salah Satu') }}</option>
			@if (!empty($record->city_id))
				<option value="{{ $record->city->province_id }}" selected>{{ $record->city->province->name}}</option>
			@endif
		</select>
	</div>
</div>

<div class="form-group row">
	<label class="col-md-12 col-form-label">{{ __('Kota') }}<span style=" color: red;margin-left: 5px;">*</span></label>
	<div class="col-md-12 parent-group">
		<select name="city_id" class="form-control base-plugin--select2-ajax city_id"
			data-url="{{ rut('ajax.selectCity', ['province_id']) }}"
			data-url-origin="{{ rut('ajax.selectCity',['province_id']) }}"
			placeholder="{{ __('Pilih Salah Satu') }}" required>
			<option value="">{{ __('Pilih Salah Satu') }}</option>
			@if (isset($record) && ($city = $record->city))
				<option value="{{ $city->id }}" selected>{{ $city->name }}</option>
			@endif
		</select>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-12 col-form-label">{{ __('Kecamatan') }} <span class="text-danger"> *</span> </label>
	<div class="col-sm-12 parent-group">
		<input type="text" name="name" required value="{{ $record->name }}" class="form-control" placeholder="{{ __('Kecamatan') }}">
	</div>
</div>
<div class="form-group row">
	<label class="col-sm-12 col-form-label">{{ __('Kode') }} <span class="text-danger"> *</span> </label>
	<div class="col-sm-12 parent-group">
		<input type="number" name="code" value="{{ $record->code }}" class="form-control" required placeholder="{{ __('Kode') }}">
	</div>
</div>
@endsection


@push('scripts')

	{{-- <script>
		$(function () {
			$('.content-page').on('change', 'select.province_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.city_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({province_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});

		});

        $(function () {
			$('.content-page').on('change', 'select.city_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.district_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({city_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});
		});
        
	</script> --}}


	<script>
		$(document).ready(function(){
    // Mengisi dropdown Provinsi
    $.get('/get-provinces', function(data){
        $('.province_id').empty();
        $('.province_id').append('<option value="">Pilih Provinsi</option>');
        $.each(data, function(index, province){
            $('.province_id').append('<option value="'+province.id+'">'+province.name+'</option>');
        });
        $('.province_id').val({{ $record->city->province_id ?? '' }}); // Menetapkan nilai awal
    });

    // Mengisi dropdown Kabupaten/Kota berdasarkan Provinsi yang dipilih
    $('.province_id').change(function(){
        var provinceId = $(this).val();
        $.get('/get-regencies/' + provinceId, function(data){
            $('.city_id').empty();
            $('.city_id').append('<option value="">Pilih Kota/Kabupaten</option>');
            $.each(data, function(index, regency){
                $('.city_id').append('<option value="'+regency.id+'">'+regency.name+'</option>');
            });
        });
    });

    // Mengisi dropdown Kota/Kabupaten berdasarkan Kabupaten yang dipilih
    $('.city_id').change(function(){
        var regencyId = $(this).val();
        $.get('/get-cities/' + regencyId, function(data){
            $('.city_id').empty();
            $('.city_id').append('<option value="">Pilih Kota/Kabupaten</option>');
            $.each(data, function(index, city){
                $('.city_id').append('<option value="'+city.id+'">'+city.name+'</option>');
            });
        });
    });

    // Menjalankan event change dropdown Kabupaten saat pertama kali
    $('.province_id').trigger('change');

    // Menjalankan event change dropdown Kota saat pertama kali
    $('.city_id').trigger('change');
});

	</script>

		

	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>

@endpush
