@extends('layouts.modal')

@section('action', route($routes . '.detailStore', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="trans_id" value="{{ $record->id }}">
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="ref_aset_id" class="form-control base-plugin--select2-ajax"
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
                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="desc_spesification" placeholder="{{ __('Spesifikasi Aset') }}"></textarea>
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
                            placeholder="{{ __('Harga Unit') }}" required >
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
                            placeholder="{{ __('Jumlah Diterima') }}"required>
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