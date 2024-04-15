@extends('layouts.lists')


@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Pemeliaharaan') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="departemen_id" data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                data-placeholder="{{ __('Unit Kerja') }}" data-post="departemen_id">
            </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
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
        <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control" name="maintenance_date_year" data-post="maintenance_date_year">
                <option value="" selected>{{ __('Pilih Tahun Pemeliharaan') }}</option>
                <?php
                $currentYear = date('Y');
                $startYear = 2020;
                for ($year = $currentYear; $year >= $startYear; $year--) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="col-12 col-sm-3 col-xl-3 pb-2 mr-n6">
            {{-- <input type='text' class="form-control filter-control  base-plugin--datepicker" name="maintenance_date_month" 
                placeholder="{{ __('Bulan Pemeliharaan') }}" data-post="maintenance_date_month"> --}}
                <select class="form-control base-plugin--select2-ajax filter-control"
				data-post="maintenance_date_month"
                name="maintenance_date_month">
				<option value="" selected>{{ __('Pilih Bulan Pemeliharaan') }}</option>
				<option value="01">Januari</option>
				<option value="02">Februari</option>
                <option value="03">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
				<option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">September</option>
                <option value="10">Oktober</option>
				<option value="11">November</option>
                <option value="12">Desember</option>
			</select>
        </div>

        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type='text' class="form-control filter-control  base-plugin--datepicker" name="maintenance_date" 
                placeholder="{{ __('Tanggal Pemeliharaan') }}" data-post="maintenance_date">
        </div> --}}
        {{-- <div class="col-4 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input class="form-control filter-control base-plugin--datepicker" name="maintenance_date" data-post="maintenance_date" placeholder="{{ __('Tanggal Pemeliharaan')}}">
        </div> --}}
    </div>
    <div class="alert alert-custom alert-light-primary fade show py-4" style="left:-30pt;" role="alert">
        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
        <div class="alert-text text-primary">
            <div class="text-bold">{{ __('Informasi') }}:</div>
            <div class="mb-10px" style="white-space: pre-wrap;">Pemeliharaan Aset Dilakukan Setiap Bulan Untuk Setiap Unit Departemen</div>
        </div>
    </div>
    
@endsection

@section('buttons')
    @if(auth()->user()->hasRole('Sarpras'))
        @include('layouts.forms.btnAdd')
    @endif

@endsection
