@extends('layouts.modal')

@section('action', rut($routes.'.update', $record->id))

@section('modal-body')
	@method('PATCH')
	<div class="form-group row">
        <label class="col-sm-12 col-form-label">{{ __('Level Tingkatan Organisasi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 parent-group">
        <select class="form-control base-plugin--select2-ajax level" name="level" data-placeholder="Level Organisasi">
            <option value="kepala" {{ $record->location->level == "kepala" ? 'selected':'' }}>Kepala</option>
            <option value="wakil kepala" {{ $record->location->level == "wakil kepala" ? 'selected':'' }}>Wakil Kepala</option>
            <option value="staf" {{ $record->location->level =="staf" ? 'selected':'' }}>Staf</option>
        </select>
        </div>
    </div>

	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Instansi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="root_id" id="root_id" class="form-control base-plugin--select2-ajax root_id"
				data-url="{{ route('ajax.selectStruct', ['parent_bod']) }}"
				data-url-origin="{{ route('ajax.selectStruct', ['parent_bod']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}" disabled>
				{{-- <option value="">{{ __('Pilih Salah Satu') }}</option> --}}
				{{-- @if ($record->code >= 1001 && $record->code <= 2000)
					@php
						$name = \App\Models\Master\Org\OrgStruct::where('id',1)->value('name');
					@endphp
					<option value=1 selected>{{$name}}</option>
				@else
					@php
						$name = \App\Models\Master\Org\OrgStruct::where('id',18)->value('name');
					@endphp
					<option value=18 selected>{{$name}}</option>
				@endif
			
			</select>
		</div>
	</div> --}} 
	
	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Struktur') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="location_id" id="location_id" class="form-control base-plugin--select2-ajax location_id"
				data-url="{{ route('ajax.selectDeps', ['18']) }}"
				data-url-origin="{{ route('ajax.selectDeps', ['18']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@if ($record->location_id)
					<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
				@endif
			</select>
		</div>
	</div> --}}

	<div class="form-group row">
		<label class="col-md-12 col-form-label">{{ __('Instansi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-md-12 parent-group">
			<select name="root_id" id="root_id" class="form-control base-plugin--select2-ajax root_id"
				data-url="{{ rut('ajax.selectStruct', [
					'parent_bod'
				]) }}"
				data-url-origin="{{ rut('ajax.selectStruct', [
					'parent_bod'
				]) }}"
				disabled>
				<option value="">{{ __('Pilih Salah Satu') }}</option>
				@if ($record->code >= 1001 && $record->code <= 2000)
					@php
						$name = \App\Models\Master\Org\OrgStruct::where('id',1)->value('name');
					@endphp
					<option value=1 selected>{{$name}}</option>
				@else
					@php
						$name = \App\Models\Master\Org\OrgStruct::where('id',18)->value('name');
					@endphp
					<option value=18 selected>{{$name}}</option>
				@endif
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-md-12 col-form-label">{{ __('Struktur Organisasi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-md-12 parent-group">
			@if ($record->code >= 1001 && $record->code <= 2000)
				<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
					data-url="{{ rut('ajax.selectDepsRSUD') }}"
					data-url-origin="{{ rut('ajax.selectDepsRSUD') }}"
					placeholder="{{ __('Pilih Salah Satu')}}">
					<option value="">{{ __('Pilih Salah Satu') }}</option>
					@if ($record->location_id)
						<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
					@endif
				</select>
			@elseif($record->code >= 2001 && $record->code <= 3000)
				<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
					data-url="{{ rut('ajax.selectDepsBPKAD') }}"
					data-url-origin="{{ rut('ajax.selectDepsBPKAD') }}"
					placeholder="{{ __('Pilih Salah Satu')}}">
					<option value="">{{ __('Pilih Salah Satu') }}</option>
					@if ($record->location_id)
						<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
					@endif
				</select>
			@else
				<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
					data-url="{{ rut('ajax.selectDeps',[root_id]) }}"
					data-url-origin="{{ rut('ajax.selectDeps',[root_id]) }}"
					placeholder="{{ __('Pilih Salah Satu')}}">
					<option value="">{{ __('Pilih Salah Satu') }}</option>
					@if ($record->location_id)
						<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
					@endif
				</select>
			@endif
		</div>
		{{-- <div class="form-group row">
			<div class="col-sm-12 offset-md-1 col-md-12">
				<span style="font-size: 11px">{{ __('*Pilih instansi terlebih dahulu untuk dapat memilih Struktur Organisasi') }}</span>
			</div>
		</div> --}}
	</div>

	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama Jabatan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" class="form-control" value="{{ $record->name }}" placeholder="{{ __('Nama') }}">
		</div>
	</div>
@endsection

@push('scripts')

	<script>
		$(function () {
			$('.content-page').on('change', 'select.root_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.location_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({root_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});

		});
	</script>

@endpush
