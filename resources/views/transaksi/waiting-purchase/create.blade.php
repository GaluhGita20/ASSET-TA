@extends('layouts.pageSubmit')

@section('action', route($routes . '.storeDetail'))

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
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>
                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                                <input type="hidden" id="dataArray" name="usulan_id" value="{{ json_encode($data) }}">
                            </div>
                            @php 
                                $temp_id = \App\Models\Pengajuan\PerencanaanDetail::where('id', $data[0])->pluck('ref_aset_id')->first();
                                $name = \App\Models\Master\Aset\AsetRs::where('id',$temp_id)->pluck('name')->first(); 
                            @endphp
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Transaksi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="trans_name" placeholder="{{ __('Nama Transaksi') }}" value="Transaksi Pembelian {{$name}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nama Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="no_spk" placeholder="{{ __('Nomor Kontrak') }}">
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor SP2D') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="sp2d_code" placeholder="{{ __('Nomor SP2D') }}">
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Mulai Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control base-plugin--datepicker" name="spk_start_date" placeholder="{{ __('Tanggal Mulai Kontrak') }}" data-date-end-date="{{ now()}}" >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Selesai Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control base-plugin--datepicker" name="spk_end_date" placeholder="{{ __('Tanggal Selesai Kontrak') }}">
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Jenis Pengadaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                                        <input type="number" class="form-control" id="qty" name="qty" placeholder="{{ __('Jumlah Beli') }}" value="{{ $jumlah_beli }}" readonly oninput="updateTotal()">
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" id='budget_limit' name="budget_limit" placeholder="{{ __('Pagu') }}" value="{{ $pagu }}" readonly>
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
                                    <label class="col-form-label">{{ __('Harga Unit') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min=0 name="unit_cost" id = "unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Unit Barang') }}" value="0"  oninput="updateTotal()">
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
                                    <label class="col-form-label">{{ __('Biaya Pajak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min=0 name="tax_cost" id="tax_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Pajak') }}" value="0"  oninput="updateTotal()">
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
                                    <label class="col-form-label">{{ __('Biaya Pengiriman') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min=0 name="shiping_cost" id="shiping_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Pengiriman') }}" value="0"  oninput="updateTotal()">
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
                                    <label class="col-form-label">{{ __('Total Biaya') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min="0" name="total_cost" id="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Total') }}" value="0" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Nota Pembelian ') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-10 parent-group">
                                    <div class="custom-file">
                                        <input type="hidden"
                                            name="uploads[uploaded]"
                                            class="uploaded"
                                            value="0">
                                        <input type="file" multiple
                                            class="custom-file-input base-form--save-temp-files"
                                            data-name="uploads"
                                            data-container="parent-group"
                                            data-max-size="30024"
                                            data-max-file="100"
                                            accept="*">
                                        <label class="custom-file-label" for="file">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">*Maksimal 20MB</div>
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
                {{-- header --}}
                <div class="card-header">
                    <h3 class="card-title">Lampiran List Usulan Pembelian</h3>
                </div>

                <div class="card-body p-8">
                    {{-- filter --}}
                    
                    {{-- data --}}
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
    var quantity = document.getElementById('qty').value;
    var unit_cost = document.getElementById('unit_cost').value;
    var tax = document.getElementById('tax_cost').value;
    var shiping = document.getElementById('shiping_cost').value;
    var pagu = document.getElementById('budget_limit').value;

    quantity= quantity.replace(/[^0-9]/g, '');
    unit_cost= unit_cost.replace(/[^0-9]/g, '');
    tax= tax.replace(/[^0-9]/g, '');
    shiping= shiping.replace(/[^0-9]/g, '');
    pagu= pagu.replace(/[^0-9]/g, '');

    quantity = parseInt(quantity);
    unit_cost = parseInt(unit_cost);
    tax = parseInt(tax);
    shiping = parseInt(shiping);
    pagu = parseInt(pagu);
    
    if(quantity > 0 && unit_cost > 0){
        console.log(quantity);
        var total_unit = parseInt(quantity) * parseInt(unit_cost);
        var total = parseInt(quantity) * parseInt(unit_cost) + tax + shiping;
        if(total_unit > pagu){
            alert("Nilai Harga Unit Melebihi Pagu Anggaran !");
            document.getElementById('total_cost').value = 0;
        }else{
            document.getElementById('total_cost').value = parseInt(total);
        }
        // console.log(total)
    }
        
       // document.getElementById('HPS_unit_cost').value = parseInt(price)
}
</script>
@endpush
