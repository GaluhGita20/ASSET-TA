@extends('layouts.modal')

@section('action', route($routes . '.updateSummary', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group row">
                <div class="col-4">
                    <label class="col-form-label">{{ __('No Surat') }}</label>
                </div>
                <div class="col-8 parent-group">
                    <input class="form-control" name="code" placeholder="{{ __('No Surat') }}" value="{{ $record->code }}" readonly>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group row">
                <div class="col-4">
                    <label class="col-form-label">{{ __('Tgl Surat') }}</label>
                </div>
                <div class="col-8 parent-group">
                    <input name="date" class="form-control base-plugin--datepicker"
                        placeholder="{{ __('Tgl Surat') }}" data-date-end-date="{{ now() }}" value="{{ $record->date->format('Y-m-d') }}" disabled>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Unit Kerja') }}</label>
                </div>
                <div class="col-10 parent-group">
                    {{-- @if($departemen->location->level == 'departement' || $departemen->location->level == 'bod' || $departemen->location->name == 'Sub Bagian Program Perencanaan') --}}
                    <select name="struct_id" class="form-control base-plugin--select2-ajax">
                        @if ($record->struct)
                            <option value="{{ $record->struct->id }}" selected>
                                {{ $record->struct->name }}
                            </option>
                        @endif
                    </select>
                    {{-- @endif --}}
                    {{-- @if($departemen->location->level == 'subdepartemen' || $departemen->location->name != 'Sub Bagian Program Perencanaan')
                        <select class="form-control"  name="struct_id">
                            <option value="{{ $departemen->location->id}}" selected> {{ $departemen->location->name }} </option>
                        </select>
                    @endif --}}
                </div>
            </div>

            {{-- <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Jenis Usulan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <select class="form-control" name="is_repair" data-placeholder="is_repair">
                        <option disabed value="">Jenis Pengadaan</option>
                        @if(auth()->user()->roles =='Sarpras')
                            <option value="yes" {{ $record->is_repair =='yes' ? 'selected':'-' }}>Pengajuan Perbaikan Aset</option>
                        @endif
                        <option value="no" {{ $record->is_repair =='no' ? 'selected':'-' }}>Pengajuan Pembelian Aset</option>
                    </select>
                </div>
            </div> --}}

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Perihal') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" name="regarding" placeholder="{{ __('Perihal') }}" value="{{ $record->regarding }}">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tahun Pengadaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" value={{ $record->procurement_year }} type="number" min="1900" max="2100" name="procurement_year" placeholder="{{ __('Tahun Pengadaan') }}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label">{{ __('Lampiran') }}</label>
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
@endsection

@push('scripts')
<script>
	$('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
</script>
@endpush
