@extends('layouts.modal')

@section('action', rut($routes.'.update', $record->id))

@section('modal-body')
	@method('PATCH')
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Parent') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="parent_id" class="form-control base-plugin--select2-ajax"
				data-url="{{ rut('ajax.selectStruct', 'parent_department') }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
				@if ($record->parent)
					<option value="{{ $record->parent->id }}" selected>{{ $record->parent->name }}</option>
				@endif
			</select>
			<div class="form-text text-muted">*Parent berupa Direktur</div>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Kode') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="code_manual" value="{{ $record->code }}" class="form-control" placeholder="{{ __('Kode') }}" disabled>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Telegram ID') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group"> 
			<input type="text" name="telegram_id" value="{{ $record->telegram_id }}" class="form-control" placeholder="{{ __('Telegram ID') }}">
		</div>
	</div>
@endsection
