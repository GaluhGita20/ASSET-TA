@extends('layouts.modal')

@section('action', route($routes.'.update',$record->id))

@section('modal-body')
	@method('PUT')
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                    <label class="col-sm-12 col-md-3 col-form-label">{{ __('Vendor') }}</label>
                    <div class=" col-sm-12 col-md-9 parent-group">
                        <input name="name" value="{{ $record->name }}" type="text" class="form-control" placeholder="{{ __('Vendor') }}">
                    </div>
            </div>
        </div>
    </div>
    <div class="form-group row">  
        <label class="col-md-3 col-form-label">{{ __('Jenis Usaha') }}</label>
        <div class="col-md-9 parent-group">
            <select name="jenisUsaha[]" class="form-control base-plugin--select2-ajax"
                data-url="{{ route('ajax.selectJenisUsaha', [
                    'search'=>'all'
                ]) }}" multiple
                placeholder="{{ __('Pilih Salah Satu') }}" required>
                <option value="">{{ __('Pilih Salah Satu') }}</option>
                {{-- @if (!empty($record->jenisUsaha)) --}}
                    @foreach($record->jenisUsaha as $jj)
                        <option value="{{ $jj->id }}" {{ in_array($jj->id, $record->jenisUsaha->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $jj->name }}</option>
                    @endforeach        
                {{-- @endif --}}
            </select>
            {{-- <select name="type_id[]" class="form-control base-plugin--select2-ajax"
                    data-url="{{ route('ajax.selectUser', ['search' => 'level_department']) }}" multiple
                    placeholder="{{ __('Pilih Beberapa') }}">
                    <option value="">{{ __('Pilih Beberapa') }}</option>
                    @foreach ($record->cc as $user)
                        <option value="{{ $user->id }}" selected>
                            {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                        </option>
                    @endforeach
                </select> --}}

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <label class="col-sm-12 col-md-3 col-form-label">{{ __('Nama Pimpinan') }}</label>
                <div class="col-sm-12 col-md-9 parent-group">
                    <input name="leader" value={{ $record->leader }} type="text" class="form-control" placeholder="{{ __('Nama Pimpinan') }}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-12 col-md-3 col-form-label">{{ __('Nomor Instansi') }}</label>
                <div class="col-sm-12 col-md-9 parent-group">
                    <input name="instansi_code" type="text" value="{{ $record->instansi_code }}" class="form-control" placeholder="{{ __('Nomor Instansi') }}"></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                    <label class="col-sm-12 col-md-3 col-form-label">{{ __('Telepon') }}</label>
                    <div class="col-sm-12 col-md-9 parent-group">
                        <input type="tel" name="telp" class="form-control" placeholder="{{ __('Telepon') }}"
                           value="{{ $record->telp }}" pattern="[0-9]{4}[0-9]{4}-[0-9]{0,7}">
                    </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <label class="col-sm-12 col-md-3 col-form-label">{{ __('Email') }}</label>
                <div class="col-sm-12 col-md-9 parent-group">
                    <input type="email" value="{{ $record->email }}" name="email" class="form-control" placeholder="{{ __('Email') }}">
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <label class="col-sm-12 col-md-3 col-form-label">{{ __('Contact Person') }}</label>
                <div class="col-sm-12 col-md-9 parent-group">
                    <input name="contact_person" class="form-control"
                    value="{{ $record->contact_person }}" placeholder="{{ __('Contact Person') }}">
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <label class="col-sm-12 col-md-3 col-form-label">{{ __('Alamat') }}</label>
                <div class="col-sm-12 col-md-9 parent-group">
                    <input name="address" class="form-control"
                    value="{{ $record->address }}"  placeholder="{{ __('Alamat') }}">
                </div>
            </div>
        </div>        
    </div>

    <div>  
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ __('Provinsi') }}</label>
            <div class="col-md-9 parent-group">
                <select name="province_id" class="form-control base-plugin--select2-ajax province_id"
                    data-url="{{ rut('ajax.selectProvince', [
                        'search'=>'all'
                    ]) }}"
                    data-url-origin="{{ rut('ajax.selectProvince', [
                        'search'=>'all'
                    ]) }}"
                    placeholder="{{ __('Pilih Salah Satu') }}" required>
                    <option value="">{{ __('Pilih Salah Satu') }}</option>
                    @if (!empty($record->province_id))
					    <option value="{{ $record->province_id }}" selected>{{ $record->getProvinceName() }}</option>
				    @endif
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ __('Kota') }}</label>
            <div class="col-md-9 parent-group">
                <select name="city_id" class="form-control base-plugin--select2-ajax city_id"
                    data-url="{{ rut('ajax.selectCity', ['province_id']) }}"
                    data-url-origin="{{ rut('ajax.selectCity',['province_id']) }}"
                    placeholder="{{ __('Pilih Salah Satu') }}" required>
                    <option value="">{{ __('Pilih Salah Satu') }}</option>
                    @if (!empty($record->city_id))
					    <option value="{{ $record->city_id }}" selected>{{ $record->getCityName() }}</option>
				    @endif
                </select>
            </div>
        </div>


        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ __('Daerah') }}</label>
            <div class="col-md-9 parent-group">
                <select name="district_id" class="form-control base-plugin--select2-ajax district_id"
                    data-url="{{ rut('ajax.selectDistrict', ['city_id']) }}"
                    data-url-origin="{{ rut('ajax.selectDistrict',['city_id']) }}"
                    placeholder="{{ __('Pilih Salah Satu') }}" required>
                    <option value="">{{ __('Pilih Salah Satu') }}</option>
                    @if (!empty($record->district_id))
					    <option value="{{ $record->district_id }}" selected>{{ $record->daerah->name }}</option>
				    @endif
                </select>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')

	<script>
		$(function () {
			$('.content-page').on('change', 'select.province_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.city_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({province_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});

		});

        $(function () {
			$('.content-page').on('change', 'select.city_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.district_id');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({city_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
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
{{-- @push('scripts')
    @include("master.vendor_barang.include.scripts")
@endpush --}}
