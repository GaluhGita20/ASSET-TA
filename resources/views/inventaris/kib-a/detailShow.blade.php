@extends('layouts.pageSubmit')

{{-- @section('action', route($routes . '.storeDetailKibA')) --}}

@section('card-body')
@section('page-content')
    @method('POST')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        &nbsp;
                        <h3 class="card-title" style="text-align: center;">{{ __('Inventarisasi Aset Tanah') }}</h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                            </div>
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="nama_aset" placeholder="{{ __('Nama Aset') }}" value="{{ $record->usulans->asetd->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="spesifikasi" value="{{ $record->usulans->desc_spesification }}" readonly>{{ $record->usulans->desc_spesification }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Register') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="no_regsiter" value="{{ str_pad($record->no_register, 3, '0', STR_PAD_LEFT) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Kode Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="coa_id" class="form-control base-plugin--select2-ajax coa_id"
                                        data-url="{{ rut('ajax.selectCoa', ['a']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['a']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
                                        @if (!empty($record->coa_id))
                                            <option value="{{ $record->coa_id }}" selected>{{ $record->coad->nama_akun.' ( '.$record->coad->kode_akun.' )'  }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Luas Tanah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="wide" placeholder="{{ __('Luas Tanah') }}" value="{{ $record->wide }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                M2
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Hak Tanah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text"  class="form-control"  placeholder="{{ __('Hak Tanah') }}" name="land_rights"  value="{{ ucwords($record->land_rights) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Sertifikat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" placeholder="{{ __('Nomor Sertifikat') }}" name="no_sertificate" value="{{ $record->no_sertificate }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Sertifikat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="sertificate_date" class="form-control base-plugin--datepicker"
                                    placeholder="{{ __('Tanggal Sertifikat') }}" data-date-end-date="{{ now() }}"  value="{{ $record->sertificate_date }}" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-6">  
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Provinsi') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="province_id" class="form-control base-plugin--select2-ajax province_id"
                                        data-url="{{ rut('ajax.selectProvince', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectProvince', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        @if (!empty($record->city_id))
                                            <option value="{{ $record->province_id }}" selected>{{ $record->provinsi->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kota') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="city_id" class="form-control base-plugin--select2-ajax city_id"
                                        data-url="{{ rut('ajax.selectCity', ['province_id']) }}"
                                        data-url-origin="{{ rut('ajax.selectCity', ['province_id']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        @if (!empty($record->city_id))
                                            <option value="{{ $record->city_id }}" selected>{{ $record->city->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Daerah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="district_id" class="form-control base-plugin--select2-ajax district_id"
                                        data-url="{{ rut('ajax.selectDistrict', ['city_id']) }}"
                                        data-url-origin="{{ rut('ajax.selectDistrict', ['city_id']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        @if (!empty($record->district_id))
                                            <option value="{{ $record->district_id }}" selected>{{ $record->district->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Alamat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="address" type="text" class="form-control" placeholder="{{ __('Alamat') }}" value="{{ ucwords($record->address) }}" readonly>
                                </div>
                            </div>
                        </div>  

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kegunaan Tanah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text"  class="form-control"  placeholder="{{ __('Kegunaan Tanah') }}" name="land_use" value="{{ ucwords($record->land_use) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Sumber Perolehan') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="source_acq" class="form-control" disabled>
                                        <option value="Pembelian" {{ $record->usulans->trans->source_acq == "Pembelian" ? 'selected':'' }}>{{ __('Pembelian') }}</option>
                                        <option value="Hibah" {{ $record->usulans->trans->source_acq == "Hibah" ? 'selected':'' }} >{{ __('Hibah') }}</option>
                                        <option value="Sumbangan" {{ $record->usulans->trans->source_acq == "Sumbangan" ? 'selected':'' }}>{{ __('Sumbangan') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        @if (!empty($usulan->trans->spk_start_date))
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Pembelian') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="receipt_date" value="{{ $usulan->trans->spk_start_date->format('Y/m/d') }}" readonly>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Penerimaan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="receipt_date" value="{{ $record->usulans->trans->receipt_date->format('Y/m/d') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Vendor') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="vendor_id" class="form-control base-plugin--select2-ajax vendor_id" disabled>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        @if ($record->usulans->trans->vendor_id)
                                            <option value="{{ $record->usulans->trans->vendors->id }}" selected>
                                                {{ $record->usulans->trans->vendors->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if (!empty($record->usulans->danad))
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Asal Usul') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="source" id="source" class="form-control base-plugin--select2-ajax">
                                        @if ($record->usulan->sumber_biaya_id)
                                            <option value="{{ $record->usulan->danad->name }}" selected>
                                                {{ $record->usulan->danad->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($record->usulans->trans->source_acq == 'pembelian')
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $usulan->trans->unit_cost }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $record->usulans->HPS_unit_cost }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif


                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Keterangan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="description" placeholder="{{ __('Keterangan') }}" value="{{ $record->description }}" readonly>{{ $record->description }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->
@endsection


@push('scripts')

<script>
    function setPercepatan() {
        // Ambil elemen input percepatan
        var percepatanInput = document.getElementById('percepatan');
        var percepatanInput2 = document.getElementById('percepatan2');

        if (percepatanInput.style.display === 'none') {
            percepatanInput.style.display = 'block';
            percepatanInput2.style.display = 'none';
        } else {
            percepatanInput.style.display = 'none';
            percepatanInput2.style.display = 'block';
        }
    }
</script>

<script>
    $(function () {
        // $('.content-page').on('change', 'select.departemen_id', function (e) {
               $loc= document.getElementById('departemen_id');

					var objectId = $('select.location');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({departemen_id: $loc.value});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                    objectId.val(null).prop('disabled', false);
				
				BasePlugin.initSelect2();
			// });
    });
</script>


<script>
    $(function () {
        $('.content-page').on('change', 'select.province_id', function (e) {
            var me = $(this);
            if (me.val()) {
                var objectId = $('select.city_id');
                var urlOrigin = objectId.data('url-origin');
                var urlParam = $.param({province_id: me.val()});
                objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                objectId.val(null).prop('disabled', false);
            }
            BasePlugin.initSelect2();
        });

        $('.content-page').on('change', 'select.city_id', function (e) {
            var me = $(this);
            if (me.val()) {
                var objectId = $('select.district_id');
                var urlOrigin = objectId.data('url-origin');
                var urlParam = $.param({city_id: me.val()});
                objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                objectId.val(null).prop('disabled', false);
            }
            BasePlugin.initSelect2();
        });

    });
</script>

@endpush



