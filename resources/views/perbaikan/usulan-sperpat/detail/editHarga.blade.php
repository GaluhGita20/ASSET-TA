@extends('layouts.modal')

@section('action', route($routes . '.detailUpdateHarga', $detail->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="row">
                        {{-- <input type="hidden" class="form-control" name ="trans_perbaikan_id" value={{$record->id}}> --}}
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Nama Sperpat') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input type="text" class="form-control" name ="sperpat_name" value="{{$detail->sperpat_name}}" placeholder="{{__('Nama Sperpat')}}" disabled>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Spesifikasi Detail Sperpat') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <textarea name="desc_sper" class="base-plugin--summernote" value="{{$detail->desc_sper}}"  placeholder="{{ __('Keterangan Spesifikasi Sperpat') }}" data-height="200" disabled>{!! $detail->desc_sper  !!}</textarea>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Jumlah Item') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input type="number" class="form-control" id="qty" name="qty" value="{{$detail->qty}}"  placeholder="{{ __('Jumlah Sperpat') }}" min="1"  oninput="updateTotal()" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Harga Unit') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <div class="input-group">
                                        <input type="text" min=0 id ="unit_cost" name="unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Unit') }}" value="{{$detail->unit_cost}}"  oninput="updateTotal()" >
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
                                        <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Total') }}" value="{{$detail->total_cost}}" value="0" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text" >
                                                Rupiah
                                            </span>
                                        </div>
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

            quantity= quantity.replace(/[^0-9]/g, '');
            price= price.replace(/[^0-9]/g, '');


            quantity = parseInt(quantity);
            price = parseInt(price);
            
            if(price > 0)
                
                var total = parseInt(quantity) * parseInt(price);

                console.log(total)
                document.getElementById('total_cost').value = parseInt(total);
                document.getElementById('unit_cost').value = parseInt(price)
            }
    </script>

    <script src="{{ '/assets/js/global.js' }}"></script>
    <script>
        $('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
    </script>
@endpush


