@extends('layouts.modal')

@section('action', rut($routes.'.update', $record->id))

@section('modal-body')
	@method('PATCH')
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Nama') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="name" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Email') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="email" value="{{ $record->email }}" class="form-control" placeholder="{{ __('Email') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Website') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="website" value="{{ $record->website }}" class="form-control" placeholder="{{ __('Website') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Telepon') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="phone" value="{{ $record->phone }}" class="form-control" placeholder="{{ __('Telepon') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Alamat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-9 parent-group">
			<textarea type="text" name="address" class="form-control" placeholder="{{ __('Address') }}">{{ $record->address }}</textarea>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{{ __('Provinsi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-md-9 parent-group">
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
					<option value="{{ $record->city->province_id }}" selected>{{ $record->city->province->name }}</option>
				@endif
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{{ __('Kota') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-md-9 parent-group">
			<select name="city_id" class="form-control base-plugin--select2-ajax city_id"
				data-url="{{ rut('ajax.selectCity', ['province_id']) }}"
				data-url-origin="{{ rut('ajax.selectCity', ['province_id']) }}"
				placeholder="{{ __('Pilih Salah Satu') }}" required>
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@if (!empty($record->city_id))
					<option value="{{ $record->city_id }}" selected>{{ $record->city->name }}</option>
				@endif
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{{ __('Daerah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-md-9 parent-group">
			<select name="district_id" class="form-control base-plugin--select2-ajax district_id"
				data-url="{{ rut('ajax.selectDistrict', ['city_id']) }}"
				data-url-origin="{{ rut('ajax.selectDistrict', ['city_id']) }}"
				placeholder="{{ __('Pilih Salah Satu') }}" disabled required>
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@if (!empty($record->district_id))
					<option value="{{ $record->district_id }}" selected>{{ $record->daerah->name }}</option>
				@endif
			</select>
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
					console.log(objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam))));
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
