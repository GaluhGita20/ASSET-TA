@extends('layouts.pageSubmit')

@section('action', route('pengajuan.perbaikan-aset.store'))

@section('card-body')
@section('page-content')
    @method('POST')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">@yield('card-title', 'Pengajuan Perbaikan '.$title)</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nama Aset') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="hidden" class="form-control" name="kib_id" value="{{ $record->id }}">
                                    <input type="text" class="form-control" name ="name" value="{{ $record->usulans->asetd->name }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Merek') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->merek_type_item))
                                    <input type="text" name="merek" class="form-control" value="{{ $record->merek_type_item }}" disabled>
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
                                    @if(!empty($record->no_factory_item))
                                    <input type="text" name="no_seri" class="form-control" value="{{ $record->no_factory_item }}" disabled>
                                    @else
                                    <input type="text" name="no_seri" class="form-control" value="-" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Type Aset') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->type }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tahun Perolehan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->usulans->trans->receipt_date->format('Y') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Sumber Perolehan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->usulans->trans->source_acq }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nilai Buku') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" value="{{ number_format($record->book_value, 0, ',', ',') }}" disabled>
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
                                <label class="col-sm-4 col-form-label">{{ __('Unit Lokasi Aset') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <select class="form-control"  name="departemen_id">
                                        @if(!empty($record->usulans->perencanaan))
                                            <option value="{{ $record->usulans->perencanaan->struct_id}}" selected> {{ $record->usulans->perencanaan->struct->name }} </option>
                                        @else
                                            <option value="{{ $record->location_hibah_aset}}" selected> {{ $record->deps->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Ruang Lokasi Aset') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->locations->name))
                                        <input type="text" class="form-control" value="{{ $record->locations->name }}" disabled>
                                    @else
                                        <input type="text" class="form-control" value="-" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Pengajuan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="submission_date" value = "{{ now()->format('d/m/Y') }}" data-date-end-date="{{ now() }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Keluhan Aset') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <textarea name="problem" class="base-plugin--summernote" placeholder="{{ __('Keluhan Aset') }}" data-height="200">{!! $record->asset_test_results  !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Foto Kerusakan') }}</label>
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
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                @if (request()->route()->getName() == $routes.'.approval')
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        @if ($record->checkAction('approval', $perms))
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

    @php
        $colors = [
            1 => 'primary',
            2 => 'info',
        ];
    @endphp

    @if (request()->route()->getName() == $routes.'.repair')
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
                                            $module = 'perbaikan-aset';
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
                                    Sebelum submit pastikan data keluhan kerusakan aset disampaikan dengan jelas.
                                </p>
                            </div>

                            <div class="ml-lg-0 ml-xxl-6 ml-6 flex-shrink-0">
                                @php
                                    $module = 'perbaikan-aset';
                                    $menu = \App\Models\Globals\Menu::where('module', $module)->first();
                                    $count = $menu->flows()->count();
                                    $submit = $count == 0 ? 'disabled' : 'enabled';
                                @endphp
                                <div style="display: none">
                                    @include('layouts.forms.btnBack')
                                </div>
                                <div class="btn-group dropup">
                                    {{-- <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mr-1 fa fa-save"></i> {{ __('Simpan') }}</button> --}}
                                    {{-- <div class="dropdown-menu dropdown-menu-right"> --}}
                                        <button type="submit" class="btn btn-primary align-items-center base-form--submit-page" data-submit="0">
                                            <i class="mr-1 flaticon-interface-10 text-white"></i>
                                            {{ __('Submit') }}
                                        </button>
                                    {{-- </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
@show
@endsection
