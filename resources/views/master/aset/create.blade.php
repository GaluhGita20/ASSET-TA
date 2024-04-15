@extends('layouts.modal')

@section('action', route($routes.'.store'))
@section('modal-body')
    @method('POST')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Nama Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input name="name" type="text" class="form-control" placeholder="{{ __('Nama Aset') }}">
        </div>
    </div>

  <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Kategori Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
        <select class="form-control base-plugin--select2-ajax" name="jenis_aset" data-placeholder="Jenis Aset">
            <option disabed value="">Tipe Akun Utama</option>
            <option value="Tanah">Tanah</option>
            <option value="Peralatan Mesin">Peralatan Mesin</option>
            <option value="Gedung Bangunan">Gedung Bangunan</option>
            <option value="Aset Tetap Lainya">Aset Tetap Lainya</option>
            <option value="Jalan Irigasi Jaringan">Jalan Irigasi Jaringan</option>
        </select>
        </div>
    </div> 
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
