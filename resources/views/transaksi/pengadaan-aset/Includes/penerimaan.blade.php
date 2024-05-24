<div class="row mb-3">
    <div class="col-sm-12">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-toolbar">
                    <h3 class="card-title">Laporan Penerimaan</h3>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">{{ __('Bukti Nota Pembelian') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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

                    
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <div class="col-4 pr-0">
                                <label class="col-form-label">{{ __('Tanggal Penerimaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                            </div>
                            <div class="col-8 parent-group">
                                @if($record->receipt_date == null)
                                    <input class="form-control base-plugin--datepicker" name="receipt_date" value="{{$record->receipt_date}}" placeholder="{{ __('Tanggal Penerimaan') }}" >
                                @else
                                    <input class="form-control base-plugin--datepicker" name="receipt_date" value="{{$record->receipt_date->format('d/m/Y')}}" placeholder="{{ __('Tanggal Penerimaan') }}">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <div class="col-4 pr-0">
                                <label class="col-form-label">{{ __('Kode Faktur Penerimaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                            </div>
                            <div class="col-8 parent-group">
                                <input type="text" class="form-control" name="faktur_code" value="{{ $record->faktur_code }}" placeholder="{{ __('Kode Faktur Penerimaan') }}" >
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group row">
                            <div class="col-2 pr-0">
                                <label class="col-form-label">{{ __('Lokasi Penerimaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                            </div>
                            <div class="col-10 parent-group">
                                <input class="form-control" name="location_receipt" value="{{ $record->location_receipt }}" placeholder="{{ __('Lokasi Penerimaan') }}" >
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group row">
                            <div class="col-2 pr-0">
                                <label class="col-form-label">{{ __('Hasil Uji Fungsi Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                            </div>
                            <div class="col-10 parent-group">
                                <textarea class="base-plugin--summernote" name="asset_test_results" value="{{ $record->asset_test_results }}" placeholder="{{ __('Hasil Uji Fungs Aset') }}" data-height="200">{{ $record->asset_test_results }}</textarea>
                            </div>
                        </div>
                    </div> 

                    
                    <div class="col-sm-12">
                        <div class="form-group row">  
                            <label class="col-md-2 col-form-label">{{ __('Penguji Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                            <div class="col-md-10 parent-group">
                                <select name="user_id[]" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ route('ajax.selectUser', ['search' => 'all']) }}"
                                        data-url-origin="{{ route('ajax.selectUser', ['search' => 'all']) }}"
                                        multiple
                                        placeholder="{{ __('Pilih Beberapa') }}" required>
                                    <option value="">{{ __('Pilih Beberapa') }}</option>
                                    @foreach ($record->pengujianPengadaan as $user)
                                        <option value="{{ $user->id }}" selected>
                                            {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                                        </option>
                                    @endforeach
                                </select>

                                
                            </div>
                        </div>
                    </div>

                   

                </div>
            </div>
        </div>
    </div>
</div>