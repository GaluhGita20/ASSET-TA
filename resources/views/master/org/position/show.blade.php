@extends('layouts.modal')

@section('action', rut($routes.'.update', $record->id))

@section('modal-body')
	@method('PATCH')
	<div class="form-group row">
        <label class="col-sm-12 col-form-label">{{ __('Level Tingkatan Organisasi') }}</label>
        <div class="col-sm-12 parent-group">
        <select class="form-control base-plugin--select2-ajax level_id" name="level_id" data-placeholder="Level Organisasi">
            <option value="bod" {{ $record->level == "bod" ? 'selected':'' }}>Penanggung Jawab</option>
            <option value="department" {{ $record->level == "departement" ? 'selected':'' }}>Departement</option>
            <option value="subdepartment" {{ $record->level =="subdepartement" ? 'selected':'' }}>Sub Departement</option>
        </select>
        </div>
    </div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Struktur') }}</label>
		<div class="col-sm-12 parent-group">
			<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
				data-url="{{ route('ajax.selectLevelJabatan', ['by_level']) }}"
                data-url-origin="{{ route('ajax.selectLevelJabatan', ['by_level']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
				@if ($record->location_id)
					<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
				@endif
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama') }}</label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" class="form-control" value="{{ $record->name }}" placeholder="{{ __('Nama') }}" disabled>
		</div>
	</div>
@endsection

