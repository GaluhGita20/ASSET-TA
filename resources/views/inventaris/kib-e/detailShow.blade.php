@extends('layouts.pageSubmit')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

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
                        <h3 class="card-title" style="text-align:center;">{{ __('Inventaris Aset Tetap Lainya') }}</h3>
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
                                    <input type="text" class="form-control" name="nama_aset" placeholder="{{ __('Nama Aset') }}" value="Bola" readonly>
                                    {{-- <input type="text" class="form-control" name="nama_aset" placeholder="{{ __('Nama Aset') }}" value="{{ $record->usulans->asetd->name }}" readonly> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Spesifikasi') }}</label>
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
                                        data-url="{{ rut('ajax.selectCoa', ['e']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['e']) }}"
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
                                    <label class="col-form-label">{{ __('Pencipta') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="creators" placeholder="{{ __('Pencipta') }}" value="{{ $record->creators }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Judul') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="title" value="{{ $record->title }}" placeholder="{{ __('Judul') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Ukuran') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" step="0.1" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Ukuran Hewan') }}" name="size_animal" value="{{ $record->size_animal }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                M
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-text text-muted">*Ukuran Hewan</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jenis') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="tipe_animal" class="form-control" disabled>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        <option value="mamalia" {{ $record->tipe_animal == "mamalia" ? 'selected':'' }}  >{{ __('Mamalia') }}</option>
                                        <option value="pisces" {{ $record->tipe_animal == "pisces" ? 'selected':'' }}>{{ __('Ikan') }}</option>
                                        <option value="reptil" {{ $record->tipe_animal == "reptil" ? 'selected':'' }} >{{ __('Reptil') }}</option>
                                        <option value="amfibi" {{ $record->tipe_animal == "amfibi" ? 'selected':'' }}>{{ __('Amfibi') }}</option>
                                        <option value="unggas" {{ $record->tipe_animal == "unggas" ? 'selected':'' }}>{{ __('Unggas') }}</option>
                                    </select>
                                    <div class="form-text text-muted">*Jenis Hewan</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Bahan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="material" value="{{ $record->materials->name }}" placeholder="{{ __('Bahan') }}"  readonly >
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
                                    <input class="form-control " name="book_date" value="{{ $record->book_date }}" readonly>
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{$record->acq_value }}" readonly>
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
                                    <label class="col-form-label">{{ __('Ruang') }}</label>
                                </div>
                                <div class="col-8 parent-group"> 
                                    <select name="room_location" class="form-control" disabled>
                                        @if(!empty($record->room_lacation)) 
                                            <option value="{{ $record->room_location }}" selected>{{ $record->locations->name }}</option>
                                        @else
                                            <option value="" selected>-</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

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
                                    <label class="col-form-label">{{ __('Keterangan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" placeholder="{{ __('Keterangan') }}" name="description" value="{{ $record->description }}" readonly>{{ $record->description }}</textarea>
                                </div>
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



