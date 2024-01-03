{{-- @extends('layouts.page') --}}
@extends('layouts.pageSubmit')
@section('action', rut($routes . '.reject', $record->id))

@section('card-body')
@section('page-content')
    {{-- {{ $record = session('record') }} --}}
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">{!!  __('Laporan Pembelian') !!}</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>

                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" value="{{ $record->id }}" name="trans_id">
                            <div class="col-10 parent-group">
                                <input type="hidden" id="dataArray" name="usulan_id" value="{{ json_encode($data) }}">
                            </div>

                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Transaksi') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="trans_name" value="{{ $record->trans_name }}" placeholder="{{ __('Nama Transaksi') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nama Vendor') }}</label>
                                </div>
                                <div class="col-8 parent-group">
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
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    {{-- Carbon::parse($detail->spk_start_date)->format('Y-m-d');; --}}
                                    <input type="text" class="form-control" name="no_spk" value="{{ $record->no_spk }}" placeholder="{{ __('Nomor Kontrak') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Mulai Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control base-plugin--datepicker" name="spk_start_date" value="{{$record->spk_start_date->format('d/m/Y')}}" placeholder="{{ __('Tanggal Mulai Kontrak') }}" data-date-end-date="{{ now()}}" readonly >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Selesai Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control base-plugin--datepicker" name="spk_end_date" value="{{ $record->spk_end_date->format('d/m/Y')}}" placeholder="{{ __('Tanggal Selesai Kontrak') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Lama Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="spk_range_time" name="spk_range_time" value="{{ $record->spk_range_time }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    Hari
                                                </span>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4">
                                    <label class="col-form-label">{{ __('Jenis Pengadaan') }}</label>
                                </div>
                                <div class="col-md-8 parent-group">
                                    <select name="jenis_pengadaan_id" class="form-control base-plugin--select2-ajax jenis_pengadaan_id">
                                        @if ($record->jenis_pengadaan_id)
                                            <option value="{{ $record->pengadaans->id }}" selected disabled>
                                                {{ $record->pengadaans->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah Beli') }}</label>
                                </div>
                                <div class="col-8 parent-group">

                                        <div class="input-group">
                                            <input type="text" class="form-control" id="qty" name="qty" value="{{ $record->qty }}" oninput="updateTotal()"readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    Unit
                                                </span>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Pagu') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" id='budget_limit' name="budget_limit" value="{{ $record->budget_limit }}" readonly oninput="updateTotal()">
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
                                    <label class="col-form-label">{{ __('Harga Unit Barang') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min=0 name="unit_cost" id="unit_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Harga Unit Barang') }}" value="{{ $record->unit_cost }}"  oninput="updateTotal()" readonly>
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
                                    <label class="col-form-label">{{ __('Biaya Pajak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min=0 name="tax_cost" id="tax_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Pajak') }}" value="{{ $record->tax_cost }}"  oninput="updateTotal()" readonly>
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
                                    <label class="col-form-label">{{ __('Biaya Pengiriman') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min=0 name="shiping_cost" id="shiping_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Pengiriman') }}" value="{{ $record->shiping_cost }}"  oninput="updateTotal()" readonly>
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
                                    <label class="col-form-label">{{ __('Total Cost') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" min="0" name="total_cost" id="total_cost" class="form-control base-plugin--inputmask_currency text-right"
                                            placeholder="{{ __('Biaya Total') }}" value="{{ $record->total_cost }}"  readonly oninput="updateTotal()" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        <h3 class="card-title">Laporan Penerimaan</h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Penerimaan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    @if($record->receipt_date == null)
                                    <input class="form-control base-plugin--datepicker" name="receipt_date" value="{{$record->receipt_date}}" placeholder="{{ __('Tanggal Penerimaan') }}" readonly>
                                    @else
                                    <input class="form-control base-plugin--datepicker" name="receipt_date" value="{{$record->receipt_date->format('d/m/Y')}}" placeholder="{{ __('Tanggal Penerimaan') }}" readonly>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kode Faktur Penerimaan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="faktur_code" value="{{ $record->faktur_code }}" placeholder="{{ __('Kode Faktur Penerimaan') }}"  readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Kode SP2D') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="sp2d_code" value="{{ $record->sp2d_code }}" placeholder="{{ __('Kode SP2D') }}" readonly>
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
                                    <input class="form-control base-plugin--datepicker" name="sp2d_date" value="{{ $record->sp2d_date}}" placeholder="{{ __('Tanggal SP2D') }}" data-date-end-date="{{ now()}}" readonly>
                                    @else

                                    <input class="form-control base-plugin--datepicker" name="sp2d_date" value="{{ $record->sp2d_date->format('d/m/Y')}}" placeholder="{{ __('Tanggal SP2D') }}" data-date-end-date="{{ now()}}" readonly >
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Lokasi Penerimaan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input class="form-control" name="location_receipt" value="{{ $record->location_receipt }}" placeholder="{{ __('Lokasi Penerimaan') }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Lokasi Penerimaan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control" name="location_receipt" value="{{ $record->location_receipt }}" placeholder="{{ __('Lokasi Penerimaan') }}" readonly>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Hasil Uji Fungsi Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="asset_test_results" value="{{ $record->asset_test_results }}" placeholder="{{ __('Hasil Uji Fungs Aset') }}" readonly>{{ $record->asset_test_results }}</textarea>
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

    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Lampiran List Usulan Pembelian') }}</h3>
                </div>
                <div class="card-body p-8">
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <div class="card card-custom">
                                <div class="card-body p-8">
                                    <div class="table-responsive">
                                        @if (isset($tableStruct['datatable_1']))
                                            <table id="datatable_1" class="table-bordered is-datatable table" style="width: 100%;"
                                                data-url="{{ $tableStruct['url'] }}" data-paging="{{ $paging ?? true }}" data-info="{{ $info ?? true }}">
                                                <thead>
                                                    <tr>
                                                        @foreach ($tableStruct['datatable_1'] as $struct)
                                                            <th class="v-middle text-center" data-columns-name="{{ $struct['name'] ?? '' }}"
                                                                data-columns-data="{{ $struct['data'] ?? '' }}"
                                                                data-columns-label="{{ $struct['label'] ?? '' }}"
                                                                data-columns-sortable="{{ $struct['sortable'] === true ? 'true' : 'false' }}"
                                                                data-columns-width="{{ $struct['width'] ?? '' }}"
                                                                data-columns-class-name="{{ $struct['className'] ?? '' }}"
                                                                style="{{ isset($struct['width']) ? 'width: ' . $struct['width'] . '; ' : '' }}">
                                                                {{ $struct['label'] }}
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
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
             
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script>
    function updateTotal() {
        var quantity = document.getElementById('qty').value;
        var unit_cost = document.getElementById('unit_cost').value;
        var tax = document.getElementById('tax_cost').value;
        var shiping = document.getElementById('shiping_cost').value;
        var pagu = document.getElementById('budget_limit').value;

        quantity= quantity.replace(/[^0-9]/g, '');
        unit_cost= unit_cost.replace(/[^0-9]/g, '');
        tax= tax.replace(/[^0-9]/g, '');
        shiping= shiping.replace(/[^0-9]/g, '');
        pagu= pagu.replace(/[^0-9]/g, '');

        quantity = parseInt(quantity);
        unit_cost = parseInt(unit_cost);
        tax = parseInt(tax);
        shiping = parseInt(shiping);
        pagu = parseInt(pagu);
        
        if(quantity > 0 && unit_cost > 0){
            console.log(quantity);
            var total = parseInt(quantity) * parseInt(unit_cost) + tax + shiping;
            if(total > pagu){
                alert("Nilai total melebihi batas anggaran!");
                document.getElementById('total_cost').value = 0;
            }else{
                document.getElementById('total_cost').value = parseInt(total);
            }
            // console.log(total)
        }
            
    }
</script>

<script>
    $(document).ready(function () {
        $('#rejectButton').on('click', function () {
            var id = 21;
            var formAction = '{{ route($routes.'.reject', ':id') }}';

            // Replace ':id' in the form action with the actual ID
            formAction = formAction.replace(':id', id);

            // Set the updated form action
            $('#modalReject form').attr('action', formAction);

            // Set the ID in the modal form
            $('#modalReject input[name="id"]').val(id);
        });
    });
</script>


@endpush
