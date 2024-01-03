@extends('layouts.pageSubmit', ['container' => 'container'])

@section('action', route($routes . '.store'))

@section('page-content')
	@method('POST')
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="row mb-3">
                    <div class="col-sm-12">
                        <div class="card card-custom">
                            <div class="card-body p-8">
	                            @include($views.'.includes.detail-grid')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @section('buttons')
    @if(auth()->user()->hasRole('PPK') )
    {{-- @if(auth()->user()->roles[0]->name != 'Direksi') --}}
        {{-- @if (auth()->user()->checkPerms($perms.'.create'))
        <a href="{{ $urlAdd ?? (\Route::has($routes.'.create') ? rut($routes.'.create') : 'javascript:;') }}"
            class="btn btn-info ml-2 {{ empty($baseContentReplace) ? 'base-modal--render' : 'base-content--replace' }}"
            data-modal-backdrop="false"
            data-modal-size="{{ $modal_size ?? 'modal-md' }}"
            data-modal-v-middle="false">
            <i class="fa fa-plus"></i> Data
        </a>
        @endif
    @endif

@endsection  --}}

@push('script')

    
@endpush

