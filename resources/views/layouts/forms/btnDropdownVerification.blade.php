<div class="btn-group dropdown">
	<button type="button"
		class="btn btn-primary dropdown-toggle"
		data-toggle="dropdown"
		aria-haspopup="true"
		aria-expanded="false">
		<i class="mr-1 fa fa-save"></i> {{ __('Verifikasi') }}
	</button>
	<div class="dropdown-menu dropdown-menu-right">
		<button type="button"
			class="dropdown-item align-items-center base-form--approveByUrl"
			data-url="{{ $urlApprove ?? (!empty($record) && \Route::has($routes.'.verify') ? rut($routes.'.verify', $record->id) : '') }}">
			<i class="mr-3 fa fa-check text-primary"></i> {{ __('Setujui') }}
		</button>
		<button type="button" class="dropdown-item"
			data-toggle="modal"
			data-target="#modalReject">
			<i class="mr-4 fa fa-times text-danger"></i> {{ __('Batal') }}
		</button>
	</div>
</div>
