@extends('layouts.modal')

@if ($page_action == "show")
    @section('action', route($routes.'.index'))
@elseif ($page_action == "edit")
    @section('action', route($routes.'.update', $record->id))
@endif

@section('modal-body')
    @if ($page_action == "edit")
        @method('PUT')
    @endif

    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Nama') }}</label>
		<div class="col-9 parent-group">
			<input name="name" class="form-control" placeholder="{{ __('Nama') }}" {{$page_action == "show" ? "readonly" : ""}}
			value="{{$record->name}}"
			>
		</div>
	</div>
    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Keterangan') }}</label>
		<div class="col-9 parent-group">
			<textarea name="description" class="form-control" placeholder="{{ __('Keterangan') }}" {{$page_action == "show" ? "readonly" : ""}}
		>{!! $record->description !!}</textarea>
		</div>
	</div>
@endsection

@if (!in_array($page_action, ["edit"]))
	@section('buttons')
	@endsection
@endif

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
