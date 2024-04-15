@extends('layouts.modal')

@section('action', route($routes . '.store'))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Unit Kerja') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <select name="departemen_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectPemeliharaan') }}"
                        data-placeholder="{{ __('Unit Kerja') }}">
                        <option value="">{{ __('Pilih Struktur Organisasi') }}</option>
                    </select>  
                </div>
            </div>
            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tanggal Pemeliharaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control base-plugin--datepicker" name="maintenance_date" placeholder="{{ __('Tanggal Pemeliharaan') }}">
                    {{-- <input name="dates" class="form-control base-plugin--datepicker"
                        placeholder="{{ __('Tanggal Pemeliharaan') }}" max="{{ now()->addMonths(1) }}" > --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
    </script>
@endpush


