@extends('layouts.lists')


@section('filters')

    <div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Pemeliaharaan') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax org" name="departemen_id" data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                data-placeholder="{{ __('Unit Kerja') }}" data-post="departemen_id">
            </select>
        </div>

        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
            <select class="form-control base-plugin--select2-ajax filter-control yearSelect"
                name="maintenance_date_year" data-post="maintenance_date_year"
                data-placeholder="{{ __('Periode Pemeliharaan') }}">
                <option value="" selected>{{ __('Periode Pemeliharaan') }}</option>
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
        
        <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
            {{-- <input type='text' class="form-control filter-control  base-plugin--datepicker" name="maintenance_date_month" 
                placeholder="{{ __('Bulan Pemeliharaan') }}" data-post="maintenance_date_month"> --}}
                <select class="form-control base-plugin--select2-ajax filter-control month"
				data-post="maintenance_date_month"
                name="maintenance_date_month">
				<option value="" selected>{{ __('Pilih Bulan Pemeliharaan') }}</option>
				<option value="01">Januari</option>
				<option value="02">Februari</option>
                <option value="03">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
				<option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">September</option>
                <option value="10">Oktober</option>
				<option value="11">November</option>
                <option value="12">Desember</option>
			</select>
        </div>

    </div>

    <div class="card-body p-8">
        <div class="col-12">
            <div class="row card-progress-wrapper">
                <div class="col-xl-6 col-md-4 col-sm-12">
                    <div class="card card-custom gutter-b card-stretch wave wave-primary"
                    data-name="Perbaikan">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center py-1">
                                <div class="symbol symbol-40 symbol-light-primary mr-5">
                                    <span class="symbol-label shadow d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                                        <i class="fa fa-hammer text-primary font-size-h5"></i>
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <div class="text-dark font-weight-bolder font-size-h6" style="text-align: center">
                                        Jumlah Pemeliharaan Dilakukan
                                    </div>
                                    <div class="text-muted font-weight-bold font-size-lg" style="margin-top:3px;">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span class="text-nowrap">
                                                <h5 class="jums" id="jums">{{number_format($jumlah, 0, ',', ',')}}</h5>
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
                                        <i class="fa fa-box text-success font-size-h5"></i>
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <div class="text-dark font-weight-bolder font-size-h6" style="text-align: center">
                                        Jumlah Aset Dipelihara
                                    </div>
                                    <div class="text-muted font-weight-bold font-size-lg" style="margin-top:3px;">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span class="text-nowrap">
                                                <h5 class="biaya" id="biaya">{{number_format($value, 0, ',', ',')}}</h5>
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
            var org = null;
            var year = null;
            var mon = null;

            $('.content-page').on('change', 'select.yearSelect', function (e) {
                year = $(this);
                // console.log(org.val());
                $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPemeliharaan',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: year.val(),
                            val2: null,
                            val3:null,
                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.value;
                            
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
            });

            $('.content-page').on('change', 'select.org', function (e) {
                org = $(this);
                // console.log(ruang.val());

                $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPemeliharaan',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: new Date().getFullYear(),
                            val2: org.val(),
                            val3: null,

                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.value;
                            
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
            });

            $('.content-page').on('change', 'select.month', function (e) {
                mon = $(this);
                // console.log(ruang.val());

                
            });


            $('.content-page').on('change', 'select.org, select.yearSelect', function (e) {
                // console.log(org.val, ruang.val)
                if (org != null && org.val() != null && year != null && year.val() != null) {
                        $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPemeliharaan',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: year.val(),
                            val2: org.val(),
                            val3: null,
                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.value;
                            
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
                }
            });


            $('.content-page').on('change', 'select.month, select.yearSelect', function (e) {
                // console.log(org.val, ruang.val)
                if (mon != null && mon.val() != null && year != null && year.val() != null) {
                        $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPemeliharaan',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: year.val(),
                            val2: null,
                            val3: mon.val(),
                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.value;
                            
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
                }
            });

            $('.content-page').on('change', 'select.month, select.yearSelect, select.org', function (e) {
                // console.log(org.val, ruang.val)
                if (mon != null && mon.val() != null && year != null && year.val() != null && org != null && org.val() != null) {
                        $.ajax({
                        type: 'POST',
                        url: '/ajax/getLapPemeliharaan',
                        data: {
                            _token: BaseUtil.getToken(),
                            val1: year.val(),
                            val2: org.val(),
                            val3: mon.val(),
                        },
                        success: function(resp) {
                            var jumlah = resp.jumlah;
                            var biaya = resp.value;
                            
                            
                            $('.jums').text(jumlah);
                            $('.biaya').text(biaya);
        
                            console.log(resp);
                        },
                        error: function(resp) {
                            console.log(resp)
                            console.log('error')
                        },
                    });
                }
            });


        });
    </script>
@endpush
