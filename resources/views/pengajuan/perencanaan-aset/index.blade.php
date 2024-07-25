@extends('layouts.lists')

@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Surat') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            @if($module == 'perencanaan-aset-pelayanan')
                <select class="form-control filter-control base-plugin--select2-ajax" name="struct_id" data-url="{{ route('ajax.selectUmum', 'object_aset') }}"
                    data-placeholder="{{ __('Unit Kerja') }}" data-post="struct_id">
                </select>
            @elseif($module == 'perencanaan-aset')
                <select class="form-control filter-control base-plugin--select2-ajax" name="struct_id" data-url="{{ route('ajax.selectPenunjang', 'object_aset') }}"
                    data-placeholder="{{ __('Unit Kerja') }}" data-post="struct_id">
                </select>
            @endif
            

        </div>
        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
            <select class="form-control base-plugin--select2-ajax filter-control"
                data-post="procurement_year"
                data-placeholder="{{ __('Periode Usulan') }}">
                <option value="" selected>{{ __('Periode Usulan') }}</option>
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
    </div>
    {{-- penunjang --}}
    @if($module != 'perencanaan-aset-pelayanan') 
        <div class="alert alert-custom alert-light-primary fade show py-3" style="left:-30pt;" role="alert">
            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
            <div class="alert-text text-primary">
                <div class="text-bold">{{ __('Informasi') }}:</div>
                <div class="mb-10px" style="white-space: pre-wrap;">Pengajuan Perencanaan Aset Untuk Unit Umum Dibawah Departemen Penunjang
                </div>
            </div>
        </div>
    @else
    {{-- umum --}}
        <div class="alert alert-custom alert-light-primary fade show py-3" style="left:-30pt;" role="alert">
            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
            <div class="alert-text text-primary">
                <div class="text-bold">{{ __('Informasi') }}:</div>
                <div class="mb-10px" style="white-space: pre-wrap;">Pengajuan Perencanaan Aset Untuk Unit Umum Dibawah Departemen Pelayanan , PSDM , dan Bagian Umum
                </div>
            </div>
        </div>
    @endif
@endsection

{{-- @php
    dd(auth()->user()->position->location->parent_id);
@endphp --}}
@section('buttons')
    @if(auth()->user()->roles[0]->name != 'Direksi')
        @if (auth()->user()->checkPerms($perms.'.create'))
            @if($module == 'perencanaan-aset-pelayanan' && auth()->user()->position->location_id != 3 || $module == 'perencanaan-aset-pelayanan' && auth()->user()->position->location->name != 'Bidang Penunjang Medik dan Non Medik' || auth()->user()->position->location->name == 'Sub Bagian Program Perencanaan dan Pelaporan' )
                @include('layouts.forms.btnAdd')
            @elseif($module == 'perencanaan-aset' && auth()->user()->position->location_id == 3 || $module == 'perencanaan-aset' && auth()->user()->position->location->parent_id == 3)
                @include('layouts.forms.btnAdd')
            @endif
        @endif
    @endif
@endsection

@section('buttons')
@endsection
