@extends('layouts.lists')


@section('filters')
<div class="row">
    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <input type="text" class="form-control filter-control" data-post="no_spk" placeholder="{{ __('No SPK') }}">
    </div>

    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <input type="text" class="form-control filter-control" data-post="trans_name" placeholder="{{ __('Nama Transaksi') }}">
    </div>

    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <select class="form-control filter-control base-plugin--select2-ajax" name="vendor_id" data-url="{{ route('ajax.selectVendor', 'all') }}"
            data-placeholder="{{ __('Nama Vendor') }}" data-post="vendor_id">
        </select>
    </div>

    <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
        <input type='text' class="form-control filter-control  base-plugin--datepicker spk_start_date" name="spk_start_date" 
            placeholder="{{ __('Tanggal Mulai Kontrak') }}" data-post="spk_start_date">
    </div>
    <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
        <input type='text' class="form-control filter-control  base-plugin--datepicker spk_end_date" name="spk_end_date" 
            placeholder="{{ __('Tanggal Selesai Kontrak') }}" data-post="spk_end_date">
    </div>

    <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
        <select id="yearSelect" class="form-control base-plugin--select2-ajax filter-control yearSelect"
            data-post="years"
            data-placeholder="{{ __('Periode Usulan') }}">
            <option value="" selected>{{ __('Periode Usulan') }}</option>
            @php
                $startYear = 2020;
                $currentYear = date('Y');
                $endYear = $currentYear + 5;
            @endphp
            @for ($year = $startYear; $year <= $endYear; $year++)
                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
        </select>
        {{-- <input type="text" class="form-control filter-control" data-post="procurement_year" placeholder="{{ __('Periode Perencanaan') }}"> --}}
    </div>

</div>
<div class="card-body p-8">
    <div class="col-12">
        <div class="row card-progress-wrapper">
            <div class="col-xl-6 col-md-6 col-sm-12">
                <div class="card card-custom gutter-b card-stretch wave wave-primary"
                data-name="Perbaikan">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center py-1">
                            <div class="symbol symbol-40 symbol-light-warning mr-5">
                                <span class="symbol-label shadow d-flex justify-content-center align-items-center" style="width: 100%; height: 100%; justify-content:center; align-items:center;">
                                    <i class="fa fa-truck text-primary font-size-h5"></i>
                                </span>
                            </div>
                            
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                <div class="text-dark font-weight-bolder font-size-h6" style="width: 100%; height: 100%;">
                                    Jumlah Pembelian Aset
                                </div>
                                <div class="text-muted font-weight-bold font-size-lg" style="margin-top:3px;">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <span class="text-nowrap">
                                            <h5 class="jums" id="jums" >0</h5>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column w-100 mt-5">
                                <div class="text-dark mr-2 font-size-lg font-weight-bolder pb-3">
                                    <div class="d-flex justify-content-between">
                                        {{-- <span class="percent-text">0%</span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-4 col-sm-12">
                <div class="card card-custom gutter-b card-stretch wave wave-success"
                data-name="Perbaikan">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center py-1">
                            <div class="symbol symbol-40 symbol-light-warning mr-5">
                                <span class="symbol-label shadow d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                                    <i class="fa fa-money-bill text-success font-size-h5"></i>
                                </span>
                            </div>
                            
                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                <div class="text-dark font-weight-bolder font-size-h6" style="text-align: center">
                                    Total Biaya Pembelian Aset
                                </div>
                                <div class="text-muted font-weight-bold font-size-lg" style="margin-top:3px;">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <span class="text-nowrap">
                                            <h5 class="biaya" id="biaya">0</h5>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column w-100 mt-5">
                                <div class="text-dark mr-2 font-size-lg font-weight-bolder pb-3">
                                    <div class="d-flex justify-content-between">
                                        {{-- <span class="percent-text">0%</span> --}}
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



@endsection

@push('scripts')
<script>
   	$(function () {
        const yearSelect = document.getElementById('yearSelect');
        var me = null;

        $('.content-page').on('change', 'select.yearSelect', function (e) {
            me = $(this);
            

            $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPengadaan1',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: me.val(),
                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.biaya;
                            // var not_realisasi = resp.not_realisasi;
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya.toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }));
                            // $('.notReal').text(not_realisasi);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
        });


        if(me == null){
            $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPengadaan1',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: new Date().getFullYear(),
                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.biaya;
                            // var not_realisasi = resp.not_realisasi;
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya.toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }));
                            // $('.notReal').text(not_realisasi);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
        }
    });
    </script>
@endpush
