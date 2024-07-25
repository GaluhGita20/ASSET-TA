@extends('layouts.pageSubmit')

@section('action', route($routes . '.storeDetailKibC'))

@section('card-body')
@section('page-content')
    @method('POST')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        &nbsp;
                        <h3 class="card-title" style="text-align:center;">{{ __('Inventaris Aset Gedung Bangunan') }}</h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-10 parent-group">
                                <input type="hidden" id="usulanId" name="usulan_id" >
                               {{-- <input type="hidden" id="trans_id" name="trans_id" value="{{ $trans->id }}">  --}}
                               <input type="hidden" id="jumlah_semua" name="jumlah_semua" value="1">
                               <input type="hidden" id="jumlah_semua" name="cst_val" value="C">
                               <input type="hidden" id="type" name="type" value="KIB C">
                            </div>
                      
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="nama__aset" placeholder="{{ __('Nama Aset') }}"  readonly>
                                </div>
                            </div>

                        </div>

                        

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Keterangan Tambahan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" placeholder="{{ __('Keterangan') }}" name="description" ></textarea>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                        <button type="submit" onclick="submitForm()" class="btn btn-primary base-form--submit-modal" data-submit="0">
                            <i class="fa fa-save mr-1"></i>
                            {{ __('Simpan') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->
@endsection


