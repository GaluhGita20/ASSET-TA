
@extends('layouts.pageSubmit')
@section('card-body')
@section('page-content')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">@yield('card-title', 'Pengajuan Pemutihan Aset ', $title)</h3>
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
                                <label class="col-sm-4 col-form-label">{{ __('Nama Aset') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="hidden" class="form-control" name="kib_id" value="{{ $record->asets->id }}">
                                    <input type="text" class="form-control" name="names" value="{{ $record->asets->asetData->name }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Type Aset') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->asets->type }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Kode Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="coa_id" class="form-control base-plugin--select2-ajax coa_id"
                                        data-url="{{ rut('ajax.selectCoa', ['c']) }}"
                                        data-url-origin="{{ rut('ajax.selectCoa', ['c']) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="" required>{{ __('Pilih Salah Satu') }}</option>
                                        @if (!empty($record->asets->coa_id))
                                            <option value="{{ $record->asets->coa_id }}" selected>{{ $record->asets->coad->nama_akun.' ( '.$record->asets->coad->kode_akun.' )'  }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Merek') }}</label>
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
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Pabrik') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->asets->no_factory))
                                        <input type="text" name="no_seri" class="form-control" value="{{ $record->asets->no_factory_item }}" disabled>
                                    @else
                                        <input type="text" name="no_seri" class="form-control" value="-" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Mesin') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->asets->no_machine_item))
                                        <input type="text" name="no_seri" class="form-control" value="{{ $record->asets->no_machine_item }}" disabled>
                                    @else
                                        <input type="text" name="no_seri" class="form-control" value="-" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Rangka') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->asets->no_frame))
                                        <input type="text" name="no_seri" class="form-control" value="{{ $record->asets->no_frame }}" disabled>
                                    @else
                                        <input type="text" name="no_seri" class="form-control" value="-" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->


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
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Pemutihan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input class="form-control base-plugin--datepicker" name="submission_date" placeholder="{{ __('Tanggal Pemutihan') }}" value="{{ $record->submission_date->format('d/m/Y') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Jenis Pemutihan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <select name="clean_type" class="form-control base-plugin--select2-ajax ref_jenis_pemutihan"
                                        data-url="{{ rut('ajax.selectJenisPemutihan', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectJenisPemutihan', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                        {{-- pemutihanType --}}
                                        @if ($record->pemutihanType)
                                            <option value="{{ $record->pemutihanType->id }}" selected disabled>
                                                {{ $record->pemutihanType->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Target Pemutihan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="target" placeholder="Target Pemutihan" value="{{$record->target}}" readonly>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Lokasi Pemutihan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="location" placeholder="Lokasi Pemutihan" value="{{$record->location}}" readonly>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Pendapatan Pemutihan') }}</label>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" id="valued" name="valued" value="{{$record->valued}}" oninput="updateTotal()" disabled>
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
                                    <label class="col-form-label">{{ __('Jumlah') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" placeholder="{{ __('Jumlah Aset') }}" min="1" value="{{$record->qty}}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">  
                                <label class="col-sm-2 col-form-label">{{ __('Penanggung Jawab Pemutihan') }}</label>
                                <div class="col-sm-10 parent-group">
                                    <select name="pic" class="form-control base-plugin--select2-ajax"
                                            data-url="{{ route('ajax.selectUser', ['search' => 'BPKAD']) }}"
                                            data-url-origin="{{ route('ajax.selectUser', ['search' => 'BPKAD']) }}"
                                            placeholder="{{ __('Pilih Petugas') }}" disabled>
                                        <option value="">{{ __('Pilih Petugas') }}</option>
                                        @if ($record->picd)
                                        <option value="{{ $record->picd->id }}" selected disabled>
                                            {{ $record->picd->name }}
                                        </option>
                                    @endif
                                    </select>
                                </div>
                            </div>
                        </div>    

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Pemutihan') }}</label>
                                <div class="col-10 parent-group">
                                    {{-- <div class="custom-file"> --}}
                                        {{-- <input type="hidden"
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
                                        <label class="custom-file-label" for="file">Choose File</label> --}}
                                    {{-- </div> --}}
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
                                                    {{-- <button type="button" class="close base-form--remove-temp-files" data-toggle="tooltip"
                                                        data-original-title="Remove">
                                                        <span aria-hidden="true">
                                                            <i class="ki ki-close"></i>
                                                        </span>
                                                    </button> --}}
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


        {{-- @if (request()->route()->getName() == $routes.'.show' )
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
                                                $module = 'penghapusan-aset';
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
                                                            @if($flow->role->name == 'Kepala Badan')
                                                                title="{{ $flow->show_type }}">Kepala BPKAD
                                                            @else 
                                                                title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                            @endif
                                                        </span>
                                            
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
        @endif --}}

        {{-- @if (request()->route()->getName() == $routes.'.approval') --}}
        {{-- <div class="col-md-6" style="margin-top:20px!important;">
            <div class="card card-custom" style="height:100%;">
                <div class="card-header">
                    <h4 class="card-title">Informasi</h4>
                </div>

                <div class="card-body" style="padding: 10px 1.75rem 10px 1.75rem;">
                    <div class="d-flex align-items-center justify-content-between flex-lg-wrap flex-xl-nowrap p-4">
                        <div class="d-flex flex-column mr-5">
                            <p class="text-dark-50">
                                Sebelum submit pastikan data alasan pemutihan aset disampaikan dengan jelas.
                            </p>
                        </div>

                        <div class="ml-lg-0 ml-xxl-6 ml-6 flex-shrink-0">
                            @php
                                $module = 'penghapusan-aset';
                                $menu = \App\Models\Globals\Menu::where('module', $module)->first();
                                $count = $menu->flows()->count();
                                $submit = $count == 0 ? 'disabled' : 'enabled';
                            @endphp
                            <div style="display: none">
                                @include('layouts.forms.btnBack')
                            </div>
                            <div class="btn-group dropup">
                                <div style="display: none">
                                    @include('layouts.forms.btnBack')
                                </div>
                                @include('layouts.forms.btnDropdownSubmit')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

    {{-- @endif --}}
    
@show
@endsection
