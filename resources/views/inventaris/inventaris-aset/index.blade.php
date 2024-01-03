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
	                            @include($views.'.detail-grid')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('buttons')
@endsection


