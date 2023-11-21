@extends('layouts.modal')

@section('action', rut($routes.'.store'))

@section('modal-body')
	@method('POST')

	<div class="form-group row">
        <label class="col-sm-12 col-form-label">{{ __('Level Tingkatan Organisasi') }}</label>
        <div class="col-sm-12 parent-group">
        <select class="form-control base-plugin--select2-ajax level_id" name="level_id" data-placeholder="Level Organisasi">
            <option value="bod">Penanggung Jawab</option>
            <option value="department">Departement</option>
            <option value="subdepartment">Sub Departement</option>
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
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama') }}</label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" class="form-control" placeholder="{{ __('Nama') }}">
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
