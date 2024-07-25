@extends('layouts.modal')

@section('action', route($routes . '.detailStore', $record->id))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" class="form-control" name ="trans_perbaikan_id" value={{$record->id}}>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Nama Sperpat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <input type="text" class="form-control" name ="sperpat_name" placeholder="{{__('Nama Sperpat')}}">
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Spesifikasi Detail Sperpat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <textarea name="desc_sper" class="base-plugin--summernote" placeholder="{{ __('Keterangan Spesifikasi Sperpat') }}" data-height="200"></textarea>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Jumlah Item') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <input type="number" class="form-control" id="qty" name="qty" placeholder="{{ __('Jumlah Sperpat') }}" min="1"  oninput="updateTotal()">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Harga Unit') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <div class="input-group">
                                        <input type="text" min=0 id ="unit_cost" name="unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Unit') }}"  oninput="updateTotal()">
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
                                <label class="col-sm-2 col-form-label">{{ __('Harga Total') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <div class="input-group">

                                        <input type="text" min="0" name="total_cost" id="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Total') }}" value="0"  oninput="updateTotal()" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>

                                        {{-- <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Total') }}" value="0"  readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text" >
                                                Rupiah
                                            </span>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
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
            var quantity = document.getElementById('qty').value;
            var price = document.getElementById('unit_cost').value;

            quantity = quantity.replace(/[^0-9]/g, '');
            price = price.replace(/[^0-9]/g, '');

            quantity = parseInt(quantity);
            price = parseInt(price);
            
            if (quantity > 0 && price > 0) {
                var total = quantity * price;
                // document.getElementById('total_cost').value = parseInt(1000);
                console.log(total);
                document.getElementById('total_cost').value = total;
                // document.getElementById('unit_cost').value = price;
            }
        }

    </script>

    {{-- <script src="{{ '/assets/js/global.js' }}"></script> --}}
    {{-- <script>
        $('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
    </script> --}}
@endpush


