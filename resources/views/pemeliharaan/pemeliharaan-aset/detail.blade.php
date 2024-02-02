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
                    <h3 class="card-title">@yield('card-title', $title)</h3>
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
                                <label class="col-sm-2 col-form-label">{{ __('No Surat Pemeliharaan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input name="text" class="form-control"
                                    placeholder="{{ __('Nomor Surat Pemeliharaan') }}"  value="{{ $record->code }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Unit Kerja') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <select class="form-control" name="departemen_id" disabled>
                                        @if(!empty($record->departemen_id))
                                            <option value="{{ $record->deps->id}}" selected> {{ $record->deps->name }} </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Pemeliharaan') }}</label>
                                <div class="col-sm-10 col-form-label">
                                    <input name="dates" class="form-control base-plugin--datepicker"
                                    placeholder="{{ __('Tanggal Pemeliharaan') }}"  value="{{ $record->dates->format('d/m/Y') }}" max="{{ now()->addMonths(1) }}" disabled>
                                </div>
                            </div>
                        </div>    
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 2 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pemeliharaan Aset</h3>
                </div>
                <div class="card-body p-8">
                    @include('pemeliharaan.pemeliharaan-aset.detail.index')
                </div>
            </div>
        </div>
    </div>

@show
@endsection
