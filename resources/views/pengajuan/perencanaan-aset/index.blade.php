@extends('layouts.lists')

@section('filters')
	<div class="row">
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="code" placeholder="{{ __('No Pengajuan') }}">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <select class="form-control filter-control base-plugin--select2-ajax" name="struct_id" data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                data-placeholder="{{ __('Unit Kerja') }}" data-post="struct_id">
            </select>
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
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <div class="input-group">
                <input name="date_start"
                    class="form-control base-plugin--datepicker date_start"
                    placeholder="{{ __('Mulai') }}"
                    data-orientation="bottom"
                    data-post="date_start"
                    >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="la la-ellipsis-h"></i>
                    </span>
                </div>
                <input name="date_end"
                    class="form-control filter-control base-plugin--datepicker date_end"
                    placeholder="{{ __('Selesai') }}"
                    data-orientation="bottom"
                    data-post="date_end"
                    >
            </div>
        </div> --}}
    </div>
@endsection

@section('buttons')
    @if(auth()->user()->roles[0]->name != 'Direksi')
        @if (auth()->user()->checkPerms($perms.'.create'))
            @include('layouts.forms.btnAdd')
        @endif
    @endif
@endsection

@section('buttons')
@endsection
