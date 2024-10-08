@extends('layouts.modal')

@section('action', rut($routes.'.update', $record->id))

@section('modal-body')
	@method('PATCH')
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{{ __('Nama') }}</label>
		<div class="col-md-8 parent-group">
			<input type="text" name="name" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama') }}" disabled>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('NIK') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="text" name="nik" value="{{ $record->nik }}" class="form-control" placeholder="{{ __('NIK') }}" disabled>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Email') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="email" name="email" value="{{ $record->email }}" class="form-control" placeholder="{{ __('Email') }}" disabled>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Jabatan') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="text" name="position_id" value="{{ $record->position->name ?? '' }}" class="form-control" placeholder="{{ __('Jabatan') }}" disabled>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-4 col-form-label">{{ __('Hak Akses') }}</label>
		<div class="col-sm-8 parent-group">
			<input type="text" name="position_id" value="{{ implode(', ', $record->roles()->pluck('name')->toArray()) }}" class="form-control" placeholder="{{ __('Hak Akses') }}" disabled>
		</div>
	</div>
	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('ID Telegram') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group"> 
			<input type="text" name="telegram_user_id" value="{{$record->telegram->user_id}}" class="form-control" placeholder="{{ __('ID Telegram') }}" disabled>
		</div>
	</div> --}}
@endsection

@section('buttons')
@endsection
