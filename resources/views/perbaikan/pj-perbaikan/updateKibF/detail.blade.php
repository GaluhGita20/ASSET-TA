
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        &nbsp;
                        <h3 class="card-title" style="text-align:center;">{{ __('Inventaris Aset Gedung Bangunan') }}</h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                                <input type="hidden" id="usulanId" name="usulan_id" value="{{ $usulan->id }}">
                                <input type="hidden" id="jumlah_semua" name="jumlah_semua" value="1">
                                <input type="hidden" id="jumlah_semua" name="cst_val" value="C">
                                <input type="hidden" id="type" name="type" value="KIB C">
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
                                    <textarea class="form-control" name="spesifikasi" value="{{ $usulan->desc_spesification }}" readonly>{{ $usulan->desc_spesification }}</textarea>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Kode Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="coa_id" class="form-control base-plugin--select2-ajax coa_id"
                                        data-url="{{ rut('ajax.selectCoa', ['c']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['c']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Bangunan Bertingkat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    <label class="col-form-label">{{ __('Berbahan Beton') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    <label class="col-form-label">{{ __('Luas Lantai') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="wide" placeholder="{{ __('Luas Lantai') }}">
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
                                    <label class="col-form-label">{{ __('Luas Bangunan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="wide_bld" placeholder="{{ __('Luas Bangunan') }}">
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
                                    <label class="col-form-label">{{ __('Nomor Dokumen') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="no_sertificate" placeholder="{{ __('Nomor Document') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Dokumen') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="sertificate_date" class="form-control base-plugin--datepicker"
                                    placeholder="{{ __('Tanggal Document') }}" value="" data-date-end-date="{{ now() }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nomer Kode Tanah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    <label class="col-form-label">{{ __('Status Tanah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="land_status" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ rut('ajax.selectStatusTanah', [
                                            'search'=>'all'
                                        ]) }}"  data-placeholder="{{ __('Pilih Salah Satu Status Tanah') }}">
                                        <option value="" selected>{{ __('Pilih Salah Satu Status Tanah') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Alamat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input name="address" type="text" class="form-control" placeholder="{{ __('Alamat') }}">
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
                                    <input class="form-control " name="receipt_date" value="{{ $usulan->trans->receipt_date->format('Y/m/d') }}" readonly>
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
                                        <option value="pembelian" {{ $usulan->trans->source_acq == "Pembelian" ? 'selected':'' }}>{{ __('Pembelian') }}</option>
                                        <option value="hibah" {{ $usulan->trans->source_acq == "Hibah" ? 'selected':'' }} >{{ __('Hibah') }}</option>
                                        <option value="sumbangan" {{ $usulan->trans->source_acq == "Sumbangan" ? 'selected':'' }}>{{ __('Sumbangan') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        @if(!empty($usulan->danad))
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Asal Usul') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="source" id="source" class="form-control base-plugin--select2-ajax">
                                        @if ($usulan->source_fund_id)
                                            <option value="{{ $usulan->danad->name }}" selected>
                                                {{ $usulan->danad->name }}
                                            </option>
                                        @endif
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
                                        @if(!empty($perbaikan))
                                            <option value="{{ $perbaikan->vendors->id }}" selected>
                                                {{ $perbaikan->vendors->name }}
                                            </option>
                                        @else
                                            @if ($usulan->trans->vendor_id)
                                                <option value="{{ $usulan->trans->vendors->id }}" selected>
                                                    {{ $usulan->trans->vendors->name }}
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($usulan->trans->source_acq == 'Pembelian')
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                
                                        @php
                                            $total = $perbaikan->total_cost + $usulan->HPS_unit_cost;
                                        @endphp
                                        <div class="input-group">
                                            <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $total }}" readonly>
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $usulan->HPS_unit_cost }}" readonly>
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

                        @if (empty($usulan->perencanaan->struct->name))
                        <div class="col-sm-6">
                            <div class="form-group row">
                
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Unit Lokasi Hibah Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="departemen_id" id="departemen_id_h" class="form-control base-plugin--select2-ajax departemen_id"
                                    data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                                    data-placeholder="{{ __('Unit Kerja') }}">
                                    <option value="">{{ __('Pilih Struktur Organisasi') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @else

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Unit Pengusul') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                        @endif

                        <div class="col-sm-6" id="percepatan" style="display:none">
                            <div class="form-group row">
                                
                                    <div class="col-4 pr-0">
                                        <label class="col-form-label">{{ __('Jumlah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                    </div>
                                    <div class="col-8 parent-group">
                                        <input type="number" class="form-control" id="qty" name="qty" max="1"  min="1" value="1" disabled>
                                    </div>
                                
                            </div>
                        </div>

                        <div class="col-sm-6" id="percepatan2" style="display:block">
                            <div class="form-group row">

                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" max="1"  min="1" value="1" disabled>
                                </div>

                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kondisi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    <label class="col-form-label">{{ __('Masa Manfaat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    <label class="col-form-label">{{ __('Nilai Residu') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                </div>
            </div>
        </div>
    </div>


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
        var $loc;
        var objectId = $('select.location');
        
        if ($('#departemen_id').length > 0) {
            $loc = document.getElementById('departemen_id');
        } else {
            $loc = document.getElementById('departemen_id_h');
            $('.content-page').on('change', 'select.departemen_id_h', function (e) {
                handleDepartemenChange($loc, objectId);
            });
        }

        if ($loc) {
            $('.content-page').on('change', 'select.departemen_id', function (e) {
                handleDepartemenChange($loc, objectId);
            });
        }

        function handleDepartemenChange(loc, objectId) {
            var urlOrigin = objectId.data('url-origin');
            var urlParam = $.param({ departemen_id: loc.value });
            objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
            console.log(decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
            objectId.val(null).prop('disabled', false);
            BasePlugin.initSelect2();
        }
    });

</script>

@endpush



