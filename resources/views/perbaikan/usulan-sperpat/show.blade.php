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
                        <div class="alert alert-custom alert-light-primary fade show py-3"  role="alert">
                            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
                            <div class="alert-text text-primary">
                                <div class="text-bold">{{ __('Informasi') }}:</div>
                                @if($data['umur_tahun'] <= 5)
                                    <div class="mb-10px" style="white-space: pre-wrap;">Aset Ini Sudah Mengalami Perbaikan Sebanyak {{ count($data['perbaikan']) }} kali dan Nilai Aset Saat Ini Rp {{number_format($data['nilai'], 2) }} Rupiah dengan Umur Aset Sudah Mencapai {{ $data['umur_tahun'] }} tahun {{ $data['umur_bulan'] }} bulan, dan Biaya Perbaikan Aset Sebesar Rp {{number_format($data['biaya_perbaikan'], 2) }} Rupiah @if($data['biaya_perbaikan'] > $data['nilai_rekomen_50']) dan Melebihi 50 % dari Nilai Aset Saat Ini, Maka Sistem Merekomendasikan Untuk Melakukan Penghapusan Aset @else Maka Sistem Merekomendasikan Untuk Melakukan Perbaikan Aset karena Biaya Perbaikan Yang Tidak Mencapai 50 % dari Harga Aset Saat Ini @endif
                                    </div>
                                @else
                                    <div class="mb-10px" style="white-space: pre-wrap;">Aset Ini Sudah Mengalami Perbaikan Sebanyak {{ count($data['perbaikan']) }} kali dan Nilai Aset Saat Ini Rp {{number_format($data['nilai'], 2) }} Rupiah dengan Umur Aset Sudah Mencapai {{ $data['umur_tahun'] }} tahun {{ $data['umur_bulan'] }} bulan, dan Biaya Perbaikan Aset Sebesar Rp {{number_format($data['biaya_perbaikan'], 2) }} Rupiah @if($data['biaya_perbaikan'] > $data['nilai_rekomen_30']) dan Melebihi 50 % dari Nilai Aset Saat Ini, Maka Sistem Merekomendasikan Untuk Melakukan Penghapusan Aset @else Maka Sistem Merekomendasikan Untuk Melakukan Perbaikan Aset karena Biaya Perbaikan Yang Tidak Mencapai 30 % dari Harga Aset Saat Ini @endif
                                    </div>
                                @endif
                            </div>
                        </div>

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
                                        <option value="sperpat" {{ $record->repair_type =='sperpat' ? 'selected' : '-' }}>Pembelian Sperpat</option>
                                        <option value="vendor"  {{ $record->repair_type =='vendor' ? 'selected' : '-' }} >Sewa Vendor</option>
                                        <option value="sperpat dan vendor"  {{ $record->repair_type =='sperpat dan vendor' ? 'selected' : '-' }} >Beli Sperpat dan Sewa Vendor</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($record->source_fund_id)
                        <div class="col-sm-12">
                            <div class="form-group row">
                                {{-- <div class="col-2"> --}}
                                    <label class="col-sm-2 col-form-label">{{ __('Sumber Pendanaan') }}</label>
                                {{-- </div> --}}
                                <div class="col-sm-10 col-form-label">
                                    <select name="source_fund_id" id="source_fund_id" class="form-control base-plugin--select2-ajax" disabled>
                                            <option value="{{ $record->source_fund_id }}" selected>
                                                {{ $record->danad->name }}
                                            </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

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

                        @if($record->repair_type == 'vendor' || $record->repair_type == 'sperpat dan vendor')
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Jasa Vendor') }}</label>
                                    <div class="col-sm-10 col-form-label">
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sewa Vendor') }}"  value="{{$record->total_cost_vendor}}" disabled>
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

    @if($record->repair_type == 'sperpat' || $record->repair_type == 'sperpat dan vendor')
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
