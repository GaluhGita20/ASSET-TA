@extends('layouts.modal')

{{-- @section('action', route($routes . '.detailUpdateApprove', $detail->id)) --}}

@section('modal-body')
    {{-- @method('POST') --}}
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="detail_id" value="{{ $detail->id }}">
    <input type="hidden" name="perencanaan_id" value="{{ $detail->id }}">
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
            <div class="form-group row">
                <div class="col-sm-12 offset-md-5 col-md-7">
                    <span style="font-size: 11px">{{ __('*Contoh Prosesor: Intel Core i7-10750H
                        RAM: 16 GB DDR4
                        Penyimpanan: 512 GB SSD') }}</span>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Standar Kebutuhan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=1 name="requirement_standard" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Standar Kebutuhan') }}"  value="{{ $detail->requirement_standard }}" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Unit
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Jumlah yang Ada') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=0 name="existing_amount" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah yang Ada') }}" value="{{ $detail->existing_amount }}" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Unit
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Jumlah Pengajuan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=1 id ="qty_req" name="qty_req" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah Pengajuan') }}" value="{{ $detail->qty_req }}" oninput="updateTotal()" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Unit
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- @if(auth()->user()->hasRole('Sub Bagian Program Perencanaan') || auth()->user()->hasRole("Direksi")) --}}

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
    
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Harga Total') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=0 id="HPS_total_cost" name="HPS_total_cost" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Harga Total Usulan') }}" value="{{ $detail->HPS_total_cost }}" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text" >
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
                    <label class="col-form-label">{{ __('Jumlah Disetujui') }}</label>
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

        <div class="col-sm-12 col-sm-12" >
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Harga Total Disetujui') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" id ="HPS_total_agree" min=0  name="HPS_total_agree" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Harga Total Disetujui') }}" value="{{ $detail->HPS_total_agree ?  $detail->HPS_total_agree : 0}}" readonly >
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
                    <label class="col-form-label">{{ __('Sumber Pendanaan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="sumber_biaya_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ rut('ajax.selectSSBiaya', [
                            'search'=>'all'
                        ]) }}"
                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                        @if (isset($detail) && ($dana = $detail->danad))
                            <option value="{{ $dana->id }}" selected disabled>{{ $dana->name }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
        {{-- @endif --}}
        <div class="col-sm-12 col-sm-12" id="reject_note">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Catatan Penolakan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" id="reject_notes" value ="{{ $detail->reject_notes }}" name="reject_notes" disabled>{{$detail->reject_notes}}</textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Diajukan Oleh') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    @php
                        $data = strip_tags($detail->createsByRaw());
                        $data = trim($data);
                    @endphp
                    <input class="form-control" type="text" name="created_by" value="{{ preg_replace('/\s+/', ' ', $data) }}" readonly>
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
