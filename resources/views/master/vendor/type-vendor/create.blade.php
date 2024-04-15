@extends('layouts.modal')

@section('action', route($routes.'.store'))

@section('modal-body')
	@method('POST')
    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Nama') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-9 parent-group">
			<input type="text" name="name" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Keterangan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-9 parent-group">
			<textarea type="text" name="description" class="form-control" placeholder="{{ __('Keterangan') }}"></textarea>
		</div>
	</div>
@endsection
