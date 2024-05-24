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
                                <label class="col-sm-4 col-form-label">{{ __('Tipe Perbaikan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <select class="form-control" name="repair_type" id="rep_type" data-placeholder="Tipe Perbaikan" disabled>
                                        <option disabled value="">Jenis Perbaikan</option>
                                        <option value="sperpat" {{ $record->repair_type == 'sperpat' ? 'selected' : '' }}>Pembelian Sperpat</option>
                                        <option value="vendor" {{ $record->repair_type == 'vendor' ? 'selected' : '' }}>Sewa Vendor</option>
                                    </select>                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Sumber Pendanaan') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <select name="source_fund_id" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ rut('ajax.selectSSBiaya', [
                                            'search'=>'all'
                                        ]) }}" autofocus style="border: 1px solid #007bff;" data-placeholder="{{ __('Pilih Salah Satu Sumber Pendanaan') }}" disabled>
                                        <option value="" selected>{{ __('Pilih Salah Satu Sumber Pendanaan') }}</option>
                                        @if ($record->source_fund_id != null)
                                            <option value="{{ $record->source_fund_id }}" selected>{{ $record->danad->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Vendor') }}</label>
                                <div class="col-sm-8 col-form-label">
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

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Mulai Kontrak') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if($record->spk_start_date != null)
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_start_date" value="{{$record->spk_start_date->format('d/m/Y')}}" disabled>                         
                                    @else
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_start_date" value="-" disabled>   
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Selesai Kontrak') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if($record->spk_end_date != null)
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_end_date" value="{{$record->spk_end_date->format('d/m/Y')}}" disabled>                         
                                    @else
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_end_date" value="-"  disabled>   
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Kontrak') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if($record->no_spk != null)
                                        <input type="text" class="form-control" name="no_spk" value="{{$record->no_spk}}" disabled>                         
                                    @else
                                        <input type="text" class="form-control" name="no_spk" value="-" disabled> 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Biaya Total Sementara') }}</label>
                                <div class="col-sm-8 col-form-label">  
                                    @if($record->repair_type =='sperpat')
                                        @if($ts_cost != null)
                                            <div class="input-group">
                                                <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                    placeholder="{{ __('Biaya Pengiriman') }}" value="{{number_format($ts_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >
                                                        Rupiah
                                                    </span>
                                                </div>
                                            </div> 
                                            {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}" disabled>                          --}}
                                        @else
                                            <div class="input-group">
                                                <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                    placeholder="{{ __('Biaya Pengiriman') }}" value="-" oninput="updateTotal()" disabled>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >
                                                        Rupiah
                                                    </span>
                                                </div>
                                            </div> 
                                            {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="-"  disabled>  --}}
                                        @endif
                                    @else
                                        <div class="input-group">
                                            <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Pengiriman') }}" value="-" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($record->total_cost, 0, ',', ',')}}" disabled> --}}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Biaya Pengiriman / Lainya') }}</label>
                                <div class="col-sm-8 col-form-label">  
                                    @if($record->shiping_cost != null)
                                        <div class="input-group">
                                            <input type="text" min=0 id="shiping_cost" name="shiping_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Pengiriman') }}" value="{{number_format($record->shiping_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="shiping_cost" id="shiping_cost" oninput="updateTotal()" value="{{number_format($record->shiping_cost, 0, ',', ',')}}" disabled>                          --}}
                                    @else
                                        <div class="input-group">
                                            <input type="text" min=0 id="shiping_cost" name="shiping_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Pengiriman') }}" value="-" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="shiping_cost" id="shiping_cost" value="-" oninput="updateTotal()" disabled>  --}}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Biaya Pajak') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if($record->tax_cost != null)
                                        <div class="input-group">
                                            <input type="text" min=0 id="tax_cost" name="tax_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Pajak') }}" value="{{number_format($record->tax_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="tax_cost" id="tax_cost" oninput="updateTotal()" value="{{number_format($record->tax_cost, 0, ',', ',')}}" disabled>                          --}}
                                    @else   
                                        <div class="input-group">
                                            <input type="text" min=0 id="tax_cost" name="tax_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Pajak') }}" value="-" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="tax_cost" id="tax_cost" value="-" oninput="updateTotal()" disabled>  --}}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Total Biaya') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if($record->total_cost != null)
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Total Biaya') }}" value="{{number_format($record->total_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div>                        
                                    @else
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Total Biaya') }}" value="-" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Kode Faktur') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    @if($record->faktur_code != null)
                                        <input type="text" class="form-control" name="faktur_code" value="{{$record->faktur_code}}" disabled>                         
                                    @else
                                        <input type="text" class="form-control" name="faktur_code" value="-" disabled> 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Faktur Pembelian') }}</label>
                                <div class="col-10 parent-group">
                                    {{-- <div class="custom-file">
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
                                    </div> --}}

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


                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Penerimaan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->receipt_date)
                                        <input type="text" class="form-control base-plugin--datepicker" name="receipt_date" value="{{$record->receipt_date->format('d/m/Y')}}" disabled>                         
                                    @else
                                        <input type="text" class="form-control base-plugin--datepicker" name="receipt_date" value="-" disabled> 
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($record->sp2d_code != null)
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <div class="col-4 pr-0">
                                        <label class="col-form-label">{{ __('Kode SP2D') }}</label>
                                    </div>
                                    <div class="col-8 parent-group">
                                        @if($record->sp2d_code == null)
                                            <input type="text" class="form-control" name="sp2d_code" placeholder="{{ __('Kode SP2D') }}" readonly>
                                        @else
                                            <input type="text" class="form-control" name="sp2d_code" value="{{ $record->sp2d_code }}" placeholder="{{ __('Kode SP2D') }}" readonly>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <div class="col-4 pr-0">
                                        <label class="col-form-label">{{ __('Tanggal SP2D') }}</label>
                                    </div>
                                    <div class="col-8 parent-group">
                                        @if($record->sp2d_date == null)
                                            <input class="form-control base-plugin--datepicker" name="sp2d_date"  placeholder="{{ __('Tanggal SP2D') }}" data-date-end-date="{{ now()}}" readonly>
                                        @else
                                            <input class="form-control base-plugin--datepicker" name="sp2d_date" value="{{$record->sp2d_date}}" placeholder="{{ __('Tanggal SP2D') }}" data-date-end-date="{{ now()}}" readonly >
                                        @endif
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
                            @if(auth()->user()->hasRole('Keuangan'))
                                @include('layouts.forms.btnTrxAset')
                            @else
                                @include('layouts.forms.btnDropdownApproval2')
                            @endif
                            {{-- @include('layouts.forms.btnDropdownApproval') --}}
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
@show
@endsection

@push('scripts')

<script>
    function updateTotal() {
    var ts_cost = document.getElementById('ts_cost').value;
    var tax = document.getElementById('tax_cost').value;
    var shiping = document.getElementById('shiping_cost').value;

    ts_cost= ts_cost.replace(/[^0-9]/g, '');
    tax= tax.replace(/[^0-9]/g, '');
    shiping= shiping.replace(/[^0-9]/g, '');

    ts_cost = parseInt(ts_cost);
    tax = parseInt(tax);
    shiping = parseInt(shiping);
    
    if(ts_cost > 0){
        var total = parseInt(ts_cost) + tax + shiping;
        document.getElementById('total_cost').value = parseInt(total);
    }

}
</script>
@endpush

