@extends('layouts.modal')

@section('action', route($routes.'.store'))
@section('modal-body')
    @method('POST')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Sumber Dana') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input name="name" type="text" class="form-control" placeholder="{{ __('Sumber Dana') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Deskripsi Sumber Dana') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <textarea name="description" class="form-control" placeholder="{{ __('Deskripsi') }}"></textarea>
        </div>
    </div>
    {{-- <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Tipe Akun Utama') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
        <select class="form-control base-plugin--select2-ajax" name="jenis_aset" data-placeholder="Jenis Aset">
            {{-- <option disabed value="">Tipe Akun Utama</option> --}}
            {{-- <option value="tanah">Tanah</option>
            <option value="peralatan_mesin">Peralatan Mesin</option>
            <option value="gedung_bangunan">Gedung Bangunan</option>
            <option value="aset_tetap_lainya">Aset Tetap Lainya</option>
            <option value="jalan_irigasi_jaringan">Jalan Irigasi Jaringan</option>
        </select>
        </div>
    </div> --}} 
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
