
@extends('layouts.modal')


@section('modal-body')
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Nama') }}</label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="name" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama') }}" readonly>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Email') }}</label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="email" value="{{ $record->email }}" class="form-control" placeholder="{{ __('Email') }}" readonly>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Website') }}</label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="website" value="{{ $record->website }}" class="form-control" placeholder="{{ __('Website') }}" readonly>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Telepon') }}</label>
		<div class="col-sm-9 parent-group">
			<input type="text" name="phone" value="{{ $record->phone }}" class="form-control" placeholder="{{ __('Telepon') }}" readonly>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label">{{ __('Alamat') }}</label>
		<div class="col-sm-9 parent-group">
			<textarea type="text" name="address" class="form-control" placeholder="{{ __('Address') }}" readonly>{{ $record->address }}</textarea>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{{ __('Provinsi') }}</label>
		<div class="col-md-9 parent-group">
			<select class="form-control base-plugin--select2-ajax province_id"
				data-url="{{ rut('ajax.selectProvince', [
					'search'=>'all'
				]) }}"
				data-url-origin="{{ rut('ajax.selectProvince', [
					'search'=>'all'
				]) }}"
				placeholder="{{ __('Pilih Salah Satu') }}" required disabled>
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@if (!empty($record->city_id))
					<option value="{{ $record->city->province_id }}" selected>{{ $record->city->province->name }}</option>
				@endif
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{{ __('Kota') }}</label>
		<div class="col-md-9 parent-group">
			<select name="city_id" class="form-control base-plugin--select2-ajax city_id"
				data-url="{{ rut('ajax.selectCity', ['province_id']) }}"
				data-url-origin="{{ rut('ajax.selectCity', ['province_id']) }}"
				placeholder="{{ __('Pilih Salah Satu') }}" disabled>
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@if (!empty($record->city_id))
					<option value="{{ $record->city_id }}" selected>{{ $record->city->name }}</option>
				@endif
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{{ __('Daerah') }}</label>
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

@section('buttons')
@endsection
