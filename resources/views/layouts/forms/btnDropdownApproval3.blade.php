<div class="btn-group dropdown">
	<button type="button"
		class="btn btn-primary dropdown-toggle"
		data-toggle="dropdown"
		aria-haspopup="true"
		aria-expanded="false">
		<i class="mr-1 fa fa-save"></i> {{ __('Verify') }}
	</button>
	<div class="dropdown-menu dropdown-menu-right">

		<button type="button" class="dropdown-item"
			data-toggle="modal"
			data-target="#modalApprove3">
			<i class="mr-3 fa fa-check text-primary"></i> {{ __('Verify') }}
		</button>
		<button type="button" class="dropdown-item"
			data-toggle="modal"
			data-target="#modalReject">
			<i class="mr-4 fa fa-times text-danger"></i> {{ __('Reject') }}
		</button>
	</div>
</div>
