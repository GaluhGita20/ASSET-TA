@extends('layouts.modal')

@section('action', rut($routes.'.store'))

@section('modal-body')
	@method('POST')
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{{ __('Nama') }}</label>
		<div class="col-md-8 parent-group">
			<input type="text" name="name" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('NIK') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="text" name="nik" class="form-control" placeholder="{{ __('NIK') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Email') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="email" name="email" class="form-control" placeholder="{{ __('Email') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Password') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Konfirmasi Password') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('Konfirmasi Password') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Position') }}</label>
		<div class="col-sm-8 parent-group">
			<select name="position_id" class="form-control base-plugin--select2-ajax"
				data-url="{{ rut('ajax.selectPosition', 'all') }}"
				placeholder="{{ __('Position') }}">
				<option value="">{{ __('Pilih Jabatan') }}</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Hak Akses') }}</label>
		<div class="col-sm-8 parent-group">
			<select name="roles[]" class="form-control base-plugin--select2-ajax"
				data-url="{{ rut('ajax.selectRole', 'all') }}"
				placeholder="{{ __('Hak Akses') }}">
				<option value="">{{ __('Pilih Hak Akses') }}</option>
			</select>
		</div>
	</div>
	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('ID Telegram') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="telegram_user_id" class="form-control" placeholder="{{ __('ID Telegram') }}">
		</div>
	</div> --}}
@endsection
