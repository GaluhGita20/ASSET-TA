@extends('layouts.modal')

@section('action', route($routes.'.update',$record->id))
@section('modal-body')
    @method('PATCH')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Sumber Dana') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input type="text" name="name" class="form-control" placeholder="{{ __('Sumber Dana') }}" value="{{ $record->name }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Deskripsi') }}</label>
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
