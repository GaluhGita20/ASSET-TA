@extends('layouts.modal')
@section('action', rut($routes.'.update', $record->id))
@section('modal-body')
@method('PUT')
<div class="form-group row">
	<label class="col-md-12 col-form-label">{{ __('Province') }}<span style=" color: red;margin-left: 5px;">*</span></label>
	<div class="col-md-12 parent-group">
		<select name="province_id" class="form-control base-plugin--select2-ajax"
			data-url="{{ rut('ajax.selectProvince', [
				'search'=>'all'
			]) }}"
			placeholder="{{ __('Pilih Salah Satu') }}">
			<option value="">{{ __('Pilih Salah Satu') }}</option>
			@if (isset($record) && ($province = $record->province))
				<option value="{{ $province->id }}" selected>{{ $province->name }}</option>
			@endif
		</select>
	</div>
</div>
<div class="form-group row">
	<label class="col-sm-12 col-form-label">{{ __('Kota / Kabupaten') }} <span class="text-danger"> *</span> </label>
	<div class="col-sm-12 parent-group">
		<input type="text" name="name" required value="{{ $record->name }}" class="form-control" placeholder="{{ __('Kota / Kabupaten') }}">
	</div>
</div>
<div class="form-group row">
	<label class="col-sm-12 col-form-label">{{ __('Kode') }} <span class="text-danger"> *</span> </label>
	<div class="col-sm-12 parent-group">
		<input type="number" name="code" value="{{ $record->code }}" class="form-control" required placeholder="{{ __('Kode') }}">
	</div>
</div>
@endsection
