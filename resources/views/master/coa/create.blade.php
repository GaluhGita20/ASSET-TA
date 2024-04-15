@extends('layouts.modal')

@section('action', route($routes.'.store'))
@section('modal-body')
    @method('POST')
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Kode Akun') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input type="number" name="kode_akun" class="form-control" placeholder="{{ __('Kode Akun') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Nama Akun') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input type="text" name="nama_akun" class="form-control" placeholder="{{ __('Nama Akun') }}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Tipe Akun Utama') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
        <select class="form-control base-plugin--select2-ajax" name="tipe_akun" data-placeholder="Tipe Akun Utama" disabled>
            {{-- <option disabed value="">Tipe Akun Utama</option> --}}
            <option value="KIB A" {{ $tipe_akun == "KIB A" ? 'selected' : '' }}>KIB A</option>
            <option value="KIB B" {{ $tipe_akun == "KIB B" ? 'selected' : '' }}>KIB B</option>
            <option value="KIB C" {{ $tipe_akun == "KIB C" ? 'selected' : '' }}>KIB C</option>
            <option value="KIB D" {{ $tipe_akun == "KIB D" ? 'selected' : '' }}>KIB D</option>
            <option value="KIB E" {{ $tipe_akun == "KIB E" ? 'selected' : '' }}>KIB E</option>
            <option value="KIB F" {{ $tipe_akun == "KIB F" ? 'selected' : '' }}>KIB F</option>
        </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Deskripsi') }}<span style=" color: red;margin-left: 5px;">*</span></label>
        <div class="col-sm-12 col-md-8 parent-group">
            <textarea type="text" name="deskripsi" class="form-control" placeholder="{{ __('Deskripsi') }}" rows="3"></textarea>
        </div>
    </div>
@endsection

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
