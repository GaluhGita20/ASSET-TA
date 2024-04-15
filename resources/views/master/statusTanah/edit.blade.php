@extends('layouts.modal')

@section('action', route($routes.'.update',$record->id))
@section('modal-body')
    @method('PATCH')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Status Tanah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input type="text" name="name" class="form-control" placeholder="{{ __('Status Tanah') }}" value="{{ $record->name }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Deskripsi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <textarea name="description" value="{{ $record->description }}" class="form-control" placeholder="{{ __('Deskripsi') }}">{{ $record->description }}</textarea>
        </div>
    </div>
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
