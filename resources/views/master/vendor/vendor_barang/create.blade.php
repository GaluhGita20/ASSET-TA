{{-- @extends('layouts.form') --}}
@extends('layouts.modal')

@section('action', route($routes.'.store'))

@section('modal-body')
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                    <label class="col-sm-12 col-md-3 col-form-label">{{ __('Vendor') }}</label>
                    <div class=" col-sm-12 col-md-9 parent-group">
                        <input name="name" type="text" class="form-control" placeholder="{{ __('Vendor') }}">
                    </div>
            </div>
        </div>
        <div class="col-sm-12 col-sm-12">
                        <div class="form-group row">
                <label class="col-sm-12 col-md-3 col-form-label">{{ __('Alamat') }}</label>
                <div class="col-sm-12 col-md-9 parent-group">
                    <textarea name="address" type="text" class="form-control" placeholder="{{ __('Alamat') }}"></textarea>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                        <label class="col-sm-12 col-md-3 col-form-label">{{ __('Telepon') }}</label>
                        <div class="col-sm-12 col-md-9 parent-group">
                            <input type="tel" name="telp" class="form-control" placeholder="{{ __('Telepon') }}"
                                pattern="[0-9]{4}[0-9]{4}-[0-9]{0,7}">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-12 col-md-3 col-form-label">{{ __('Email') }}</label>
                        <div class="col-sm-12 col-md-9 parent-group">
                            <input type="email" name="email" class="form-control" placeholder="{{ __('Email') }}">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-12 col-md-3 col-form-label">{{ __('Contact Person') }}</label>
                        <div class="col-sm-12 col-md-9 parent-group">
                            <input name="contact_person" class="form-control"
                                placeholder="{{ __('Contact Person') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
	$('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
</script>
	<script>
		$(document).ready(function(){
			$('.content-page').on('click', '.btn-delete', function(e){
				e.preventDefault()
				let id = $(this).data('id')
				$(`tr#${id}`).remove()
			})
		})
	</script>
@endpush

@push('scripts')
    @include("master.vendor_barang.include.scripts")
@endpush
