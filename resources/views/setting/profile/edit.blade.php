@extends('layouts.modal')

@section('action', rut($routes.'.update', $record->id))

@section('modal-body')
	@method('PATCH')
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{{ __('Nama') }}</label>
		<div class="col-md-8 parent-group">
			<input type="text" name="name" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('NIK') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="text" name="nik" value="{{ $record->nik }}" class="form-control" placeholder="{{ __('NIK') }}">
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Email') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="email" name="email" value="{{ $record->email }}" class="form-control" placeholder="{{ __('Email') }}">
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Position') }}</label>
		<div class="col-sm-8 parent-group">
			<select name="position_id" class="form-control base-plugin--select2-ajax"
				data-url="{{ rut('ajax.selectPosition', 'all') }}"
				data-placeholder="{{ __('Position') }}">
				@if ($record->position)
					<option value="{{ $record->position->id }}" selected>{{ $record->position->name }}</option>
				@endif
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Hak Akses') }}</label>
		<div class="col-sm-8 parent-group">
			<select name="roles[]" class="form-control base-plugin--select2-ajax"
				data-url="{{ rut('ajax.selectRole', 'all') }}"
				data-placeholder="{{ __('Hak Akses') }}">
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@foreach ($record->roles as $val)
					<option value="{{ $val->id }}" selected>{{ $val->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('ID Telegram') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group"> 
			<input type="text" name="telegram_user_id" value="{{$record->telegram->user_id}}" class="form-control" placeholder="{{ __('ID Telegram') }}">
		</div>
	</div> --}}
@endsection
