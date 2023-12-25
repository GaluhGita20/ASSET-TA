@extends('layouts.pageSubmit')

@section('action', route($routes . '.store', $record->id))

@section('card-body')
@section('page-content')
    @method('POST')
    @csrf
    {{-- {{ $record = session('record') }} --}}
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>
                <div class="card-body">
                    {{-- @include('globals.notes')
                    @csrf --}}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                                <input type="hidden" id="dataArray" name="usulan_id" value="{{ json_encode($data) }}">
                            </div>

                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Transaksi') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="trans_name" value="{{ $record->trans_name }}" placeholder="{{ __('Nama Transaksi') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nama Vendor') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="vendor_id" class="form-control base-plugin--select2-ajax vendor_id"
                                        data-url="{{ rut('ajax.selectVendor', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectVendor', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        @if ($record->vendor_id)
                                            <option value="{{ $record->vendors->id }}" selected>
                                                {{ $record->vendors->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="no_spk" value="{{ $record->no_spk }}" placeholder="{{ __('Nomor Kontrak') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Mulai Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control base-plugin--datepicker" name="spk_start_date" value="{{ $record->spk_start_date }}" placeholder="{{ __('Tanggal Mulai Kontrak') }}" data-date-end-date="{{ now()}}" >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Selesai Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control base-plugin--datepicker" name="spk_end_date" value="{{ $record->spk_end_date }}" placeholder="{{ __('Tanggal Selesai Kontrak') }}">
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Jenis Pengadaan') }}</label>
                                </div>
                                <div class="col-md-10 parent-group">
                                    <select name="jenis_pengadaan_id" class="form-control base-plugin--select2-ajax jenis_pengadaan_id"
                                        data-url="{{ rut('ajax.selectJenisPengadaan', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectJenisPengadaan', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        @if ($record->jenis_pengadaan_id)
                                            <option value="{{ $record->pengadaans->id }}" selected>
                                                {{ $record->pengadaans->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah Beli') }}</label>
                                </div>
                                <div class="col-8 parent-group">

                                        <div class="input-group">
                                            <input type="number" class="form-control" id="qty" name="qty" value="{{ $record->qty }}" oninput="updateTotal()"readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    Unit
                                                </span>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Pagu') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" class="form-control" id='budget_limit' name="budget_limit" value="{{ $record->budget_limit }}" readonly oninput="updateTotal()">
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
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Harga Unit Barang') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" min=0 name="unit_cost" id = "unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Unit Barang') }}" value="{{ $record->unit_cost }}"  oninput="updateTotal()">
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
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Biaya Pajak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" min=0 name="tax_cost" id="tax_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Pajak') }}" value="{{ $record->tax_cost }}"  oninput="updateTotal()">
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
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Biaya Pengiriman') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" min=0 name="shiping_cost" id="shiping_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Pengiriman') }}" value="{{ $record->shiping_cost }}"  oninput="updateTotal()">
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
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Total Cost') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min="0" name="total_cost" id="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Total') }}" value="{{ $record->total_cost }}"  oninput="updateTotal()" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                        @include('layouts.forms.btnSubmitModal')
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Lampiran List Usulan Pembelian</h3>
                </div>
                <div class="card-body p-8">
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <div class="card card-custom">
                                <div class="card-body p-8">
                                    <div class="table-responsive">
                                        @if (isset($tableStruct['datatable_1']))
                                            <table id="datatable_1" class="table-bordered is-datatable table" style="width: 100%;"
                                                data-url="{{ $tableStruct['url'] }}" data-paging="{{ $paging ?? true }}" data-info="{{ $info ?? true }}">
                                                <thead>
                                                    <tr>
                                                        @foreach ($tableStruct['datatable_1'] as $struct)
                                                            <th class="v-middle text-center" data-columns-name="{{ $struct['name'] ?? '' }}"
                                                                data-columns-data="{{ $struct['data'] ?? '' }}"
                                                                data-columns-label="{{ $struct['label'] ?? '' }}"
                                                                data-columns-sortable="{{ $struct['sortable'] === true ? 'true' : 'false' }}"
                                                                data-columns-width="{{ $struct['width'] ?? '' }}"
                                                                data-columns-class-name="{{ $struct['className'] ?? '' }}"
                                                                style="{{ isset($struct['width']) ? 'width: ' . $struct['width'] . '; ' : '' }}">
                                                                {{ $struct['label'] }}
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- @include('pengajuan.perencanaan-aset.detail.index') --}}
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->
@endsection

@push('scripts')

<script>
    function updateTotal() {
        // Ambil nilai dari input jumlah barang dan harga per barang
        if(document.getElementById('qty').value > 0 && document.getElementById('unit_cost').value > 0)
            var quantity = parseInt(document.getElementById('qty').value);
            var price = parseInt(document.getElementById('unit_cost').value);
            var tax = parseInt(document.getElementById('tax_cost').value);
            var shiping = parseInt(document.getElementById('shiping_cost').value);
            var pagu = parseInt(document.getElementById('budget_limit').value);

        // Hitung total harga
            var total = (quantity * price) + tax + shiping;

            if(total >  pagu){
                alert("Nilai total melebihi batas anggaran!");

                total = 0;
            }

            // Tampilkan total harga pada elemen dengan id 'total'
            console.log(total)
            document.getElementById('total_cost').value = total;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Cek apakah total_cost sudah tersimpan di local storage
        var storedTotal = localStorage.getItem('total_cost');

        if (storedTotal !== null) {
            // Jika sudah tersimpan, tampilkan nilai pada input dengan id 'total_cost'
            document.getElementById('total_cost').value = storedTotal;
        }

        // Panggil fungsi updateTotal untuk memastikan nilai lainnya terupdate
        updateTotal();
    });
</script>
    
@endpush
