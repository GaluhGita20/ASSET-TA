@extends('layouts.modal')

@section('action', route($routes . '.detailStore', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="code" value="{{ $record->id }}">
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Akun') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="coa_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectCoa', 'all') }}"
                        data-placeholder="{{ __('Nama Akun') }}">
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
                            placeholder="{{ __('Standar Kebutuhan') }}">
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
                            placeholder="{{ __('Jumlah yang Ada') }}">
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
                            placeholder="{{ __('Jumlah Pengajuan') }}">
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
