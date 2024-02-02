@extends('layouts.pageSubmit')

@if($record->status == 'waiting.verify')
    @section('action', route($routes . '.updateSummary', $record->id))
@else
    @section('action', route($routes . '.update', $record->id))
@endif

@section('card-body')
@section('page-content')
@if($record->status == 'waiting.verify')
    @method('POST')
@else
    @method('PATCH')
@endif
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Pengajuan Perbaikan Aset {{ $record->asets->usulans->asetd->name}}</h3>
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
                                    @if($record->status == 'waiting.verify')
                                    <input type="hidden" class="form-control" name="kib_id" value="{{ $record->id }}">
                                    @else
                                    <input type="hidden" class="form-control" name="kib_id" value="{{ $record->asets->id }}">
                                    @endif
                                    <input type="text" class="form-control" value="{{ $record->asets->usulans->asetd->name }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Merek') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->merek_type_item))
                                    <input type="text" class="form-control" value="{{ $record->asets->merek_type_item }}" disabled>
                                    @else
                                    <input type="text" class="form-control" value="-" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Seri') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if(!empty($record->no_factory_item))
                                    <input type="text" class="form-control" value="{{ $record->asets->no_factory_item }}" disabled>
                                    @else
                                    <input type="text" class="form-control" value="-" disabled>
                                    @endif
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

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tahun Perolehan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->asets->usulans->trans->receipt_date->format('Y') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Sumber Perolehan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->asets->usulans->trans->source_acq }}" disabled>
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
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" value="{{ number_format($record->asets->book_value, 0, ',', ',') }}" disabled>
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
                                        @if(!empty($record->asets->usulans->perencanaan))
                                            <option value="{{ $record->asets->usulans->perencanaan->struct_id}}" selected> {{ $record->asets->usulans->perencanaan->struct->name }} </option>
                                        @else
                                            <option value="{{ $record->asets->location_hibah_aset}}" selected> {{ $record->asets->deps->name }}</option>
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
                                        <input type="text" class="form-control" value="{{ $record->asets->locations->name }}" disabled>
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
                                    <textarea name="problem" class="base-plugin--summernote" value="{{ $record->problem }}" placeholder="{{ __('Keluhan Aset') }}" data-height="200" disabled>{!! $record->problem  !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Diajukan Oleh') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    @php
                                        $data = strip_tags($record->createsByRaw());
                                        $data = trim($data);
                                        @endphp
                                    <input class="form-control" type="text" name="created_by" value="{{ preg_replace('/\s+/', ' ', $data) }}" readonly>
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
                    <h3 class="card-title">Hasil Perbaikan Aset {{ $record->asets->usulans->asetd->name}}</h3>
                    {{-- <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div> --}}
                </div>

                @if(empty($record->repair_date))
                    <div class="alert alert-custom alert-light-primary fade show py-4" role="alert">
                        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
                        <div class="alert-text text-primary">
                            <div class="text-bold">{{ __('Catatan') }}:</div>
                            <div class="mb-10px" style="white-space: pre-wrap;">Sebelum Melakukan Verfikikasi , Pastikan Tanggal Pemanggilan Diisi</div>
                        </div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="col-sm-12 mt-3">
                    <div class="form-group row mt-2">
                        <label class="col-sm-2 col-form-label">{{ __('Tanggal Pemanggilan') }}</label>
                        <div class="col-sm-10 col-form-label">
                            @if(!empty($record->repair_date))
                                <input name="repair_date" class="form-control" value="{{ $record->repair_date->format('Y/m/d') }}" disabled>
                            @else
                                @if(auth()->user()->hasRole('Sarpras') && request()->route()->getName() == $routes.'.edit')
                                    <input name="repair_date" class="form-control filter-control base-plugin--datepicker">
                                @else
                                    <input name="repair_date" value="-" class="form-control" disabled>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @if($record->status=='approved')
                <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{ __('Tindakan Perbaikan') }}</label>
                        <div class="col-sm-10 col-form-label">
                            @if(!empty($record->repair_date))
                                <textarea name="action_repair" class="base-plugin--summernote"  placeholder="{{ __('Tindakan Dilakukan') }}" data-height="200" >{!! $record->action_repair  !!}</textarea>
                            @else
                                <textarea name="action_repair" class="base-plugin--summernote"  placeholder="{{ __('Tindakan Dilakukan') }}" data-height="200" disabled> </textarea>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{ __('Hasil Perbaikan') }}</label>
                        <div class="col-sm-10 col-form-label">
                            @if(!empty($record->repair_date))
                                <select name="repair_results" class="form-control">
                                    <option value="SELESAI" {{ $record->repair_results == "SELESAI" ? 'selected':'' }}>{{ __('SELESAI') }}</option>
                                    <option value="BELUM SELESAI" {{ $record->repair_results == "BELUM SELESAI" ? 'selected':'' }}>{{ __('BELUM SELESAI') }}</option>
                                    <option value="ALAT TIDAK BISA DIGUNAKAN" {{ $record->repair_results == "ALAT TIDAK BISA DIGUNAKAN" ? 'selected':'' }}>{{ __('ALAT TIDAK BISA DIGUNAKAN') }}</option>
                                </select>
                            @else
                                <select name="repair_results" class="form-control" disabled>
                                    <option value="SELESAI" {{ $record->repair_results == "SELESAI" ? 'selected':'' }}>{{ __('SELESAI') }}</option>
                                    <option value="BELUM SELESAI" {{ $record->repair_results == "BELUM SELESAI" ? 'selected':'' }}>{{ __('BELUM SELESAI') }}</option>
                                    <option value="ALAT TIDAK BISA DIGUNAKAN" {{ $record->repair_results == "ALAT TIDAK BISA DIGUNAKAN" ? 'selected':'' }}>{{ __('ALAT TIDAK BISA DIGUNAKAN') }}</option>
                                </select>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group row">  
                        <label class="col-md-2 col-form-label">{{ __('Petugas Perbaikan') }}</label>
                        <div class="col-md-10 parent-group">
                            @if(!empty($record->repair_date))
                            <select name="user_id[]" class="form-control base-plugin--select2-ajax"
                                    data-url="{{ route('ajax.selectUser', ['search' => 'sarpras']) }}"
                                    data-url-origin="{{ route('ajax.selectUser', ['search' => 'sarpras']) }}"
                                    multiple
                                    placeholder="{{ __('Pilih Beberapa') }}" required>
                                <option value="">{{ __('Pilih Beberapa') }}</option>
                                @foreach ($record->petugas as $user)
                                    <option value="{{ $user->id }}" selected>
                                        {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                                    </option>
                                @endforeach
                            </select>
                            @else
                            <option disabled>{{ __('Pilih Beberapa') }}</option>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                @if (request()->route()->getName() == $routes.'.edit')
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        @if(auth()->user()->hasRole('Sarpras'))
                        {{-- @if ($record->checkAction('approval', $perms)) --}}
                            @include('layouts.forms.btnBack')
                            @if($record->repair_date ==  null && $record->status == 'waiting.verify')
                            <div class="d-flex flex-column mr-2 mt-2">
                                <p class="text-dark-50">
                                    Sebelum Melakukan Verifikasi Pastikan Tanggal Pemanggilan Diisi.
                                </p>
                            </div>
                            <div class="btn-group dropdown">
                                <button type="submit" class="btn btn-primary align-items-center base-form--submit-page" data-submit="0">
                                    <i class="mr-1 flaticon-interface-10 text-white"></i>
                                    {{ __('Verify') }}
                                </button>
                                {{-- </div> --}}
                            </div>
                            @else
                            <div class="btn-group dropdown">
                                <button type="submit" class="btn btn-success align-items-center base-form--submit-page" data-submit="1">
                                    <i class="mr-1 flaticon-interface-10 text-white"></i>
                                    {{ __('Update Repair') }}
                                </button>
                                {{-- </div> --}}
                            </div>
                            @endif
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
                                        <button type="submit" class="btn btn-primary align-items-center base-form--submit-page" data-submit="1">
                                            <i class="mr-1 flaticon-interface-10 text-success"></i>
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
