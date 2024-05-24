@extends('layouts.lists')


@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Perbaikan') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="vendor_id" data-url="{{ route('ajax.selectVendor', 'all') }}"
                data-placeholder="{{ __('Nama Vendor') }}" data-post="vendor_id">
            </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2" name="repair_type"
                data-post="repair_type"
                data-placeholder="{{ __('Jenis Perbaikan') }}"
            >
            <option value="">Pilih Salah Satu</option>
            <option value="sperpat">Sperpat</option>
            <option value="vendor">Vendor</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
			<select class="form-control base-plugin--select2-ajax filter-control"
				data-post="sper_status"
				data-placeholder="{{ __('Status') }}">
				<option value="" selected>{{ __('Status') }}</option>
				<option value="Draft">Draft</option>
				<option value="waiting.approval">Waiting Approval</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
			</select>
		</div>
        
        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
            <select class="form-control base-plugin--select2-ajax filter-control"
                data-post="procurement_year"
                data-placeholder="{{ __('Periode Usulan Sperpat') }}">
                <option value="" selected>{{ __('Periode Usulan Sperpat') }}</option>
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
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="departemen_id" data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                data-placeholder="{{ __('Unit Kerja') }}" data-post="departemen_id">
            </select>
        </div> --}}
        {{-- <div class="col-4 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input class="form-control filter-control base-plugin--datepicker" data-post="submission_date" placeholder="{{ __('Tanggal Pengajuan') }}">
        </div> --}}
    </div>
@endsection

@section('buttons')
    @if(auth()->user()->roles[0]->name == 'Sarpras')
        @if (auth()->user()->checkPerms($perms.'.create'))
            @include('layouts.forms.btnAdd')
        @endif
    @endif
@endsection
