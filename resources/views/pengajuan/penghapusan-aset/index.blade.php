@extends('layouts.lists')


@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Perbaikan') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="departemen_id" data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                data-placeholder="{{ __('Unit Kerja') }}" data-post="departemen_id">
            </select>
        </div>
        <div class="col-4 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input class="form-control filter-control base-plugin--datepicker" data-post="submission_date" placeholder="{{ __('Tanggal Pengajuan') }}">
        </div>
    </div>
@endsection

@section('buttons')
@endsection
