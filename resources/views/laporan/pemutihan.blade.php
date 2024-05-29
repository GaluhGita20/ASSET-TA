@extends('layouts.lists')


@section('filters')
<div class="row">
    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
        <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Pengajuan') }}">
    </div>
    <div class="col-12 col-sm-6 col-xl-3 pb-2">
        <select class="form-control base-plugin--select2-ajax filter-control"
            data-post="kib_id"
            name= "kib_id"
            data-url="{{ route('ajax.selectAsetRS', 'all') }}"
            placeholder="{{ __('Pilih Salah Satu') }}">
            <option value="">{{ __('Pilih Salah Satu') }}</option>
        </select>
    </div>

    <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
        <select class="form-control base-plugin--select2-ajax filter-control yearSelect"
            data-post="submission_date"
            data-placeholder="{{ __('Periode Usulan Perbaikan') }}">
            <option value="" selected>{{ __('Periode Usulan Perbaikan') }}</option>
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
                <div class="col-xl-6 col-md-4 col-sm-12">
                    <div class="card card-custom gutter-b card-stretch wave wave-danger"
                    data-name="Perbaikan">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center py-1">
                                <div class="symbol symbol-40 symbol-light-warning mr-5">
                                    <span class="symbol-label shadow d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                                        <i class="fa fa-rocket text-danger font-size-h5"></i>
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <div class="text-dark font-weight-bolder font-size-h6" style="text-align: center">
                                        Jumlah Aset Diputihkan
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
                                        <i class="fa fa-money-bill text-success font-size-h5"></i>
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <div class="text-dark font-weight-bolder font-size-h6" style="text-align: center">
                                        Total Pendapatan Pemutihan Aset
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

        $('.content-page').on('change', 'select.yearSelect', function (e) {
            year = $(this);
            // console.log(org.val());
			$.ajax({
					type: 'POST',
					url: '/ajax/getLapPemutihan',
					data: {
						_token: BaseUtil.getToken(),
						val1: year.val(),
					},
					success: function(resp) {
						var jumlah = resp.jumlah;
						var biaya = resp.value;
						
						
						$('.jums').text(jumlah);
						$('.biaya').text(biaya.toLocaleString('id-ID', {
							style: 'currency',
							currency: 'IDR'
						}));
	
						console.log(resp);
					},
					error: function(resp) {
						console.log(resp)
						console.log('error')
					},
				});
        });
    });
</script>
@endpush
