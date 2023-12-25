@extends('layouts.modal')

@section('action', route($routes.'.update',$record->id))
@section('modal-body')
    @method('PATCH')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Nama Aset') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input name="name" type="text" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama Aset') }}" readonly>
        </div>
    </div>

  <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Kategori Aset') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
        <select class="form-control base-plugin--select2-ajax" name="jenis_aset" data-placeholder="Kategori Aset" disabled>
            <option disabed value="">Tipe Akun Utama</option>
            <option value="tanah" {{ $record->jenis_aset =='tanah' ? 'selected' : '-' }}>Tanah</option>
            <option value="peralatan_mesin" {{ $record->jenis_aset =='peralatan_mesin' ? 'selected' : '-' }}>Peralatan Mesin</option>
            <option value="gedung_bangunan" {{ $record->jenis_aset =='gedung_bangunan' ? 'selected' : '-' }}>Gedung Bangunan</option>
            <option value="aset_tetap_lainya" {{ $record->jenis_aset =='aset_tetap_lainya' ? 'selected' : '-' }}>Aset Tetap Lainya</option>
            <option value="jalan_irigasi_jaringan" {{ $record->jenis_aset =='jalan_irigasi_jaringan' ? 'selected' : '-' }}>Jalan Irigasi Jaringan</option>
        </select>
        </div>
    </div> 
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
