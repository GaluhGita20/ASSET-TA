@extends('layouts.pageSubmit')

{{-- @section('action', route('pengajuan.perbaikan-aset.approve',$record->id)) --}}
{{-- @section('action', rut($routes . '.reject', $record->id)) --}}
@section('card-body')
@section('page-content')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">@yield('card-title', $title)</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>

                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('No Surat Pemeliharaan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input name="text" class="form-control"
                                    placeholder="{{ __('Nomor Surat Pemeliharaan') }}"  value="{{ $record->code }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Unit Kerja') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <select class="form-control" name="departemen_id" disabled>
                                        @if(!empty($record->departemen_id))
                                            <option value="{{ $record->deps->id}}" selected> {{ $record->deps->name }} </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Pemeliharaan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input name="dates" class="form-control base-plugin--datepicker"
                                    placeholder="{{ __('Tanggal Pemeliharaan') }}"  value="{{ $record->maintenance_date->format('d/m/Y') }}"  disabled>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Pemeliharaan') }}</label>
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
                                            accept="*" disabled>
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
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pemeliharaan Aset</h3>
                </div>

                <div class="card-body p-8">
                    @include('pemeliharaan.pemeliharaan-aset.detail.index')
                </div>

            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 2 -->
    @if (request()->route()->getName() == $routes.'.approval')
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                @if ($record->checkAction('approval', $perms))
                    @include('layouts.forms.btnBack')
                    @include('layouts.forms.btnDropdownApproval2')
                    @include('layouts.forms.modalReject')
                @endif
            </div>
        </div>
    @endif
{{-- @show --}}
@endsection
