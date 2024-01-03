@extends('layouts.modal')

@section('action', route($routes.'.update',$record->id))
@section('modal-body')
    @method('PATCH')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Nama Aset') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input name="name" type="text" value="{{ $record->name }}" class="form-control" placeholder="{{ __('Nama Aset') }}">
        </div>
    </div>

  <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Kategori Aset') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
        <select class="form-control base-plugin--select2-ajax" name="jenis_aset" data-placeholder="Kategori Aset">
            <option disabed value="">Tipe Akun Utama</option>
            <option value="Tanah" {{ $record->jenis_aset =='Tanah' ? 'selected' : '-' }}>Tanah</option>
            <option value="Peralatan Mesin" {{ $record->jenis_aset =='Peralatan Mesin' ? 'selected' : '-' }}>Peralatan Mesin</option>
            <option value="Gedung Bangunan" {{ $record->jenis_aset =='Gedung Bangunan' ? 'selected' : '-' }}>Gedung Bangunan</option>
            <option value="Aset Tetap Lainya" {{ $record->jenis_aset =='Aset Tetap Lainya' ? 'selected' : '-' }}>Aset Tetap Lainya</option>
            <option value="Jalan Irigasi Jaringan" {{ $record->jenis_aset =='Jalan Irigasi Jaringan' ? 'selected' : '-' }}>Jalan Irigasi Jaringan</option>
        </select>
        </div>
    </div> 
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
