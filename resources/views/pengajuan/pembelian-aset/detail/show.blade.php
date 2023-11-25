@extends('layouts.modal')

@section('action', route($routes . '.detailStore', $detail->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="code" value="{{ $detail->pembelian_id }}">
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Akun') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="coa_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectCoa', 'all') }}"
                        data-placeholder="{{ __('Nama Akun') }}"
                        @if($detail->coa_id != NULL) disabled @endif>
                        <option value="">{{ __('Pilih Kode Aset') }}</option>
                        <option value="{{ $detail->coa->id }}" selected>{{ $detail->coa->nama_akun }}</option>
                    </select>
             

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
                        <input type="text" name="requirement_standard" class="form-control base-plugin--inputmask_currency text-right"
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
                        <input type="text" name="existing_amount" class="form-control base-plugin--inputmask_currency text-right"
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
                        <input type="text" name="qty_req" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Jumlah Pengajuan') }}" value="{{ $detail->qty_req }}" readonly>
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
    <script src="{{ '/assets/js/global.js' }}"></script>
    <script>
	    $('.modal-dialog-right-bottom').removeClass('modal-lg').addClass('modal-md');
    </script>
@endpush
