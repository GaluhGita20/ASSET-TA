@extends('layouts.pageSubmit')

{{-- @section('action', route($routes . '.storeDetailKibF')) --}}

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
                        <h3 class="card-title" style="text-align:center;">{{ __('Inventaris Aset Pembangunan Kontruksi') }}</h3>
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
                                    <input type="text" class="form-control" name="nama__aset" placeholder="{{ __('Nama Aset') }}" value="Bangunan Gedung" readonly>
                                    {{-- <input type="text" class="form-control" name="nama__aset" placeholder="{{ __('Nama Aset') }}" value="{{ $record->usulans->asetd->name }}" readonly> --}}
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
                                        data-url="{{ rut('ajax.selectCoa', ['f']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['f']) }}"
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
                                    <label class="col-form-label">{{ __('Bangunan (P,SP,D)') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="charater_bld" class="form-control" disabled>
                                        <option value="permanen" {{ $record->charater_bld == "permanen" ? 'selected':'' }}>{{ __('Permanen') }}</option>
                                        <option value="semi permanen" {{ $record->charater_bld == "semi permanen" ? 'selected':'' }}>{{ __('Semi Permanen') }}</option>
                                        <option value="darurat" {{ $record->charater_bld == "darurat" ? 'selected':'' }}>{{ __('Darurat') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Bangunan Bertingkat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="is_graded_bld" class="form-control" disabled>
                                        <option value="yes" {{ $record->is_graded_bld == "yes" ? 'selected':'' }}>{{ __('Yes') }}</option>
                                        <option value="no" {{ $record->is_graded_bld == "no" ? 'selected':'' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Berbahan Beton') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="is_concreate_bld" class="form-control" disabled>
                                        <option value="yes" {{ $record->is_concreate_bld == "yes" ? 'selected':'' }}>{{ __('Yes') }}</option>
                                        <option value="no" {{ $record->is_concreate_bld == "no" ? 'selected':'' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Luas Bangunan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="wide" placeholder="{{ __('Luas Bangunan') }}" value="{{ $record->wide }}" readonly>
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
                                    <label class="col-form-label">{{ __('Nomor Document') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="no_sertificate" value="{{ $record->no_sertificate }}" placeholder="{{ __('Nomor Document') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Document') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="sertificate_date" class="form-control base-plugin--datepicker"
                                    placeholder="{{ __('Tanggal Document') }}" value="{{ $record->sertificate_date }}" readonly data-date-end-date="{{ now() }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Alamat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="address" type="text" class="form-control" placeholder="{{ __('Alamat') }}" value="{{ $record->address }}" readonly>
                                </div>
                            </div>
                        </div>  

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Kode Tanah') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="tanah_id" class="form-control base-plugin--select2-ajax tanah_id"
                                        data-url="{{ rut('ajax.selectCoa', ['a']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['a']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
                                        @if (!empty($record->tanah_id))
                                            <option value="{{ $record->tanah_id }}" selected>{{ $record->coad->nama_akun.' ( '.$record->coad->kode_akun.' )'  }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Status Tanah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    {{-- filter tanah --}}
                                    <input type="text" name="land_status" class="form-control"
                                    placeholder="{{ __('Status Tanah') }}" value="{{ $record->statusTanah->name}}" readonly>
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
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Pembukuan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="book_date" value="{{ now()->format('Y/m/d') }}" readonly>
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
                                        <option value="pembelian" {{ $record->usulans->trans->source_acq == "pembelian" ? 'selected':'' }}>{{ __('Pembelian') }}</option>
                                        <option value="hibah" {{ $record->usulans->trans->source_acq == "hibah" ? 'selected':'' }} >{{ __('Hibah') }}</option>
                                        <option value="sumbangan" {{ $record->usulans->trans->source_acq == "sumbangan" ? 'selected':'' }}>{{ __('Sumbangan') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if(!empty($record->usulans->danad))
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Asal Usul') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="source" id="source" class="form-control base-plugin--select2-ajax">
                                       
                                            <option value="{{ $record->usulans->danad->name }}" selected>
                                                {{ $record->usulans->danad->name }}
                                            </option>
                                     
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

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

                        @if($record->usulans->trans->source_acq == 'pembelian')
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $record->acq_value }}" readonly>
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $record->acq_value }}" readonly>
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

                        @if(!empty($record->usulans->perencanaan->struct))
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label class="col-form-label">{{ __('Unit Pengusul') }}</label>
                                    </div>
                                    <div class="col-md-8 parent-group">
                                        <select name="departemen_id" id="departemen_id" class="form-control base-plugin--select2-ajax departemen_id">
                                            @if ($record->usulans->perencanaan->struct->name)
                                                <option value="{{ $record->usulans->perencanaan->struct->id }}" selected>
                                                    {{ $record->usulans->perencanaan->struct->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Unit Lokasi Aset') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="departemen_id" id="departemen_id" class="form-control base-plugin--select2-ajax departemen_id">
                                        @if (!empty($record->location_hibah_aset))
                                            <option value="{{ $record->location_hibah_aset }}" selected>
                                                {{ $record->deps->name }}
                                            </option>
                                        @else
                                        <option selected>
                                           -
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kondisi') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="condition" class="form-control" disabled>
                                        <option value="baik" {{ $record->condition == "baik" ? 'selected':'' }}>{{ __('Baik') }}</option>
                                        <option value="kurang baik" {{ $record->condition == "kurang baik" ? 'selected':'' }}>{{ __('Kurang Baik') }}</option>
                                        <option value="rusak berat" {{ $record->condition == "rusak berat" ? 'selected':'' }}>{{ __('Rusak Berat') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Masa Manfaat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Masa Manfaat') }}" name="useful" value="{{ $record->useful }}" readonly> 
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                Tahun
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nilai Residu') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Nilai Residu') }}" name="residual_value" value="{{ $record->residual_value }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                Rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Keterangan Tambahan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" placeholder="{{ __('Keterangan') }}" name="description" value="{{ $record->description }}" readonly >{{ $record->description  }}</textarea>
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

@endpush



