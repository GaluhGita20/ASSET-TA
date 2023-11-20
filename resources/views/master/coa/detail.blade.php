@extends('layouts.modal')

@if ($page_action == "create")
    @section('action', route($routes.'.store'))
@elseif ($page_action == "edit")
    @section('action', route($routes.'.update', $record->id))
@endif

@section('modal-body')
    @if ($page_action == "edit")
        @method('PUT')
    @endif
    
	<div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Kode Akun') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input type="number" name="kode_akun" class="form-control" placeholder="{{ __('Kode Akun') }}"
        {{$page_action == "show" ? "readonly" : ""}} {{$page_action == "edit" ? "readonly" : ""}}
                value="{{$page_action != "create" ? $record->kode_akun : ""}}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Nama Akun') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <input type="text" name="nama_akun" class="form-control" placeholder="{{ __('Nama Akun') }}"
        {{$page_action == "show" ? "readonly" : ""}}
                value="{{$page_action != "create" ? $record->nama_akun : ""}}">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Tipe Akun Utama') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
        <select class="form-control base-plugin--select2-ajax" name="tipe_akun" data-placeholder="Tipe Akun Utama"
        disabled>
            {{-- <option {{$page_action == "create" ? "selected" : ""}} disabed value="">Tipe Akun Utama</option> --}}
            <option disabed value="">Tipe Akun Utama</option>
            <option value="KIB A" {{ $tipe_akun == "KIB A" ? 'selected' : '' }}>KIB A</option>
            <option value="KIB B" {{ $tipe_akun == "KIB B" ? 'selected' : '' }}>KIB B</option>
            <option value="KIB C" {{ $tipe_akun == "KIB C" ? 'selected' : '' }}>KIB C</option>
            <option value="KIB D" {{ $tipe_akun == "KIB D" ? 'selected' : '' }}>KIB D</option>
            <option value="KIB E" {{ $tipe_akun == "KIB E" ? 'selected' : '' }}>KIB E</option>
            <option value="KIB F" {{ $tipe_akun == "KIB F" ? 'selected' : '' }}>KIB F</option>
        </select>

        @if ($page_action == "show" || $page_action == "edit")
            <input type="hidden" name="tipe_akun" value="{{$record->tipe_akun}}">
        @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-md-4 col-form-label">{{ __('Deskripsi') }}</label>
        <div class="col-sm-12 col-md-8 parent-group">
            <textarea type="text" name="deskripsi" class="form-control" placeholder="{{ __('Deskripsi') }}" {{$page_action == "show" ? "disabled" : ""}}  rows="3">{{ $record->deskripsi ?? "" }}</textarea>
        </div>
    </div>
@endsection

@if (!in_array($page_action, ["edit", "create"]))
@section('buttons')
@endsection
@endif

@push('scripts')
	<script>
		$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
	</script>
@endpush
