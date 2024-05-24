@extends('layouts.pageSubmit')

@section('action', rut($routes . '.reject', $record->id))

@section('card-body')
@section('page-content') 

@method('PATCH')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">@yield('card-title', 'Pengajuan Usulan Sperpat ', $title)</h3>
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
                                <label class="col-sm-2 col-form-label">{{ __('No Surat') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="trans_perbaikan_id" id="trans_perbaikan_id" class="form-control base-plugin--select2-ajax trans_perbaikan_id"
                                        data-url="{{ route('ajax.selectPerbaikan') }}"
                                        data-placeholder="{{ __('Pilih Usulan Perbaikan') }}" value="{{$record->trans_perbaikan_id}}" disabled>
                                        @if ($record->codes)
                                        <option value="{{ $record->codes->id }}" selected>
                                            {{ $record->codes->code }}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Pengajuan Usulan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->submission_date->format('d/m/Y') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Periode Usulan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" value="{{ $record->procurement_year }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tipe Perbaikan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <select class="form-control" name="repair_type" value="{{$record->repair_type}}" data-placeholder="Tipe Perbaikan" disabled>
                                        <option disabed value="">Jenis Perbaikan</option>
                                        <option value="Tanah" {{ $record->repair_type =='sperpat' ? 'selected' : '-' }}>Pembelian Sperpat</option>
                                        <option value="Peralatan Mesin"  {{ $record->repair_type =='vendor' ? 'selected' : '-' }} >Sewa Vendor</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                {{-- <div class="col-2"> --}}
                                    <label class="col-sm-2 col-form-label">{{ __('Sumber Pendanaan') }}</label>
                                {{-- </div> --}}
                                <div class="col-sm-10 col-form-label">
                                    <select name="source_fund_id" id="source_fund_id" class="form-control base-plugin--select2-ajax">
                                        @if ($record->source_fund_id)
                                            <option value="{{ $record->source_fund_id }}" selected>
                                                {{ $record->danad->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Vendor') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="vendor_id" class="form-control base-plugin--select2-ajax vendor_id"
                                            data-url="{{ rut('ajax.selectVendor', [
                                                'search'=>'all'
                                            ]) }}"
                                            data-url-origin="{{ rut('ajax.selectVendor', [
                                                'search'=>'all'
                                            ]) }}"
                                            placeholder="{{ __('Pilih Salah Satu') }}"  value="{{$record->vendor_id}}" disabled>
                                            <option value="">{{ __('Pilih Salah Satu') }}</option>
                                            @if ($record->vendor_id)
                                            <option value="{{ $record->vendors->id }}" selected>
                                                {{ $record->vendors->name }}
                                            </option>
                                            @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($record->repair_type == 'vendor')
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Sewa Vendor') }}</label>
                                    <div class="col-sm-10 col-form-label">
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sewa Vendor') }}"  value="{{$record->total_cost}}" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    @if($record->repair_type == 'sperpat')
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Informasi Detail Sperpat</h3>
                </div>
                <div class="card-body p-8">
                    @include('perbaikan.usulan-sperpat.detail.index')
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                @if (request()->route()->getName() == $routes.'.approval')
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                        @if(auth()->user()->hasRole('Sub Bagian Program Perencanaan'))
                            @include('layouts.forms.btnTrxAset2')
                        @else
                        {{-- @include('layouts.forms.btnTrxAset2') --}}
                            @include('layouts.forms.btnDropdownApproval')
                        @endif
                        @include('layouts.forms.modalReject')
                        {{-- @if (auth()->user()->hasRole('Sub Bagian Program Perencanaan') || auth()->user()->hasRole('Direksi'))
                            @include('layouts.forms.btnBack')
                            @include('layouts.forms.btnDropdownApproval')
                            @include('layouts.forms.modalReject')
                        @endif --}}
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

    {{-- @if (request()->route()->getName() == $routes.'.detail' || request()->route()->getName() == $routes.'.show' )
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
                                            $module = 'usulan_pembelian-sperpat';
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
        </div>
    @endif --}}
@show
@endsection
