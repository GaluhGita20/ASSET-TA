@extends('layouts.modal')

@section('modal-body')

	<div class="form-group row">
        <label class="col-sm-12 col-form-label">{{ __('Level Tingkatan Organisasi') }}</label>
        <div class="col-sm-12 parent-group">
        <select class="form-control base-plugin--select2-ajax level_id" name="level_id" data-placeholder="Level Organisasi" disabled>
            <option value="bod" {{ $record->location->level == "bod" ? 'selected':'' }}>Direksi</option>
            <option value="department" {{ $record->location->level == "department" ? 'selected':'' }}>Departement</option>
            <option value="subdepartment" {{ $record->location->level =="subdepartment" ? 'selected':'' }}>Sub Departement</option>
        </select>
        </div>
    </div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Struktur') }}</label>
		<div class="col-sm-12 parent-group">
			<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
				data-url="{{ route('ajax.selectStruct', ['by_level']) }}"
                data-url-origin="{{ route('ajax.selectStruct', ['by_level']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}" disabled>
				@if ($record->location_id)
					<option value="{{ $record->location_id }}" selected>{{ $record->location->name }}</option>
				@endif
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama') }}</label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" class="form-control" value="{{ $record->name }}" placeholder="{{ __('Nama') }}" readonly>
		</div>
	</div>

	{{-- <div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Username Telegram') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group"> 
			<input type="text" name="telegram_user_id" value="{{$record->telegram_user_id}}" class="form-control" placeholder="{{ __('Username Telegram') }}" disabled>
		</div>
	</div> --}}
@endsection
@section('buttons')
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
