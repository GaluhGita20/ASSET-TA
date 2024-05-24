<form action="@yield('action')" method="POST" autocomplete="@yield('autocomplete', 'off')">
	<div class="modal-header">
		<h4 class="modal-title">@yield('modal-title', $title)</h4>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<i aria-hidden="true" class="ki ki-close"></i>
		</button>
	</div>
	<div class="modal-body pt-3">
		@csrf
		@yield('modal-body')
	</div>

	<?php
// Periksa apakah variabel $type telah didefinisikan
	if (!isset($type)) {
		// Jika belum, atur $type ke nilai default 'create'
		$type = 'create';
	}
	?>

	@if ($type != 'show')
	{{-- @if(request()->route()->getName() != $routes.'.show') --}}
		@section('buttons')
			<div class="modal-footer">
				{{-- @if($type == 'edit' || $type=='create') --}}
					@section('modal-footer')
						@include('layouts.forms.btnSubmitModal')
					@show
				{{-- @endif --}}
			</div>
		@show
	@endif
</form>


{{-- <script src="{{ (('/assets/js/plugins.bundle.js')) }}"></script>
        {{-- <script src="{{ ('/assets/js/theme.config.js')) }}"></script> --}}
{{-- <script src="{{ (('/assets/js/theme.bundle.js')) }}"></script>
<script src="{{ (('/assets/js/base.bundle.js')) }}"></script>
<script src="{{ (('/assets/js/modules.bundle.js')) }}"></script>  --}}
		{{-- @stack('scripts') --}}
<script>
	// Mendaftarkan fungsi untuk menangani penutupan modal saat tombol close diklik
	$(document).ready(function () {
		$('.modal .modal-header .close').click(function () {
			$(this).closest('.modal').modal('hide');
		});
	});
</script>
@stack('scripts')
