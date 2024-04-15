@extends('layouts.modal')

@section('action', route($routes . '.updateSummary', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    
    <div class="row">

        <div class="col-sm-6">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">{{ __('Nama Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                <div class="col-sm-8 col-form-label">
                    <input type="hidden" class="form-control" name="kib_id" value="{{ $record->asets->id }}">
                    <input type="text" class="form-control" name="names" value="{{ $record->asets->asetData->name }}" disabled>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">{{ __('Merek') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                <div class="col-sm-8 col-form-label">
                {{-- {{dd($record->asets->merek_type_item)}} --}}
                    @if(!empty($record->asets->merek_type_item))
                    <input type="text" name="merek" class="form-control" value="{{ $record->asets->merek_type_item }}" disabled>
                    @else
                    <input type="text" name="merek" class="form-control" value="-" disabled>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">{{ __('Nomor Seri') }}</label>
                <div class="col-sm-8 col-form-label">
                    @if(!empty($record->asets->no_factory))
                    <input type="text" name="no_seri" class="form-control" value="{{ $record->asets->no_factory }}" disabled>
                    @else
                    <input type="text" name="no_seri" class="form-control" value="-" disabled>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">{{ __('Tipe Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                <div class="col-sm-8 col-form-label">
                    <input type="text" class="form-control" value="{{ $record->asets->type }}" disabled>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pemutihan Aset</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input class="form-control base-plugin--datepicker" name="submmission_date" placeholder="{{ __('Tanggal Pemutihan') }}" value="{{ $record->submmission_date->format('d/m/Y') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Jenis Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <select name="clean_type" class="form-control base-plugin--select2-ajax ref_jenis_pemutihan"
                                        data-url="{{ rut('ajax.selectJenisPemutihan', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectJenisPemutihan', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        {{-- pemutihanType --}}
                                        @if ($record->pemutihanType)
                                            <option value="{{ $record->pemutihanType->id }}" selected>
                                                {{ $record->pemutihanType->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Target Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="target" placeholder="Target Pemutihan" value="{{$record->target}}">
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Lokasi Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="location" placeholder="Lokasi Pemutihan" value="{{$record->location}}">
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Pendapatan Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" id="valued" name="valued" value="{{$record->valued}}" oninput="updateTotal()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" placeholder="{{ __('Jumlah Aset') }}" min="1" value="{{$record->qty}}" >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">  
                                <label class="col-sm-4 col-form-label">{{ __('Penanggung Jawab Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 parent-group">
                                    <select name="pic" class="form-control base-plugin--select2-ajax"
                                            data-url="{{ route('ajax.selectUser', ['search' => 'BPKAD']) }}"
                                            data-url-origin="{{ route('ajax.selectUser', ['search' => 'BPKAD']) }}"
                                            placeholder="{{ __('Pilih Petugas') }}" required>
                                        <option value="">{{ __('Pilih Petugas') }}</option>
                                        @if ($record->picd)
                                        <option value="{{ $record->picd->id }}" selected>
                                            {{ $record->picd->name }}
                                        </option>
                                    @endif
                                    </select>
                                </div>
                            </div>
                        </div>    

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-10 parent-group">
                                    <div class="custom-file">
                                        <input type="hidden"
                                            name="uploads[uploaded]"
                                            class="uploaded"
                                            value="0">
                                        <input type="file" multiple
                                            class="custom-file-input base-form--save-temp-files"
                                            data-name="uploads"
                                            data-container="parent-group"
                                            data-max-size="30024"
                                            data-max-file="100"
                                            accept="*">
                                        <label class="custom-file-label" for="file">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">*Maksimal 20MB</div>
                                    @foreach ($record->files as $file)
                                    <div class="progress-container w-100" data-uid="{{ $file->id }}">
                                    
                                            <div class="alert alert-custom alert-light fade show py-2 px-3 mb-0 mt-2 success-uploaded" role="alert">
                                                <div class="alert-icon">
                                                    <i class="{{ $file->file_icon }}"></i>
                                                </div>
                                                <div class="alert-text text-left">
                                                    <input type="hidden" name="uploads[files_ids][]" value="{{ $file->id }}">
                                                    <div>Uploaded File:</div>
                                                    <a href="{{ $file->file_url }}" target="_blank" class="text-primary">
                                                        {{ $file->file_name }}
                                                    </a>
                                                </div>
                                                <div class="alert-close">
                                                    <button type="button" class="close base-form--remove-temp-files" data-toggle="tooltip"
                                                        data-original-title="Remove">
                                                        <span aria-hidden="true">
                                                            <i class="ki ki-close"></i>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                    </div>
                                    @endforeach
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
        var valued = document.getElementById('valued').value;
    
        valued= valued.replace(/[^0-9]/g, '');
    
        valued = parseInt(valued);
    }

</script>
@endpush
