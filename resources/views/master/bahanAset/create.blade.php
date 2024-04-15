@extends('layouts.modal')

@section('action', route($routes.'.store'))
@section('modal-body')
    @method('POST')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Bahan Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input name="name" type="text" class="form-control" placeholder="{{ __('Bahan Aset') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Deskripsi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <textarea name="description" class="form-control" placeholder="{{ __('Deskripsi') }}"></textarea>
        </div>
    </div>
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
