@extends('layouts.modal')

@section('action', route($routes . '.store'))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('No Surat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="perbaikan_id" id="trans_perbaikan_id" class="form-control base-plugin--select2-ajax trans_perbaikan_id"
                                    data-url="{{ route('ajax.selectPerbaikan') }}"
                                        data-placeholder="{{ __('Pilih Usulan Perbaikan') }}">
                                    </select>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tipe Perbaikan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <select class="form-control" name="repair_type" data-placeholder="Tipe Perbaikan">
                                        <option disabed value="">Jenis Perbaikan</option>
                                        <option value="sperpat">Pembelian Sperpat</option>
                                        <option value="vendor">Sewa Vendor</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
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
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
    <!-- end of header -->
@push('scripts')
    <script>
        $('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
    </script>
@endpush

