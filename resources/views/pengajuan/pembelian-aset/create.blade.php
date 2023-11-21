@extends('layouts.modal')

@section('action', route($routes . '.store'))

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
                    <input class="form-control" name="code" placeholder="{{ __('No Surat') }}">
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
                    <select name="struct_id" class="form-control base-plugin--select2-ajax"
                        data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                        data-placeholder="{{ __('Unit Kerja') }}">
                        <option value="">{{ __('Unit Kerja') }}</option>
                    </select>
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
