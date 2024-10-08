@if (!empty($record))
	@php 
		if($record->perbaikan_id){
			$dep =  \App\Models\Pengajuan\Perbaikan::where('id',$record->perbaikan_id)->value('departemen_id');
			$parent = \App\Models\Master\Org\OrgStruct::where('id',$dep)->value('parent_id');
	
			if($parent == 3 || $dep == 3){
				$module = 'usulan_pembelian-sperpat';
			}else{
				$module = 'usulan_pembelian-sperpat-umum';
			}
		}
	@endphp
	@if ($approval = $record->rejected($module))
		<div class="alert alert-custom alert-light-warning fade show py-4" role="alert">
			<div class="alert-icon"><i class="flaticon-warning"></i></div>
			<div class="alert-text text-danger">
				<div class="text-bold">{{ __('Catatan') }}:</div>
				<div class="mb-10px" style="white-space: pre-wrap;">{!! $approval->note !!}</div>

				<div class="text-muted">Rejected by : {{ $approval->user->name ?? '-' }}</div>
				<div class="text-muted">Rejected at : {{ $approval->creationDate() }}</div>
			</div>
			<div class="alert-close">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="ki ki-close"></i></span>
				</button>
			</div>
		</div>
	@endif
@endif