@extends('layouts.modal')

@section('action', route($routes . '.store'))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    <input type="hidden" name="is_submit" value="0">
    {{-- @dump($errors) --}}
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group row">
                <div class="col-4">
                    <label class="col-form-label">{{ __('No Surat') }}</label>
                </div>
                <div class="col-8 parent-group">
                    <input class="form-control" name="code" placeholder="{{ __('No Surat') }}">
                    @error('code')
                        <div class="invalid-feedback" style="display:block;">
                            {{  $message }}
                        </div>
                    @enderror
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
                        placeholder="{{ __('Tgl Surat') }}" data-date-end-date="{{ now() }}">
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Unit Kerja') }}</label>
                </div>
                <div class="col-10 parent-group">
                    @if($departemen->location->level == 'department' || $departemen->location->name == 'Sub Bagian Program Perencanaan')
                        <select name="struct_id" class="form-control base-plugin--select2-ajax"
                            data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                            data-placeholder="{{ __('Unit Kerja') }}">
                            <option value="">{{ __('Pilih Struktur Organisasi') }}</option>
                        </select>   
                    @endif
                    @if($departemen->location->level == 'subdepartmen' || $departemen->location->name != 'Sub Bagian Program Perencanaan' && $departemen->location->level != 'department')
                        <select class="form-control"  name="struct_id">
                            <option value="{{ $departemen->location->id}}" selected> {{ $departemen->location->name }} </option>
                        </select>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Perihal') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" name="regarding" placeholder="{{ __('Perihal') }}">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tahun Perencanaan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" type="number" min="1900" max="2100" name="procurement_year" placeholder="{{ __('Tahun Perencanaan') }}">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Jenis Usulan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <select class="form-control" name="is_repair" data-placeholder="is_repair">
                        <option disabed value="">Jenis Pengadaan</option>
                        @if(auth()->user()->roles =='Sarpras')
                            <option value="yes">Pengajuan Perbaikan Aset</option>
                        @endif
                        <option value="no">Pengajuan Pembelian Aset</option>
                    </select>
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
