@extends('layouts.modal')

@section('action', route($routes . '.detailStore', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="perencanaan_id" id="usulan" value="{{ $record->id }}">
    <input type="hidden" name="dep" id="dep" value="{{ $record->struct_id }}">
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="ref_aset_id" id="ref_aset_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectAsetRS', 'all') }}"
                        placeholder="{{ __('Pilih Salah Satu') }}">
                        <option value="">{{ __('Pilih Salah Satu') }}</option>

                        @if (isset($detail) && ($asetd = $detail->asetd))
                            <option value="{{ $asetd->id }}" selected>{{ $asetd->name }}</option>
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
                    <textarea class="form-control" name="desc_spesification" placeholder="{{ __('Spesifikasi Aset') }}"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12 offset-md-5 col-md-7">
                    <span style="font-size: 11px">{{ __('*Contoh Bahan: Kaca
                        Ukuran: 100 Ml
                        Panjang: 20 cm
                        Lebar : 20 cm
                        Frekuensi: 100Hz'
                        ) }}</span>
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
                            placeholder="{{ __('Standar Kebutuhan') }}" >
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
                            placeholder="{{ __('Jumlah Tersedia') }}">
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
                            placeholder="{{ __('Jumlah Pengajuan') }}" oninput="updateTotal()">
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
                    <label class="col-form-label">{{ __('Harga Unit') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <div class="input-group">
                        <input type="text" min=0 id ="HPS_unit_cost" name="HPS_unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Harga Unit') }}" required value="0" oninput="updateTotal()">
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
                            placeholder="{{ __('Harga Total Usulan') }}" value="0" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text" >
                                Rupiah
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>     
        {{-- @endif --}}
    </div>
@endsection

@push('scripts')
<script src="{{ '/assets/js/global.js' }}"></script>
    <script>
        function updateTotal() {
            var quantity = document.getElementById('qty_req').value;
            var price = document.getElementById('HPS_unit_cost').value;

            quantity= quantity.replace(/[^0-9]/g, '');
            price= price.replace(/[^0-9]/g, '');


            quantity = parseInt(quantity);
            price = parseInt(price);
            
            if(quantity > 0 && price > 0)
                
                var total = parseInt(quantity) * parseInt(price);

                console.log(total)
                document.getElementById('HPS_total_cost').value = parseInt(total);
                document.getElementById('HPS_unit_cost').value = parseInt(price)
        }


        </script>
    <script>
        $("#ref_aset_id").on('change', function() {
            var me = $(this);
            var dep = $('#dep');
            var usulan = $('#usulan');

            console.log(me.val());

            $.ajax({
                type: 'POST',
                url: '/ajax/checkAset',
                data: {
                    _token: BaseUtil.getToken(),
                    id: me.val(), 
                    dep: dep.val(),
                    usulan :usulan.val(),
                },
                success: function(resp) {
                    // var jumlah = resp[0].qty;

                    // // $('#tempName').val(coa);
                    // if (no_factory !== null || no_frame !== null) {
                    //     $('#qty').val(1);
                    // }

                    console.log(resp);
                },
                error: function(resp) {
                    console.log(resp)
                    console.log('error')
                },
            });
        });
    </script>

    <script>
        $('.modal-dialog-right-bottom').removeClass('modal-lg').addClass('modal-md');
    </script>
@endpush