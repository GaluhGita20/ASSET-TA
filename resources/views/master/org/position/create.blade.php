@extends('layouts.modal')

@section('action', rut($routes.'.store'))

@section('modal-body')
	@method('POST')

	<div class="form-group row">
        <label class="col-sm-12 col-form-label">{{ __('Level Tingkatan Organisasi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 parent-group">
        <select class="form-control base-plugin--select2-ajax level_id" name="level" data-placeholder="Level Organisasi">
            <option value="kepala">Kepala</option>
            <option value="wakil kepala">Wakil Kepala</option>
            <option value="staf">Staf</option>
        </select>
        </div>
    </div>

	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Instansi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="root_id" id="root_id" class="form-control base-plugin--select2-ajax root_id"
				data-url="{{ route('ajax.selectStruct', ['parent_bod']) }}"
				data-url-origin="{{ route('ajax.selectStruct', ['parent_bod']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
				<option value="">{{ __('Pilih Salah Satu') }}</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Struktur') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<select name="location_id" class="form-control base-plugin--select2-ajax location_id"
				data-url="{{ route('ajax.selectDeps', ['root_id']) }}"
				data-url-origin="{{ route('ajax.selectDeps', ['root_id']) }}"
				data-placeholder="{{ __('Pilih Salah Satu') }}">
				<option value="">{{ __('Pilih Salah Satu') }}</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<label class="col-sm-12 col-form-label">{{ __('Nama Jabatan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
		<div class="col-sm-12 parent-group">
			<input type="text" name="name" class="form-control" placeholder="{{ __('Nama') }}">
		</div>
	</div>
@endsection

@push('scripts')

<script>

    $(function () {
        $('.content-page').on('change', 'select.root_id', function (e) {
            var me = $(this);
            console.log(me.val());
            if (me.val()) {
                var objectId = $('select.location_id');
                var urlOrigin = objectId.data('url-origin');
                var urlParam = $.param({ root_id: me.val() }); // Mengubah parameter menjadi root_id
                console.log(objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam))));
                objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
                objectId.val(null).prop('disabled', false);
            }
            BasePlugin.initSelect2();
        });
    });
</script>
@endpush
{{-- 
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

@endpush --}}
