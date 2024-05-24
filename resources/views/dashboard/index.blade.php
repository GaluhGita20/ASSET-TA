{{-- {{ dd(session('remember_username')) }} --}}
{{-- @extends('layouts.page')

@section('page')
 
@endsection --}}

@extends('layouts.page')

@section('page')
    <div class="row">
        @include($views . '._card-progress')
        <div class="col-12">

            <div class="row card-progress-wrapper">
                
                {{-- @foreach ($cards as $card) --}}
                    <div class="col-xl-6 col-md-6 col-sm-12">
                        <div class="card card-custom gutter-b card-stretch wave wave-primary"
                        data-name="jumlah">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center py-1">
                                    <div class="symbol symbol-40 symbol-light-primary mr-5">
                                        <span class="symbol-label shadow">
                                            <i class="fa fa-box align-self-center text-success font-size-h5"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                        <div class="text-dark font-weight-bolder font-size-h5">
                                            {{ __('Total Aset') }}
                                        </div>
                                        <div class="text-muted font-weight-bold font-size-lg">
                                            <div class="d-flex justify-content-between">
                                                {{-- <span class="text-nowrap">Active/Not Active</span> --}}
                                                <span class="text-nowrap">
                                                    <h4 class="actived">{{number_format($jumlah, 0, ',', ',')}}</h4>
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
        
                    <div class="col-xl-6 col-md-6 col-sm-12">
                        <div class="card card-custom gutter-b card-stretch wave wave-success"
                        data-name="value">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center py-1">
                                    <div class="symbol symbol-40 symbol-light-success mr-5">
                                        <span class="symbol-label shadow">
                                            <i class="fa fa-money-bill align-self-center text-primary font-size-h5"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                        <div class="text-dark font-weight-bolder font-size-h5">
                                            {{ __('Total Nilai Aset') }}
                                        </div>
                                        <div class="text-muted font-weight-bold font-size-lg">
                                            <div class="d-flex justify-content-between">
                                                {{-- <span class="text-nowrap">Active/Not Active</span> --}}
                                                <span class="text-nowrap">
                                                    <h4 class="actived">Rp. {{number_format($value, 0, ',', ',')}}</h4>
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
                {{-- @endforeach --}}
            </div>
        </div>
    </div>
    <div class="row">
        {{-- @include($views . '._chart-aset_b') --}}
        {{-- @include($views . '._chart-aset_b') --}}
        @include($views . '._chart-stage')
    </div>
    <div class="row">
        {{-- @include($views . '._chart-followup') --}}
        {{-- @include($views . '._chart-finding') --}}
        {{-- @include($views . '._chart-aset_c')
        @include($views . '._chart-aset_d') --}}
        {{-- @include($views . '._chart-termin') --}}
    </div>
    <div class="row">
        {{-- @include($views . '._chart-aset_e')
        @include($views . '._chart-aset_f') --}}
        {{-- @include($views . '._chart-termin') --}}
    </div>
   
@endsection

