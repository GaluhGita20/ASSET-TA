@extends('layouts.pageSubmit')

@section('action', route($routes . '.storeDetailKibB'))

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
                        {{-- @include('layouts.forms.btnBackTop') --}}
                        &nbsp;
                        <h3 class="card-title" style="text-align:center;">{{ __('Inventarisasi Aset Peralatan dan Mesin') }}</h3>
                        {{-- <h3 class="card-title">{{ __('Inventarisasi Aset Peralatan dan Mesin') }}</h3> --}}
                    </div>
                    <div class="card-toolbar">
                        <button class="btn btn-primary" style="margin-right: 8px;" onclick="setPercepatan()">
                            {{ __('Percepatan') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                                <input type="hidden" id="usulanId" name="usulan_id" value="{{ $usulan->id }}">
                                {{-- <input type="hidden" id="trans_id" name="trans_id" value="{{ $trans->id }}">  --}}
                                <input type="hidden" id="jumlah_semua" name="jumlah_semua" value="{{ $jumlah}}">
                                <input type="hidden" id="type" name="type" value="KIB B">
                            </div>
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="nama_aset" placeholder="{{ __('Nama Aset') }}" value="{{ $usulan->asetd->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                        data-url="{{ rut('ajax.selectCoa', ['b']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['b']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Sumber Perolehan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-md-10 parent-group">
                                    <select name="source_acq" class="form-control" disabled>
                                        <option value="pembelian" {{ $usulan->trans->source_acq == "Pembelian" ? 'selected':'' }}>{{ __('Pembelian') }}</option>
                                        <option value="hibah" {{ $usulan->trans->source_acq == "Hibah" ? 'selected':'' }} >{{ __('Hibah') }}</option>
                                        <option value="sumbangan" {{ $usulan->trans->source_acq == "Sumbangan" ? 'selected':'' }}>{{ __('Sumbangan') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Merek') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control" placeholder="{{ __('Merek') }}" name="merek_type_item">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Bahan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="material" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ rut('ajax.selectBahanAset', [
                                            'search'=>'all'
                                        ]) }}"  data-placeholder="{{ __('Pilih Salah Satu Bahan Aset') }}">
                                        <option value="" selected>{{ __('Pilih Salah Satu Bahan Aset') }}</option>
                                    </select>
                                </div>
                                {{-- <div class="col-8 parent-group">
                                    <input type="text"  class="form-control" placeholder="{{ __('Bahan') }}" name="material">
                                </div> --}}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Ukuran CC') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Ukuan CC') }}" name="cc_size_item">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                CC
                                            </span>
                                        </div>
                                    </div>
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
                                    <label class="col-form-label">{{ __('Nomor Pabrik') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text"  class="form-control" placeholder="{{ __('Nomor Pabrik') }}" name="no_factory_item">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Rangka') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text"  class="form-control" placeholder="{{ __('Nomor Rangka') }}" name="no_frame">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor BPKB') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text"  class="form-control" placeholder="{{ __('Nomor BPKB') }}" name="no_BPKB_item">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Mesin') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" placeholder="{{ __('Nomor Mesin') }}" name="no_machine_item">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Polisi') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text"  class="form-control" placeholder="{{ __('Nomor Polisi') }}" name="no_police_item">
                                </div>
                            </div>
                        </div>

                        @if(!empty($usulan->trans->spk_start_date))
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Pembelian') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    
                                    <input class="form-control" name="receipt_date" value="{{ $usulan->trans->spk_start_date->format('Y/m/d') }}" readonly>
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

                        @if(!empty($usulan->trans->spk_start_date))
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Asal Usul') }}</label>
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
                                        @if ($usulan->trans->vendor_id)
                                            <option value="{{ $usulan->trans->vendors->id }}" selected>
                                                {{ $usulan->trans->vendors->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($usulan->trans->source_acq == 'Pembelian')
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                        @else
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    <label class="col-form-label">{{ __('Lokasi Hibah Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="location_hibah_aset" id="departemen_id_h" class="form-control base-plugin--select2-ajax departemen_id"
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

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Ruang') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="room_location" class="form-control base-plugin--select2-ajax location"
                                    data-url="{{ rut('ajax.selectRoom', ['departemen_id']) }}"
                                    data-url-origin="{{ rut('ajax.selectRoom', ['departemen_id']) }}"
                                    placeholder="{{ __('Pilih Salah Satu') }}">
                                    <option value="">{{ __('Pilih Salah Satu') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 offset-md-4 col-md-7">
                                    <span style="font-size: 11px">{{ __('* Nama Ruang Diisi Jika Aset Berada di Dalam Ruangan') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Lokasi Non Ruangan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" id="non_room_location" name="non_room_location" placeholder="{{ __('Nama Lokasi') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 offset-md-4 col-md-7">
                                    <span style="font-size: 11px">{{ __('* Lokasi Non Ruang Diisi Jika Aset Berada di Luar Ruangan') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6" id="percepatan" style="display:none">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" max="{{ $jumlah }}" placeholder="{{ __('Jumlah Maximum '.$jumlah.' ') }}" min="1" value="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6" id="percepatan2" style="display:block">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" max="{{ $jumlah }}" min="1" value="1" disabled>
                                </div>
                            </div>
                        </div>

                        {{-- input baru --}}
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
                                    <label class="col-form-label">{{ __('Keterangan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="description" placeholder="{{ __('Keterangan') }}" ></textarea>
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
    var $loc;
    var objectId = $('select.location');
    
    if ($('#departemen_id').length > 0) {
        $loc = document.getElementById('departemen_id');
        handleDepartemenChange($loc, objectId);
    } else {
        $loc = document.getElementById('departemen_id_h');
        $('.content-page').on('change', 'select.departemen_id_h', function (e) {
            handleDepartemenChange($loc, objectId);
        });
    }

    if ($loc) {
        // console.log($loc.value);
        $('.content-page').on('change', 'select.departemen_id', function (e) {
            // console.log($loc);
            handleDepartemenChange($loc, objectId);
        });
    }
    
    // console.log(document.getElementById('departemen_id').value);

    function handleDepartemenChange($loc, objectId) {
        console.log($loc.value);
        var urlOrigin = objectId.data('url-origin');
        var urlParam = $.param({departemen_id:$loc.value});
        objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        console.log(decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        objectId.val(null).prop('disabled', false);
        BasePlugin.initSelect2();
    }
});


    // if(document.getElementById('departemen_id') != none){

    //     $(function () {
    //         // $('.content-page').on('change', 'select.departemen_id', function (e) {
    
    //             $loc= document.getElementById('departemen_id');
                
    
    //             var objectId = $('select.location');
    //             var urlOrigin = objectId.data('url-origin');
    //             var urlParam = $.param({departemen_id: $loc.value});
    //             objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
    //             console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
    //             objectId.val(null).prop('disabled', false);
            
    //         BasePlugin.initSelect2();
    //             // });
    //     });
    // }else{

    //     $(function () {
    //         $('.content-page').on('change', 'select.departemen_id_h', function (e) {
    
    //             $loc= document.getElementById('departemen_id_h');
                
    
    //             var objectId = $('select.location');
    //             var urlOrigin = objectId.data('url-origin');
    //             var urlParam = $.param({departemen_id: $loc.value});
    //             objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
    //             console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
    //             objectId.val(null).prop('disabled', false);
            
    //         BasePlugin.initSelect2();
    //         });
    //     });
    // }

</script>

@endpush



