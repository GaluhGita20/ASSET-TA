<div class="modal fade modal-loading" id="modalReject"
	data-keyboard="false"
	data-backdrop="static"
	aria-hidden="true">
	<div class="modal-dialog modal-md modal-dialog-centered">
		<div class="modal-content">
			
			<form action="{{ rut($routes.'.reject', $record->id) }}" method="POST" autocomplete="off">
				@csrf
				{{-- {{ dd($record) }} --}}
				@method('POST')
				<input type="hidden"
                       name="action"
                       value="reject">
				<div class="modal-header">
					<h4 class="modal-title">Pembatalan</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i aria-hidden="true" class="ki ki-close"></i>
					</button>
				</div>
				<div class="modal-body text-left">
					<div class="form-group">
						<label>{{ __('Catatan') }}</label>
						<div class="parent-group">
							<textarea name="note" class="form-control" placeholder="{{ __('Catatan') }}"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger base-form--submit-modal"
						data-swal-confirm="true"
						{{-- @if(in_array($module,["pengajuan_pembelian.pengajuan", "pelepasan-aktiva.penghapusan", "sgu","mutasi-aktiva.pengajuan"]))
						data-swal-title = "{{in_array($module, ['pengajuan_pembelian.pengajuan', 'pelepasan-aktiva.penghapusan', 'sgu']) ? "Batalkan " . $title . "<br>" . $record->code . "?": ""}}"
						@elseif(in_array($module,["ump.pengajuan-ump"]))
						data-swal-title="Batalkan Pengajuan UMP?"
						@endif --}}
						data-swal-text="{{ __('base.confirm.save.text') }}"
						data-swal-ok="{{ __('base.confirm.reject.ok') }}"
						data-swal-cancel="{{ __('base.confirm.reject.cancel') }}">
						<i class="fa fa-times mr-1"></i>
						{{ __('Submit') }}
					</button>
				</div>
			</form>
			<div class="modal-loader pt-6" style="display: none;">
				<span class="spinner spinner-primary"></span>
			</div>
		</div>
	</div>
</div>

<div class="modal fade modal-loading" id="modalApprove"
	data-keyboard="false"
	data-backdrop="static"
	aria-hidden="true">
	<div class="modal-dialog modal-md modal-dialog-centered">
		<div class="modal-content">
			
			<form action="{{ rut($routes.'.approve', $record->id) }}" method="POST" autocomplete="off">
				@csrf
				{{-- {{ dd($record) }} --}}
				@method('POST')
				
				<div class="modal-header">
					<h4 class="modal-title">Input Data SP2D (Surat Perintah Pencairan Dana)</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i aria-hidden="true" class="ki ki-close"></i>
					</button>
				</div>
				<div class="modal-body text-left">
					<div class="form-group">
						<label>{{ __('SP2D Code') }}</label>
						<div class="parent-group">
							@if($record->sp2d_code == null)
								<input type="text" name="sp2d_code" class="form-control" placeholder="{{ __('Kode SP2D') }}">
							@else
								<input type="text" name="sp2d_code" class="form-control" value="{{$record->sp2d_code}}" placeholder="{{ __('Kode SP2D') }}">
							@endif
						</div>
					</div>
				</div>

                <div class="modal-body text-left">
					<div class="form-group">
						<label>{{ __('SP2D Date') }}</label>
						<div class="parent-group">
							@if($record->sp2d_date == null)
								<input class="form-control base-plugin--datepicker" name="sp2d_date" placeholder="{{ __('Tanggal SP2D') }}">
							@else
								<input class="form-control base-plugin--datepicker" name="sp2d_date" value="{{$record->sp2d_date}}" placeholder="{{ __('Tanggal SP2D') }}" data-date-end-date="{{ now()}}">
							@endif
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary base-form--submit-modal"
						data-swal-confirm="true"
						data-swal-text="{{ __('base.confirm.save.text') }}"
						{{-- data-swal-ok="{{ __('base.confirm.reject.ok') }}" --}}
						>
						<i class="fa fa-check mr-1"></i>
						{{ __('Verify') }}
					</button>
				</div>
			</form>
			<div class="modal-loader pt-6" style="display: none;">
				<span class="spinner spinner-primary"></span>
			</div>
		</div>
	</div>
</div>


<div class="modal fade modal-loading" id="modalApprove2"
	data-keyboard="false"
	data-backdrop="static"
	aria-hidden="true">
	<div class="modal-dialog modal-md modal-dialog-centered">
		<div class="modal-content">
			
			<form action="{{ rut($routes.'.approve', $record->id) }}" method="POST" autocomplete="off">
				@csrf
				@method('POST')
				<div class="modal-body text-left">
					<div class="form-group">
						<label>{{ __('Sumber Pendanaan') }}</label>
						<div class="parent-group">
							<select name="source_fund_id" class="form-control base-plugin--select2-ajax"
								data-url="{{ rut('ajax.selectSSBiaya', [
									'search'=>'all'
								]) }}" autofocus style="border: 1px solid #007bff;" data-placeholder="{{ __('Pilih Salah Satu Sumber Pendanaan') }}">
								<option value="" selected>{{ __('Pilih Salah Satu Sumber Pendanaan') }}</option>
								@if ($record->source_fund_id != null)
									<option value="{{ $record->source_fund_id }}" selected>{{ $record->danad->name }}</option>
								@endif
							</select>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary base-form--submit-modal"
						data-swal-confirm="true"
						data-swal-text="{{ __('base.confirm.save.text') }}"
						{{-- data-swal-ok="{{ __('base.confirm.reject.ok') }}" --}}
						>
						<i class="fa fa-check mr-1"></i>
						{{ __('Approve') }}
					</button>
				</div>
			</form>
			<div class="modal-loader pt-6" style="display: none;">
				<span class="spinner spinner-primary"></span>
			</div>
		</div>
	</div>
</div>



<div class="modal fade modal-loading" id="modalApprove3"
	data-keyboard="false"
	data-backdrop="static"
	aria-hidden="true">
	
	<div class="modal-dialog modal-md modal-dialog-centered">
		<div class="modal-content">
			<!-- Tombol Close -->
			<div class="modal-header">
				<h5 class="modal-title">{{ __('Tanggal Pemanggilan') }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<form action="{{ rut($routes.'.approve', $record->id) }}" method="POST" autocomplete="off">
				@csrf
				@method('POST')
				
				<div class="modal-body text-left">
					<div class="form-group">
						<label>{{ __('Tanggal Panggil') }}</label>
						<div class="parent-group">
							@if(!empty($record->repair_date))
								<input name="repair_date" class="form-control" value="{{ $record->repair_date->format('Y/m/d') }}">
							@else
								{{-- @if(auth()->user()->hasRole('Sarpras') && request()->route()->getName() == $routes.'.approval1') --}}
								<input name="repair_date" class="form-control filter-control base-plugin--datepicker" autofocus style="border: 1px solid #007bff;">
								{{-- @endif --}}
							@endif
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary base-form--submit-modal"
						data-swal-confirm="true"
						data-swal-text="{{ __('base.confirm.save.text') }}"
						{{-- data-swal-ok="{{ __('base.confirm.reject.ok') }}" --}}
						>
						<i class="fa fa-check mr-1"></i>
						{{ __('Verify') }}
					</button>
					<!-- Tambahkan tombol Close di footer -->
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Cancel') }}</button>
				</div>
			</form>
			<div class="modal-loader pt-6" style="display: none;">
				<span class="spinner spinner-primary"></span>
			</div>
		</div>
	</div>
</div>








{{-- <div class="modal fade modal-loading" id="modalReject"
	data-keyboard="false"
	data-backdrop="static"
	aria-hidden="true">
	<div class="modal-dialog modal-md modal-dialog-centered">
		<div class="modal-content">
			<form action="{{ route($routes.'.reject', $record->id) }}" method="POST" autocomplete="off">
				@csrf
				@method('PUT')
				<div class="modal-header">
					<h4 class="modal-title">Reject Data</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i aria-hidden="true" class="ki ki-close"></i>
					</button>
				</div>
				<div class="modal-body text-left">
					<div class="form-group">
						<label>{{ __('Catatan') }}</label>
						<div class="parent-group">
							<textarea name="note" class="form-control" placeholder="{{ __('Catatan') }}"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger base-form--submit-modal"
						data-swal-confirm="true"
						data-swal-title="{{ __('base.confirm.reject.title') }}"
						data-swal-text="{{ __('base.confirm.reject.text') }}"
						data-swal-ok="{{ __('base.confirm.reject.ok') }}"
						data-swal-cancel="{{ __('base.confirm.reject.cancel') }}">
						<i class="fa fa-times mr-1"></i>
						{{ __('Reject') }}
					</button>
				</div>
			</form>
			<div class="modal-loader pt-6" style="display: none;">
				<span class="spinner spinner-primary"></span>
			</div>
		</div>
	</div>
</div> --}}
