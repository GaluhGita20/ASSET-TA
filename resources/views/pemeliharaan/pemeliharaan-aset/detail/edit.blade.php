@extends('layouts.modal')

@section('action', route($routes . '.detailUpdate', $detail->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="detail_id" value="{{ $detail->id }}">
    <input type="hidden" name="pemeliharaan_id" value="{{ $detail->pemeliharaan_id }}">
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="ref_aset_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectAsetKib', 'all') }}"
                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                        @if (isset($detail->asetd) && ($asetd = $detail->asetd))
                            <option value="{{ $asetd->id }}" selected disabled>{{ $asetd->usulans->asetd->name }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Merek') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <input class="form-control" name="merek" value = "{{ $detail->asetd->merek_type_item }}"  disabled>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Kondisi Awal Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="first_condition" value = "{{ $detail->first_condition }}" placeholder="{{ __('Kondisi Awal Aset') }}">{{ $detail->first_condition }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Kondisi Setelah Pemeliharaan Aset') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="latest_condition" value="{{ $detail->latest_condition }}" placeholder="{{ __('Kondisi Setelah Pemeliharaan Aset') }}">{{ $detail->latest_condition }} </textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Tindakan Pemeliharaan Dilakukan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="maintenance_action" value="{{ $detail->maintenance_action }}" placeholder="{{ __('Tindakan Pemeliharaan Dilakukan') }}">{{ $detail->maintenance_action }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">  
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Petugas Pemeliharaan') }}</label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="repair_officer" class="form-control base-plugin--select2-ajax"
                            data-url="{{ route('ajax.selectUser', ['search' => 'sarpras']) }}"
                            data-url-origin="{{ route('ajax.selectUser', ['search' => 'sarpras']) }}"
                            placeholder="{{ __('Pilih Petugas') }}" required>
                        <option value="">{{ __('Pilih Petugas') }}</option>

                        {{-- @foreach ($detail->repair_officer as $user) --}}
                        @if(!empty($detail->repair_officer))
                            <option value="{{ $detail->repair_officer }}" selected>
                                {{ $detail->petugas->name}}
                            </option>
                        @endif
                        {{-- @endforeach --}}
                    </select>
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