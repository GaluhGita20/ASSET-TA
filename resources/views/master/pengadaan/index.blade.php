@extends('layouts.lists')
@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="name" placeholder="{{ __('Jenis Pengadaan') }}">
        </div>

        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2" name="tipe_akun"
                data-post="tipe_akun"
                data-placeholder="{{ __('Tipe Akun Utama') }}"
            >
            <option value="">Pilih Salah Satu</option>
            <option value="tanah">Tanah</option>
            <option value="gedung_bangunan">Gedung Bangunan</option>
            <option value="peralatan_mesin">Peralatan Mesin</option>
            <option value="jalan_irigasi_jaringan">Jalan Irigasi Jaringan</option>
            <option value="aset_tetap_lainya">Aset Tetap Lainya</option>
            </select>
        </div> --}}
    </div>
@endsection

@section('buttons')
	@if (auth()->user()->checkPerms($perms.'.create'))
        <a href="{{ $urlAdd ?? (\Route::has($routes.'.create') ? route($routes.'.create') : 'javascript:;') }}"
            class="btn btn-info base-modal--render"
            data-modal-size="{{ $modalSize ?? 'modal-lg' }}"
            data-modal-backdrop="false"
            data-modal-v-middle="false">
            <i class="fa fa-plus"></i> Data
        </a>
	@endif
@endsection
