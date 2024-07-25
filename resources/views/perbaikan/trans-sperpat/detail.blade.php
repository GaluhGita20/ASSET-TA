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
                    <h3 class="card-title">@yield('card-title', 'Transaksi Sperpat ', $title)</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>

                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                    <div class="row">
                        <input type="hidden" name="perbaikans_id" value="{{$record->perbaikan_id}}">
                        <input type="hidden" name="vendors_id" value="{{$record->vendor_id}}">
                        <input type="hidden" name="repair_type" value="{{$record->repair_type}}">
                        
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('No Surat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="perbaikan_id" id="perbaikan_id" class="form-control base-plugin--select2-ajax trans_perbaikan_id"
                                        data-url="{{ route('ajax.selectPerbaikan') }}"
                                        data-placeholder="{{ __('Pilih Usulan Perbaikan') }}" value="{{$record->perbaikan_id}}" disabled>
                                        @if ($record->codes)
                                        <option value="{{ $record->codes->id }}" selected>
                                            {{ $record->codes->code }}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tipe Perbaikan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <select class="form-control" name="repair_type" id="rep_type" data-placeholder="Tipe Perbaikan" disabled>
                                        <option disabled value="">Jenis Perbaikan</option>
                                        <option value="sperpat" {{ $record->repair_type == 'sperpat' ? 'selected' : '' }}>Pembelian Sperpat</option>
                                        <option value="vendor" {{ $record->repair_type == 'vendor' ? 'selected' : '' }}>Jasa Vendor</option>
                                        <option value="sperpat dan vendor" {{ $record->repair_type == 'sperpat dan vendor' ? 'selected' : '' }}>Beli Sperpat dan Jasa Vendor</option>
                                    </select>                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Sumber Pendanaan') }}</label>
                                <div class="col-sm-10 col-form-label">
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

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Mulai Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->spk_start_date)
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_start_date" value="{{$record->spk_start_date->format('d/m/Y')}}">                         
                                    @else
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_start_date">    
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Selesai Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->spk_end_date)
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_end_date" min="{{now()}}" value="{{$record->spk_end_date->format('d/m/Y')}}">                         
                                    @else
                                        <input type="text" class="form-control base-plugin--datepicker" name="spk_end_date" min="{{now()}}">  
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Nomor Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->no_spk)
                                        <input type="text" class="form-control" name="no_spk" value="{{$record->no_spk}}"> 
                                        {{-- <input type="text" class="form-control base-plugin--datepicker" name="spk_end_date" min="{{now()}}" value="{{$record->spk_end_date->format('d/m/Y')}}">                          --}}
                                    @else
                                        <input type="text" class="form-control" name="no_spk"> 
                                        {{-- <input type="text" class="form-control base-plugin--datepicker" name="spk_end_date" min="{{now()}}">   --}}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Biaya Total Sementara') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">  
                                    @if($record->repair_type =='sperpat' || $record->repair_type =='sperpat dan vendor')
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
                                            {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}" disabled>        --}}
                                            {{-- <input type="hidden" min=0 id="ts_cost" name="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}">                  
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
                                            {{-- <input type="hidden" min=0 id="ts_cost" name="ts_cost" value="-">  
                                        @endif
                                    @else
                                        <div class="input-group">
                                            <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Pengiriman') }}" value="{{number_format($record->total_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($record->total_cost, 0, ',', ',')}}" disabled> --}}
                                        {{-- <input type="hidden" min=0 id="ts_cost" name="ts_cost" id="ts_cost" value="{{ number_format($record->total_cost, 0, ',', ',')}}">  
                                    @endif                         
                                </div>
                            </div>
                        </div> --}}

                        @if($record->repair_type == 'sperpat')
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Total Sperpat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                    <div class="col-sm-10 col-form-label">  
                                        <div class="input-group">
                                            <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sperpat') }}" value="{{number_format($ts_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}" disabled>        --}}
                                        <input type="hidden" min=0 id="ts_cost" name="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}">  
                                    </div>
                                </div>
                            </div>

                        @elseif($record->repair_type == 'vendor')
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Total Jasa Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                    <div class="col-sm-10 col-form-label">  
                                        <div class="input-group">
                                            <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sperpat') }}" value="{{number_format($record->total_cost_vendor, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}" disabled>        --}}
                                        <input type="hidden" min=0 id="ts_cost_vendor" name="ts_cost_vendor" value="{{ number_format($record->total_cost_vendor, 0, ',', ',')}}">  
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Total Jasa Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                    <div class="col-sm-10 col-form-label">  
                                        <div class="input-group">
                                            <input type="text" min=0 id="ts_cost_vendor" name="ts_cost_vendor" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sperpat') }}" value="{{number_format($record->total_cost_vendor, 0, ',', ',')}}" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}" disabled>        --}}
                                        <input type="hidden" min=0 id="ts_cost_vendor" name="ts_cost_vendor" value="{{ number_format($record->total_cost_vendor, 0, ',', ',')}}">  
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">{{ __('Biaya Total Sperpat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                    <div class="col-sm-10 col-form-label">  
                                        <div class="input-group">
                                            <input type="text" min=0 id="ts_cost" name="ts_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Biaya Sperpat') }}" value="{{number_format($ts_cost, 0, ',', ',')}}" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div> 
                                        {{-- <input type="text" class="form-control" name="ts_cost" id="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}" disabled>        --}}
                                        <input type="hidden" min=0 id="ts_cost" name="ts_cost" value="{{ number_format($ts_cost, 0, ',', ',')}}">  
                                    </div>
                                </div>
                            </div>

                        @endif

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Biaya Pengiriman') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label"> 
                                    {{-- <div class="col-sm-8 col-form-label">   --}}
                                        @if($record->shiping_cost != null)
                                            <div class="input-group">
                                                <input type="text" min=0 id="shiping_cost" name="shiping_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                    placeholder="{{ __('Biaya Pengiriman') }}" value="{{number_format($record->shiping_cost, 0, ',', ',')}}" oninput="updateTotal()">
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
                                                    placeholder="{{ __('Biaya Pengiriman') }}" value="-" oninput="updateTotal()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >
                                                        Rupiah
                                                    </span>
                                                </div>
                                            </div> 
                                            {{-- <input type="text" class="form-control" name="shiping_cost" id="shiping_cost" value="-" oninput="updateTotal()" disabled>  --}}
                                        @endif
                                    {{-- </div> --}}
                                    {{-- @if($record->shiping_cost)
                                        <input type="text" class="form-control" name="shiping_cost" id="shiping_cost" oninput="updateTotal()" value="{{number_format($record->shiping_cost, 0, ',', ',')}}">                         
                                    @else
                                        <input type="text" class="form-control" name="shiping_cost" id="shiping_cost" oninput="updateTotal()" >  
                                    @endif  --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Biaya Pajak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                {{-- <div class="col-sm-10 col-form-label"> --}}
                                    <div class="col-sm-10 col-form-label">
                                        @if($record->tax_cost != null)
                                            <div class="input-group">
                                                <input type="text" min=0 id="tax_cost" name="tax_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                    placeholder="{{ __('Biaya Pajak') }}" value="{{number_format($record->tax_cost, 0, ',', ',')}}" oninput="updateTotal()">
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
                                                    placeholder="{{ __('Biaya Pajak') }}" value="-" oninput="updateTotal()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" >
                                                        Rupiah
                                                    </span>
                                                </div>
                                            </div> 
                                            {{-- <input type="text" class="form-control" name="tax_cost" id="tax_cost" value="-" oninput="updateTotal()" disabled>  --}}
                                        @endif
                                    </div>
                                    {{-- @if($record->tax_cost)
                                        <input type="text" class="form-control" name="tax_cost" id="tax_cost" oninput="updateTotal()" value="{{number_format($record->tax_cost, 0, ',', ',')}}">                         
                                    @else
                                        <input type="text" class="form-control" name="tax_cost" id="tax_cost" oninput="updateTotal()">  
                                    @endif                        --}}
                                {{-- </div> --}}
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Total Biaya') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                
                                <div class="col-sm-10 col-form-label">

                                    @if($record->total_cost != null)
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Total Biaya') }}" value="{{number_format($record->total_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                            <input type="hidden" min=0 id="total_cost" name="total_cost" oninput="updateTotal()">
                                        </div>
                                        {{-- <input type="text" class="form-control" name="total_cost" id="total_cost" value="{{number_format($record->total_cost, 0, ',', ',')}}" oninput="updateTotal()" disabled>                          --}}
                                    @else
                                        <div class="input-group">
                                            <input type="text" min=0 id="total_cost" name="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                                placeholder="{{ __('Total Biaya') }}" oninput="updateTotal()" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text" >
                                                    Rupiah
                                                </span>
                                            </div>
                                        </div>
                                        <input type="hidden" min=0 id="total_cost" name="total_cost" oninput="updateTotal()">
                                        {{-- <input type="text" class="form-control" name="total_cost" id="total_cost" value="0" oninput="updateTotal()" disabled >   --}}
                                    @endif                       
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Nomor Faktur/Invoice ') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->faktur_code)
                                        <input type="text" class="form-control" name="faktur_code" value="{{$record->faktur_code}}">                         
                                    @else
                                        <input type="text" class="form-control" name="faktur_code">  
                                    @endif                       
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Nomor SPM') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->spm_code)
                                        <input type="text" class="form-control" name="spm_code" value="{{$record->spm_code}}">                         
                                    @else
                                        <input type="text" class="form-control" name="spm_code">  
                                    @endif                       
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Penerimaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    @if($record->receipt_date)
                                        <input type="text" class="form-control base-plugin--datepicker" name="receipt_date" min="{{now()}}" value="{{$record->receipt_date->format('d/m/Y')}}">                         
                                    @else
                                        <input type="text" class="form-control base-plugin--datepicker" name="receipt_date" min="{{now()}}">  
                                    @endif                       
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Upload Bukti Faktur/Invoice , SPM, dan Dokumen Kontrak') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    @if($record->repair_type == 'sperpat' || $record->repair_type == 'sperpat dan vendor' )
        <div class="row mb-3">
            <div class="col-sm-12">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Detail Sperpat</h3>
                    </div>
                    <div class="card-body p-8">
                        @include('perbaikan.trans-sperpat.detail.index')
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
                                            $module = 'trans-sperpat';
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
                                    Sebelum submit pastikan data detail pengajuan diisi dengan lengkap.
                                </p>
                            </div>

                            <div class="ml-lg-0 ml-xxl-6 ml-6 flex-shrink-0">
                                @php
                                    $module = 'trans-sperpat';
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

                                    <div id="submitBtn">
                                        @include('layouts.forms.btnDropdownSubmit')
                                    </div>
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

<script>
    // function updateTotal() {
    //     var ts_cost = document.getElementById('ts_cost').value;
    //     var tax = document.getElementById('tax_cost').value;
    //     var shiping = document.getElementById('shiping_cost').value;

    //     ts_cost= ts_cost.replace(/[^0-9]/g, '');
    //     tax= tax.replace(/[^0-9]/g, '');
    //     shiping= shiping.replace(/[^0-9]/g, '');

    //     ts_cost = parseInt(ts_cost);
    //     tax = parseInt(tax);
    //     shiping = parseInt(shiping);
        
    //     if(ts_cost > 0){
    //         var total = parseInt(ts_cost) + tax + shiping;
    //         document.getElementById('total_cost').value = parseInt(total);
    //     }



    // }


    function updateTotal() {

        var element = document.getElementById('ts_cost_vendor');
        if (element) {
            var ts_cost_v = element.value;
            ts_cost_v= ts_cost_v.replace(/[^0-9]/g, '');
            ts_cost_v = parseInt(ts_cost_v);
        } else{
            ts_cost_v = 0;
        }

        console.log(ts_cost_v);

        var element2 = document.getElementById('ts_cost');
        if (element2) {
            var ts_cost = element2.value;
            ts_cost= ts_cost.replace(/[^0-9]/g, '');
            ts_cost = parseInt(ts_cost);
        } else{
            ts_cost = 0;
        }

        var element3 = document.getElementById('rep_type');



        // var ts_cost_v = document.getElementById('ts_cost_vendor').value;
        // var ts_cost = document.getElementById('ts_cost').value;
        var tax = document.getElementById('tax_cost').value;
        var shiping = document.getElementById('shiping_cost').value;

        // ts_cost_v= ts_cost_v.replace(/[^0-9]/g, '');
        // ts_cost= ts_cost.replace(/[^0-9]/g, '');
        tax= tax.replace(/[^0-9]/g, '');
        shiping= shiping.replace(/[^0-9]/g, '');

        // ts_cost = parseInt(ts_cost);
        // ts_cost_v = parseInt(ts_cost_v);
        tax = parseInt(tax);
        shiping = parseInt(shiping);


        console.log(ts_cost, ts_cost_v,element3.value)

        if(ts_cost >= 0 && ts_cost_v >= 0 && element3.value == 'sperpat dan vendor'){
            var total = parseInt(ts_cost) + tax + shiping + ts_cost_v;
            document.getElementById('total_cost').value = parseInt(total);
        }else if(ts_cost > 0 && element3.value == 'sperpat'){
            var total = parseInt(ts_cost) + tax + shiping;
            document.getElementById('total_cost').value = parseInt(total);
        }else if(ts_cost_v > 0 && element3.value == 'vendor'){
            var total = parseInt(ts_cost_v) + tax + shiping;
            document.getElementById('total_cost').value = parseInt(total);
        }

    }
</script>
@endpush

