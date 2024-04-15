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
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Struktur') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
				data-url="{{ route('ajax.selectStruct', ['by_level']) }}"
                data-url-origin="{{ route('ajax.selectStruct', ['by_level']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
				@if ($record->location_id)
					<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
				@endif
			</select>
		</div>
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
			$('.content-page').on('change', 'select.level_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.location_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({level_id: me.val()});
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
