@extends('layouts.pageSubmit')
@section('action', rut($routes . '.reject', $record->id))

{{-- @extends('layouts.pageSubmit')

@section('action', route($routes . '.update', $record->id)) --}}

@section('card-body')
@section('page-content')
    {{-- @method('PATCH')
    @csrf --}}
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Laporan Hibah Aset</h3>
                    {{-- <h3 class="card-title">@yield('Laporan Hibah Aset')</h3> --}}
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>
                
                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                    <div class="row">
   
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nama Transaksi') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->trans_name }}" disabled>
                                </div>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Vendor') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <select name="vendor_id" class="form-control base-plugin--select2-ajax vendor_id">
                                        @if ($record->vendor_id)
                                            <option value="{{ $record->vendors->id }}" selected disabled>
                                                {{ $record->vendors->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Penerimaan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->receipt_date->format('d/m/Y') }}" disabled>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Jenis Penerimaan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value={{ $record->source_acq }} placeholder="{{ __('Tahun Pengadaan') }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Lokasi Penerimaan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input class="form-control" name="location_receipt" value="{{ $record->location_receipt }}" placeholder="{{ __('Lokasi Penerimaan') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Penerimaan') }}</label>
                                <div class="col-10 parent-group">
                                    <div class="form-text text-muted">*Maksimal 20MB</div>
                                        @foreach ($record->files()->where('flag', 'uploads')->get() as $file)
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
                        </div> -->

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Hasil Pengujian Aset') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <textarea name="asset_test_results" class="base-plugin--summernote" placeholder="{{ __('Hasil Pengujian Aset') }}" data-height="200" disabled>{!! $record->asset_test_results  !!}</textarea>
                                    {{-- <input type="text" class="form-control" value={{ $record->source_acq }} placeholder="{{ __('Tahun Pengadaan') }}" readonly> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">  
                                <label class="col-md-2 col-form-label">{{ __('Penguji Aset') }}</label>
                                <div class="col-md-10 parent-group">
                                    <input type="hidden" name="user_id" value="{{ implode(',', $record->pengujianPengadaan->pluck('id')->toArray()) }}">
                                    @foreach ($record->pengujianPengadaan as $i => $user)
                                        <p>
                                            {{ ($i + 1) . ". {$user->name} (" . (empty($user->position) ? '' : $user->position->name) . ")" }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 2 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Lampiran Daftar Penerimaan</h3>
                </div>
                <div class="card-body p-8">
                @include('transaksi.non-pengadaan-aset.detail.index')
                </div>
            </div>
        </div>
    </div>

    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">

                @if (request()->route()->getName() == $routes.'.approval')
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        @if ($record->checkAction('approval', $perms) || auth()->user()->position->level == "kepala" &&  auth()->user()->position->location->level == "department" )
                            @include('layouts.forms.btnBack')
                            @include('layouts.forms.btnDropdownApproval')
                            @include('layouts.forms.modalReject')
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
{{-- @show --}}
@endsection
