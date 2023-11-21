<div class="row">
    <div class="col-sm-6">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">{{ __('No Surat') }}</label>
            <div class="col-sm-8 col-form-label">
                <input type="text" class="form-control" value="{{ $record->code }}" disabled>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">{{ __('Tgl Surat') }}</label>
            <div class="col-sm-8 col-form-label">
                <input type="text" class="form-control" value="{{ $record->date->format('d/m/Y') }}" disabled>
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
                    data-placeholder="{{ __('Unit Kerja') }}" disabled>
                    <option value="">{{ __('Unit Kerja') }}</option>
                    @if ($record->struct)
                        <option value="{{ $record->struct->id }}" selected>
                            {{ $record->struct->name }}
                        </option>
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-2 pr-0">
                <label class="col-form-label">{{ __('Perihal') }}</label>
            </div>
            <div class="col-10 parent-group">
                <input class="form-control" name="regarding" placeholder="{{ __('Perihal') }}" value="{{ $record->regarding }}" disabled>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-2 col-form-label">{{ __('Lampiran') }}</label>
            <div class="col-10 parent-group">
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
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 col-form-label">{{ __('Tembusan') }}</label>
            <div class="col-md-10 parent-group">
                <select name="cc[]" class="form-control base-plugin--select2-ajax"
                    data-url="{{ route('ajax.selectUser', ['search' => 'level_department']) }}" multiple
                    placeholder="{{ __('Pilih Beberapa') }}">
                    <option value="">{{ __('Pilih Beberapa') }}</option>
                    @foreach ($record->cc as $user)
                        <option value="{{ $user->id }}" selected>
                            {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
