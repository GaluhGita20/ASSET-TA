@extends('layouts.pageSubmit')

@section('action', route($routes . '.storeDetailKibF'))

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
                                <input type="hidden" id="usulanId" name="usulan_id" value="{{ $usulan->id }}">
                               <input type="hidden" id="trans_id" name="trans_id" value="{{ $trans->id }}"> 
                               <input type="hidden" id="jumlah_semua" name="jumlah_semua" value="{{ $jumlah}}">
                               <input type="hidden" id="type" name="type" value="KIB F">
                               <input type="hidden" id="jumlah_semua" name="cst_val" value="F">
                            </div>
                      
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="nama__aset" placeholder="{{ __('Nama Aset') }}" value="{{ $usulan->asetd->name }}" readonly>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="spesifikasi" value="{{ $usulan->desc_spesification }}">{{ $usulan->desc_spesification }}"</textarea>
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
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
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
                                    <select name="charater_bld" class="form-control">
                                        <option value="permanen">{{ __('Permanen') }}</option>
                                        <option value="semi permanen">{{ __('Semi Permanen') }}</option>
                                        <option value="darurat">{{ __('Darurat') }}</option>
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
                                    <select name="is_graded_bld" class="form-control">
                                        <option value="yes">{{ __('Yes') }}</option>
                                        <option value="no">{{ __('No') }}</option>
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
                                    <select name="is_concreate_bld" class="form-control">
                                        <option value="yes">{{ __('Yes') }}</option>
                                        <option value="no">{{ __('No') }}</option>
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="wide" placeholder="{{ __('Luas Bangunan') }}">
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
                                    <input class="form-control " name="no_sertificate" placeholder="{{ __('Nomor Document') }}">
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
                                    placeholder="{{ __('Tanggal Document') }}" value="" data-date-end-date="{{ now() }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Alamat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="address" type="text" class="form-control" placeholder="{{ __('Alamat') }}">
                                </div>
                            </div>
                        </div>  

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Kode Tanah') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="tanah_id" class="form-control base-plugin--select2-ajax coa_id"
                                        data-url="{{ rut('ajax.selectCoa', ['a']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['a']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
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
                                    placeholder="{{ __('Status Tanah') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Pembelian') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="receipt_date" value="{{ $trans->spk_start_date->format('Y/m/d') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Penerimaan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="receipt_date" value="{{ $trans->receipt_date->format('Y/m/d') }}" readonly>
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
                                    <select name="source_acq" class="form-control">
                                        <option value="pembelian">{{ __('Pembelian') }}</option>
                                        <option value="hibah" >{{ __('Hibah') }}</option>
                                        <option value="sumbangan" >{{ __('Sumbangan') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Asal Usul') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="jenis_pengadaan_id" class="form-control">
                                        <option value="{{ $usulan->danad->id }}" placeholder="{{ __('Asal Usul Perolehan') }}">{{ $usulan->danad->name }}</option>
                                    </select>
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
                                        @if ($trans->vendor_id)
                                            <option value="{{ $trans->vendors->id }}" selected>
                                                {{ $trans->vendors->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $trans->unit_cost }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Unit Pengusul') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="departemen_id" id="departemen_id" class="form-control base-plugin--select2-ajax departemen_id">
                                        @if ($usulan->perencanaan->struct->name)
                                            <option value="{{ $usulan->perencanaan->struct->id }}" selected>
                                                {{ $usulan->perencanaan->struct->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6" id="percepatan" style="display:none">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" max="{{ $jumlah }}" min="1" value="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6" id="percepatan2" style="display:block">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" max="{{ $jumlah }}" placeholder="{{ __('Jumlah Maksimum '.$jumlah.' ') }}" min="1" value="1" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kondisi') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="condition" class="form-control">
                                        <option value="baik">{{ __('Baik') }}</option>
                                        <option value="kurang baik">{{ __('Kurang Baik') }}</option>
                                        <option value="rusak berat">{{ __('Rusak Berat') }}</option>
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
                                        <input type="number" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Masa Manfaat') }}" name="useful">
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Nilai Residu') }}" name="residual_value">
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
                                    <textarea class="form-control" placeholder="{{ __('Keterangan') }}" name="description" ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                        <button type="submit" onclick="submitForm()" class="btn btn-primary base-form--submit-modal" data-submit="0">
                            <i class="fa fa-save mr-1"></i>
                            {{ __('Simpan') }}
                        </button>
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



