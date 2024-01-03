@extends('layouts.pageSubmit')

@section('action', route($routes . '.store'))

@section('card-body')
@section('page-content')
    @method('POST')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                </div>
                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                        @include('layouts.forms.btnSubmitModal')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->
@endsection

@push('scripts')
	<script>
		$(function () {
			$('.content-page').on('change', 'select.aset_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.detailPembelian');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({aset_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                    objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});
		});
	</script>    
@endpush