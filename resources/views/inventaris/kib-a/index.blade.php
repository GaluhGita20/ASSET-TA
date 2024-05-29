@extends('layouts.lists')
@section('filters')
	<div class="row">
        {{-- <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="coa_id" placeholder="{{ __('Nama Aset') }}">
        </div> --}}
    
        <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
            <input type="text" class="form-control filter-control" data-post="jenis_aset" placeholder="{{ __('Nama Aset') }}">
        </div>

        <div class="col-12 col-sm-6 col-xl-2 pb-2 mr-n6">
			<select class="form-control base-plugin--select2-ajax filter-control"
				data-post="status"
				data-placeholder="{{ __('Status') }}">
				<option value="" selected>{{ __('Status') }}</option>
				<option value="actives">Active</option>
				<option value="notactive">Not active</option>
                <option value="diputihkan">Diputihkan</option>
			</select>
		</div>
    </div>
@endsection
@section('buttons')
<a href="{{ route($routes . '.export') }}" target="_blank" class="btn btn-info ml-2 export-excel text-nowrap">
    <i class="far fa-file-excel mr-2"></i> Excel
</a>
<a href="{{ route($routes . '.kib-pdf') }}" target="_blank" class="btn btn-danger ml-2 export-pdf text-nowrap">
    <i class="far fa-file-pdf mr-2"></i> Pdf
</a>
@endsection

{{-- @section('buttons')
	@if (auth()->user()->checkPerms($perms.'.create'))
        <a href="{{ $urlAdd ?? (\Route::has($routes.'.create') ? route($routes.'.create') : 'javascript:;') }}"
            class="btn btn-info base-modal--render"
            data-modal-size="{{ $modalSize ?? 'modal-lg' }}"
            data-modal-backdrop="false"
            data-modal-v-middle="false">
            <i class="fa fa-plus"></i> Data
        </a>
	@endif
@endsection --}}
