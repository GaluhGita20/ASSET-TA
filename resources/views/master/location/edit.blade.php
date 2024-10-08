@extends('layouts.modal')
@section('action', rut($routes.'.update', $record->id))
@section('modal-body')
@method('PATCH')
    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Nama') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-9 parent-group">
			<input type="text" name="name" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama Ruang') }}" >
		</div>
	</div>
    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Kode') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-9 parent-group">
			<input type="text" name="space_code"  value="{{ $record->space_code }}" class="form-control" placeholder="{{ __('Kode Ruang') }}" >
		</div>
	</div>
	<div class="form-group row">
        <label class="col-3 col-form-label">{{ __('Posisi Lantai') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-9 parent-group">
        <select class="form-control base-plugin--select2-ajax"  name="floor_position" data-placeholder="Posisi Lantai" >
            <option value="1" {{ $record->floor_position == 1 ? 'selected':'' }}>1</option>
            <option value="2" {{ $record->floor_position == 2 ? 'selected':'' }}>2</option>
            <option value="3" {{ $record->floor_position == 3 ? 'selected':'' }}>3</option>
            <option value="4" {{ $record->floor_position == 4 ? 'selected':'' }}>4</option>
            <option value="5" {{ $record->floor_position == 5 ? 'selected':'' }}>5</option>
            <option value="6" {{ $record->floor_position == 6 ? 'selected':'' }}>6</option>
        </select>
        </div>
    </div>


	<div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Struktur') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-9 parent-group">
			<select name="departemen_id" class="form-control base-plugin--select2-ajax departemen_id"
				data-url="{{ route('ajax.selectStruct', ['alls']) }}"
                data-url-origin="{{ route('ajax.selectStruct', ['alls']) }}"
				data-placeholder="{{ __('Pilih Struktur Organisasi') }}"
				@if($record->departemen_id == NULL) disabled @endif>
				<option value="">{{ __('Pilih Struktur Organisasi') }}</option>
				<option value="{{ $record->orgLocation->id }}" selected>{{ $record->orgLocation->name }}</option>
			</select>
		</div>
	</div>

    <div class="form-group row">
		<label class="col-3 col-form-label">{{ __('Penanggung Jawab') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-9 parent-group">
			<select name="pic_id" class="form-control base-plugin--select2-ajax pic_id"
				data-url="{{ route('ajax.selectUser', ['org_struct']) }}"
                data-url-origin="{{ route('ajax.selectUser', ['org_struct']) }}"
				data-placeholder="{{ __('Pilih Penangung Jawab Ruang') }}" 
				@if($record->pic_id == NULL) disabled @endif>
				<option value="">{{ __('Pilih Struktur Organisasi') }}</option>
				<option value="{{ $record->user->id }}" selected>{{ $record->user->name }}</option>
			</select>
		</div>
	</div>
@endsection


@push('scripts')
	<script>
		$(function () {
            $('.content-page').on('change', 'select.departemen_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.pic_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({org_struct: me.val()});
                    console.log(urlParam);
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});
		});
	</script>

	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>

@endpush
