@extends('layouts.modal')

@section('action', route($routes . '.detailUpdateApprove', $detail->id))

@section('modal-body')
    @method('POST')
    {{-- <input type="hidden" name="is_submit" value=1> --}}
    <input type="hidden" name="detail_id" value="{{ $detail->id }}">
    <input type="hidden" name="perencanaan_id" value="{{ $detail->id }}">
    {{-- <input type="hidden" name="pembelian_id" value="{{ $detail->id }}"> --}}
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                    <label class="col-form-label">{{ __('Standar Kebutuhan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                    <label class="col-form-label">{{ __('Jumlah Tersedia') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=0 name="existing_amount" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah Tersedia') }}" value="{{ $detail->existing_amount }}" readonly>
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
                    <label class="col-form-label">{{ __('Jumlah Pengajuan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=1 id ="qty_req" name="qty_req" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah Pengajuan') }}" value="{{ $detail->qty_req }}" oninput="updateTotal()" readonly oninput="updateRejectNoteStatus()">
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
                    <label class="col-form-label">{{ __('Harga Unit') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
        
        @if(auth()->user()->hasRole('Sub Bagian Program Perencanaan'))
        <div class="col-sm-12 col-sm-12" >
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Jumlah Disetujui') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" id="qty_agree" min=0 name="qty_agree" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah Disetujui') }}" value="{{ $detail->qty_agree ?  $detail->qty_agree : 0}}" oninput="updateTotal()" required autofocus style="border: 1px solid #007bff;">
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
                            placeholder="{{ __('Harga Total Disetujui') }}" value="{{ $detail->HPS_total_agree ?  $detail->HPS_total_agree : 0}}" readonly  >
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
                    <select name="source_fund_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ rut('ajax.selectSSBiaya', [
                            'search'=>'all'
                        ]) }}" autofocus style="border: 1px solid #007bff;" data-placeholder="{{ __('Pilih Salah Satu Sumber Pendanaan') }}">
                        <option value="" selected>{{ __('Pilih Salah Satu Sumber Pendanaan') }}</option>
                        @if (isset($detail) && ($dana = $detail->danad))
                            <option value="{{ $dana->id }}" selected>{{ $dana->name }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12 offset-md-5 col-md-7">
                    <span style="font-size: 11px">{{ __('Sumber Pendanaan Diisi Ketika Jumlah Disetujui Lebih dari 0 (Nol)') }}</span>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12" id="reject_note">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Catatan Penolakan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" id="reject_notes" value ="{{ $detail->reject_notes }}" name="reject_notes" placeholder="{{ __('Catatan Penolakan DetaiL Aset') }}" >{{ $detail->reject_notes }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12 offset-md-5 col-md-7">
                    <span style="font-size: 11px">{{ __('Catatan Penolakan Diisi Ketika Jumlah Disetujui Kurang Dari Jumlah Pengajuan') }}</span>
                </div>
            </div>
            {{-- <span>
                <strong>{{"__(Catatan Penolakan Diisi Ketika Jumlah Disetujui Kurang Dari Jumlah Diajukan)"}}</strong>
            </span> --}}
        </div>
        
        @endif
    </div>
@endsection

@push('scripts')

    <script>
        function updateTotal() {
            var quantity = document.getElementById('qty_req').value;
            var price = document.getElementById('HPS_unit_cost').value;
            var quantity_agree = document.getElementById('qty_agree').value;

            quantity= quantity.replace(/[^0-9]/g, '');
            quantity_agree= quantity_agree.replace(/[^0-9]/g, '');
            price= price.replace(/[^0-9]/g, '');


            quantity = parseInt(quantity);
            quantity_agree = parseInt(quantity_agree);
            price = parseInt(price);

            
            if(quantity > 0 && price > 0)
                
                var total = parseInt(quantity) * parseInt(price);
                var total_agree = quantity_agree * price;

                document.getElementById('HPS_total_cost').value = parseInt(total);
                document.getElementById('HPS_unit_cost').value = parseInt(price)
                document.getElementById('HPS_total_agree').value = parseInt(total_agree);

               // var rejectNoteInput = document.getElementById('reject_note');
                var rejectNoteInputDetail = document.getElementById('reject_notes');

                // if (quantity_agree !== undefined && quantity !== undefined) {
                //     if (quantity_agree < quantity || quantity_agree == 0 ) {
                //       // rejectNoteInput.style.display = 'block'; 
                //        rejectNoteInputDetail.setAttribute('required');
                //     } else {
                //        rejectNoteInputDetail.removeAttribute('required');
                //        //rejectNoteInput.style.display = 'none'; 
                //     }
                // }
            }
            
        // Panggil fungsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            updateTotal();
           // updateRejectNoteStatus();
        });
    </script>

    <script src="{{ '/assets/js/global.js' }}"></script>
    <script>
	    $('.modal-dialog-right-bottom').removeClass('modal-lg').addClass('modal-md');
    </script>
@endpush
