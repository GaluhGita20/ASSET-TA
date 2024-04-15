@extends('layouts.pageSubmit')
{{-- @section('action', route($routes . '.update', $record->id)) --}}

@section('card-body')
@section('page-content')
    @method('PATCH')
    @csrf
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Aset {{$record->usulans->asetd->name}}</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>
                <div class="card-body py-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                            </div>
                      
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="nama_aset" placeholder="{{ __('Nama Aset') }}" value="{{ $record->usulans->asetd->name }}" readonly>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="spesifikasi" value="{{ $record->usulans->desc_spesification }}" readonly>{{ $record->usulans->desc_spesification }}"</textarea>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Register') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="no_regsiter" value="{{ str_pad($record->no_register, 3, '0', STR_PAD_LEFT) }}" readonly>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Pembukuan') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input class="form-control " name="book_date" value="{{ now()->format('Y/m/d') }}" readonly>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Masa Manfaat') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="number" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Masa Manfaat') }}" name="useful" value="{{ $record->useful }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                Tahun
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nilai Buku') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" name="unit_cost" value="{{ $record->book_value }}" readonly>
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
                                    <label class="col-form-label">{{ __('Nilai Residu') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" placeholder="{{ __('Nilai Residu') }}" name="residual_value" value="{{ $record->residual_value }}" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                Rupiah
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
                    <h3 class="card-title">Laporan Penyusutan Aset {{$record->usulans->asetd->name}}</h3>
                </div>
                <div class="card-body p-8">
                    <div class="table-responsive">
                        @if (isset($tableStruct['datatable_1']))
                            <table id="datatable_1" class="table table-bordered is-datatable" style="width: 100%;"
                                data-url="{{ $tableStruct['url'] }}" data-paging="{{ $paging ?? true }}" data-info="{{ $info ?? true }}">
                                    <thead>
                                        <tr>
                                            @foreach ($tableStruct['datatable_1'] as $struct)
                                                <th class="text-center v-middle" data-columns-name="{{ $struct['name'] ?? '' }}"
                                                    data-columns-data="{{ $struct['data'] ?? '' }}"
                                                    data-columns-label="{{ $struct['label'] ?? '' }}"
                                                    data-columns-sortable="{{ $struct['sortable'] === true ? 'true' : 'false' }}"
                                                    data-columns-width="{{ $struct['width'] ?? '' }}"
                                                    data-columns-class-name="{{ $struct['className'] ?? '' }}"
                                                    style="{{ isset($struct['width']) ? 'width: ' . $struct['width'] . '; ' : '-' }}">
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
            </div>
        </div>
    </div>

    @show
@endsection