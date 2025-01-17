@extends('layouts.modal')

@section('action', rut($routes.'.store'))

@section('modal-body')
	@method('POST')
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Parent') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="parent_id" class="form-control base-plugin--select2-ajax"
				data-url="{{ rut('ajax.selectStruct', 'parent_bod') }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
			</select>
			<div class="form-text text-muted">*Parent berupa Root/Direktur</div>
		</div>
	</div>
	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Kode') }}</label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="code" class="form-control" placeholder="{{ __('Kode') }}">
		</div>
	</div> --}}
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Telegram ID') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group"> 
			<input type="text" name="telegram_id" class="form-control" placeholder="{{ __('Telegram ID') }}">
		</div>
	</div>

	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Telegram Group') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="grup_telegram" class="form-control" placeholder="{{ __('Group Telegram') }}">
		</div>
	</div> --}}
@endsection
