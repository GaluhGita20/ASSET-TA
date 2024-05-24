@extends('layouts.lists')


@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Pengajuan') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="departemen_id" data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                data-placeholder="{{ __('Unit Kerja') }}" data-post="departemen_id">
            </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2">
            <select class="form-control base-plugin--select2-ajax filter-control"
                data-post="kib_id"
                name= "kib_id"
                data-url="{{ route('ajax.selectAsetRS', 'all') }}"
                placeholder="{{ __('Pilih Salah Satu') }}">
                <option value="">{{ __('Pilih Salah Satu') }}</option>
            </select>
            {{-- <input type="text" class="form-control filter-control" data-post="aset_name" name="aset_name"
                placeholder="{{ __('Nama Aset') }}"> --}}
        </div>
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2">
            <input type='text' class="form-control filter-control  base-plugin--datepicker" name="submission_date" 
                placeholder="{{ __('Tanggal Pengajuan') }}" data-post="submission_date">
        </div> --}}

        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
            <select class="form-control base-plugin--select2-ajax filter-control"
                data-post="submission_date"
                data-placeholder="{{ __('Periode Usulan Perbaikan') }}">
                <option value="" selected>{{ __('Periode Usulan Perbaikan') }}</option>
                @php
                    $startYear = 2020;
                    $currentYear = date('Y');
                    $endYear = $currentYear + 5;
                @endphp
                @for ($year = $startYear; $year <= $endYear; $year++)
                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
            {{-- <input type="text" class="form-control filter-control" data-post="procurement_year" placeholder="{{ __('Periode Perencanaan') }}"> --}}
        </div>

        <div class="col-12 col-sm-6 col-xl-3 pb-2">
			<select class="form-control base-plugin--select2-ajax filter-control"
				data-post="status"
				data-placeholder="{{ __('Status') }}">
				<option value="" selected>{{ __('Status') }}</option>
				<option value="Draft">Draft</option>
				<option value="waiting.approval">Waiting Approval</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
			</select>
		</div>

        

    </div>
@endsection

@section('buttons')
@endsection
