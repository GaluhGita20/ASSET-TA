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
                                    <select class="form-control" name="repair_type" id="rep_type" data-placeholder="Tipe Perbaikan" disabled>
                                        <option disabled value="">Jenis Perbaikan</option>
                                        <option value="sperpat" {{ $record->repair_type == 'sperpat' ? 'selected' : '' }}>Pembelian Sperpat</option>
                                        <option value="vendor" {{ $record->repair_type == 'vendor' ? 'selected' : '' }}>Sewa Vendor</option>
                                    </select>                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Vendor') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="vendor_id" id="vendor_id" class="form-control base-plugin--select2-ajax vendor_id"
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
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Sewa Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                    <div class="col-sm-10 col-form-label">
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sewa Vendor') }}" autofocus>
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
                        @if ($record->checkAction('approval', $perms))
                            @include('layouts.forms.btnBack')
                            {{-- btnTrxAset --}}
                            @if(auth()->user()->hasRole('Sub Bagian Program Perencanaan'))
                                @include('layouts.forms.btnTrxAset')
                            @else
                                @include('layouts.forms.btnDropdownApproval')
                            @endif
                            @include('layouts.forms.modalReject')

                            {{-- @include('layouts.forms.btnBack')
                            @include('layouts.forms.btnDropdownApproval')
                            @include('layouts.forms.modalReject') --}}
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

    @if (request()->route()->getName() == $routes.'.detail' )
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

            <div class="col-md-6" style="margin-top:20px!important;">
                <div class="card card-custom" style="height:100%;">
                    <div class="card-header">
                        <h4 class="card-title">Informasi</h4>
                    </div>

                    <div class="card-body" style="padding: 10px 1.75rem 10px 1.75rem;">
                        <div class="d-flex align-items-center justify-content-between flex-lg-wrap flex-xl-nowrap p-4">
                            <div class="d-flex flex-column mr-5">
                                <p class="text-dark-50">
                                    Sebelum submit pastikan data detail sperpat diisi dengan lengkap.
                                </p>
                            </div>

                            <div class="ml-lg-0 ml-xxl-6 ml-6 flex-shrink-0">
                                @php
                                    $module = 'usulan_pembelian-sperpat';
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
                                    @php

                                    // $flags = \App\Models\Perbaikan\TransPerbaikanDisposisi::where('perbaikan_id', $record->codes->id)
                                    //     ->where('vendor_id', $record->vendor_id)
                                    //     ->where('repair_type', $record->repair_type)
                                    //     ->first();

                                    // if ($flags) {
                                    //     $flag = \App\Models\Perbaikan\UsulanSperpat::where('trans_perbaikan_id', $flags->id)->count();
                                    // } else {
                                    //     // Handle case when $flags is null
                                    //     $flag = 0;
                                    // }
                                    @endphp
                                    <div id="submitBtn">
                                        @include('layouts.forms.btnDropdownSubmit')
                                    </div>
                                    {{-- @if($flag > 0)
                                        @include('layouts.forms.btnDropdownSubmit')
                                    @endif --}}
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

@push('scripts')

{{-- <script>
var resp1;
var me = document.getElementById('trans_perbaikan_id');
var vendors = document.getElementById('vendor_id');
var tipes= document.getElementById('rep_type');

$.ajax({
    type: 'POST',
    url: '/ajax/cekSperpat',
    data: {
        _token: BaseUtil.getToken(),
        id: me.value,
        vendor : vendors.value,
        tip : tipes.value,
    },
    success: function(resp) {
        console.log(resp);
        resp1 = resp
        // flagValue = resp;
    },
    error: function(resp) {
        console.log(resp)
        console.log('error')
    },
});

if(resp1 > 0) {
    document.getElementById('submitBtn').style.display = 'block';
} else{
    document.getElementById('submitBtn').style.display = 'none';
}

</script> --}}


@endpush

