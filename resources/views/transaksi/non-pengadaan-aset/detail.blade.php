@extends('layouts.pageSubmit')

@section('action', route($routes . '.update', $record->id))

@section('card-body')
@section('page-content')
    @method('PATCH')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Laporan Hibah Aset</h3>
                    {{-- <h3 class="card-title">Laporan Hibah Aset</h3> --}}
                    {{-- <h3 class="card-title">@yield('card-title', $title)</h3> --}}
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
                                    <input type="text" class="form-control" value={{ $record->source_acq }} placeholder="{{ __('Jenis Penerimaan') }}" readonly>
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
                        </div> -->

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Penerimaan Hibah Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Hasil Pengujian Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <textarea name="asset_test_results" class="base-plugin--summernote" placeholder="{{ __('Hasil Pengujian Aset') }}" data-height="200">{!! $record->asset_test_results  !!}</textarea>
                                    {{-- <input type="text" class="form-control" value={{ $record->source_acq }} placeholder="{{ __('Tahun Pengadaan') }}" readonly> --}}
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
                        @if ($record->checkAction('approval', $perms))
                            @include('layouts.forms.btnBack')
                            @include('layouts.forms.btnDropdownApproval2')
                            @include('layouts.forms.modalReject')
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $colors = [
            1 => 'primary',
            2 => 'info',
        ];
    @endphp

    @if (request()->route()->getName() == $routes.'.detail')
        <div class="row">
            <div class="col-md-6" style="margin-top:20px!important;">
                <div class="card card-custom" style="height:100%;">
                    <div class="card-header">
                        <h4 class="card-title">Alur Persetujuan</h4>
                    </div>
                    <div class="card-body" style="padding: 10px 1.75rem 10px 1.75rem;display:grid;">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="d-flex flex-column mr-5">
                                    <div class="d-flex align-items-center justify-content-center">
                                        @php
                                            $menu = \App\Models\Globals\Menu::where('module', $module)->first();
                                        @endphp

                                        @if ($menu->flows()->get()->groupBy('order')->count() == 0)
                                            <span class="label label-light-info font-weight-bold label-inline mt-3"
                                                data-toggle="tooltip">Data tidak tersedia.</span>
                                        @else
                                            @foreach ($orders = $menu->flows()->get()->groupBy('order') as $i => $flows)
                                                @foreach ($flows as $j => $flow)
                                                    <span class="label label-light-{{ $colors[$flow->type] }} font-weight-bold label-inline"
                                                        data-toggle="tooltip"
                                                        @if($flow->role->name == 'Umum')
                                                            title="{{ $flow->show_type }}">Departemen
                                                        @else 
                                                            title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                        @endif
                                                    </span>
                                                
                                                    {{-- <span
                                                        class="label label-light-{{ $colors[$flow->type] }} font-weight-bold label-inline"
                                                        data-toggle="tooltip"  {{  $flow->role->name == 'Umum'}}  ?? title="{{ $flow->show_type }}">Departemen
                                                        @else 
                                                            title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                        @endif
                                                    </span> --}}

                                                    @if (!($i === $orders->keys()->last() && $j === $flows->keys()->last()))
                                                        <i class="fas fa-angle-double-right text-muted mx-2"></i>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6" style="margin-top:20px!important;">
                <div class="card card-custom" style="height:100%;">
                    <div class="card-header">
                        <h4 class="card-title">Informasi</h4>
                    </div>

                    <div class="card-body" style="padding: 10px 1.75rem 10px 1.75rem;">
                        <div class="d-flex align-items-center justify-content-between flex-lg-wrap flex-xl-nowrap p-4">
                            <div class="d-flex flex-column mr-5">
                                <p class="text-dark-50">
                                    Sebelum submit pastikan data {!! $title !!} tersebut sudah sesuai.
                                </p>
                            </div>

                            <div class="ml-lg-0 ml-xxl-6 ml-6 flex-shrink-0">
                                @php
                                    $menu = \App\Models\Globals\Menu::where('module', $module)->first();
                                    $count = $menu->flows()->count();
                                    $submit = $count == 0 ? 'disabled' : 'enabled';
                                @endphp
                                <div style="display: none">
                                    @include('layouts.forms.btnBack')
                                </div>
                                @include('layouts.forms.btnDropdownSubmit')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@show
@endsection
