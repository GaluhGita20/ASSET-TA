@extends('layouts.modal')
@section('action', rut($routes.'.store'))
@section('modal-body')
@method('POST')

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
				<option value="{{ $record->city->ref_province_id }}" selected>{{ $record->city->provinsi->name }}</option>
			@endif
		</select>
	</div>
</div>

<div class="form-group row">
	<label class="col-md-12 col-form-label">{{ __('Kota') }}<span style=" color: red;margin-left: 5px;">*</span></label>
	<div class="col-md-12 parent-group">
		<select name="city_id" class="form-control base-plugin--select2-ajax city_id"
			data-url="{{ rut('ajax.selectCity', ['province_id']) }}"
			data-url-origin="{{ rut('ajax.selectCity', ['province_id']) }}"
			placeholder="{{ __('Pilih Salah Satu') }}" disabled required>
			<option value="">{{ __('Pilih Salah Satu') }}</option>
			@if (!empty($record->city_id))
				<option value="{{ $record->ref_city_id }}" selected>{{ $record->kota->name }}</option>
			@endif
		</select>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-12 col-form-label">{{ __('Kecamatan') }} <span class="text-danger"> *</span> </label>
	<div class="col-sm-12 parent-group">
		<input type="text" name="name" class="form-control" required placeholder="{{ __('Kecamatan') }}">
	</div>
</div>
<div class="form-group row">
	<label class="col-sm-12 col-form-label">{{ __('Kode') }} <span class="text-danger"> *</span> </label>
	<div class="col-sm-12 parent-group">
		<input type="number" name="code" class="form-control" required placeholder="{{ __('Kode') }}">
	</div>
</div>
@endsection


@push('scripts')
	<script>
		$(function () {
			$('.content-page').on('change', 'select.province_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.city_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({province_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                    objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});

            $('.content-page').on('change', 'select.city_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.district_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({city_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                    objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});

		});
	</script>

	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>

@endpush
