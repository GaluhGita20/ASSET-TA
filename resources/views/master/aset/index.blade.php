@extends('layouts.lists')
@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="name" placeholder="{{ __('Nama Aset') }}">
        </div>
    
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control base-plugin--select2-ajax" name="jenis_aset" data-placeholder="Jenis Aset">
                <option disabed value="">Tipe Akun Utama</option>
                <option value="Tanah">Tanah</option>
                <option value="Peralatan Mesin">Peralatan Mesin</option>
                <option value="Gedung Bangunan">Gedung Bangunan</option>
                <option value="Aset Tetap Lainya">Aset Tetap Lainya</option>
                <option value="Jalan Irigasi Jaringan">Jalan Irigasi Jaringan</option>
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
