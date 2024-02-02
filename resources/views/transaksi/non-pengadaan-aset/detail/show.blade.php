@extends('layouts.modal')

{{-- @section('action', route($routes . '.detailUpdateApprove', $detail->id)) --}}

@section('modal-body')
    {{-- @method('POST') --}}
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="detail_id" value="{{ $detail->id }}">
    <input type="hidden" name="trans_id" value="{{ $detail->id }}">
    {{-- <input type="hidden" name="pembelian_id" value="{{ $detail->id }}"> --}}
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="ref_aset_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectAsetRS', 'all') }}"
                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                        <option value="">{{ __('Pilih Salah Satu') }}</option>

                        @if (isset($detail) && ($asetd = $detail->asetd))
                            <option value="{{ $asetd->id }}" selected disabled>{{ $asetd->name }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="desc_spesification" placeholder="{{ __('Spesifikasi Aset') }}" value ="{{ $detail->desc_spesification }}" readonly>{{ $detail->desc_spesification }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12" >
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Harga Unit') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=0 id ="HPS_unit_cost" name="HPS_unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Harga Unit') }}" value="{{ $detail->HPS_unit_cost }}" oninput="updateTotal()" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Rupiah
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        

        <div class="col-sm-12 col-sm-12" >
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Jumlah Diterima') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" id="qty_agree" min=0 name="qty_agree" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah Disetujui') }}" value="{{ $detail->qty_agree ?  $detail->qty_agree : 0}}" oninput="updateTotal()" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Unit
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')

    <script>
        function updateTotal() {
            // Ambil nilai dari input jumlah barang dan harga per barang
            if(document.getElementById('qty_req').value > 0 && document.getElementById('HPS_unit_cost').value > 0)
                var quantity = parseInt(document.getElementById('qty_req').value);
                var price = parseInt(document.getElementById('HPS_unit_cost').value);

            // Hitung total harga
                var total = quantity * price;

                // Tampilkan total harga pada elemen dengan id 'total'
                console.log(total)
                document.getElementById('HPS_total_cost').value = total;
            
                var quantity_agree = parseInt(document.getElementById('qty_agree').value);
                var total_agree = quantity_agree * price;
                document.getElementById('HPS_total_agree').value = total_agree;
        }
    </script>
@endpush
