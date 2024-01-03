<div class="col-12">
	<div class="row card-progress-wrapper" data-url="{{ route($routes.'.progressAset') }}">
		@php
			$cards = collect(json_decode(json_encode([
				[
					'name' => 'Aset KIB A',
					'title' => 'Aset KIB A',
					'icon' => 'fas fa-paper-plane',
				],
				[
					'name' => 'Aset KIB B',
					'title' => 'Aset KIB B',
					'icon' => 'fa fa-tags',
				],
				[
					'name' => 'Aset KIB C',
					'title' => 'Aset KIB C',
					'icon' => 'fas fa-bookmark',
				],
				[
					'name' => 'Aset KIB D',
					'title' => 'Aset KIB D',
					'icon' => 'fas fa-id-card',
				],
                [
					'name' => 'Aset KIB E',
					'title' => 'Aset KIB E',
					'icon' => 'fas fa-bookmark',
				],
				[
					'name' => 'Aset KIB F',
					'title' => 'Aset KIB F',
					'icon' => 'fas fa-id-card',
				],
			])));
		@endphp
		@foreach ($cards as $card)
			<div class="col-xl-3 col-md-6 col-sm-12">
				<div class="card card-custom gutter-b card-stretch bg-white"
					data-name="{{ $card->name }}">
					<div class="card-body">
						<div class="d-flex flex-wrap align-items-center py-1">
							{{-- <div class="symbol symbol-40 symbol-light-{{ $card->color }} mr-5">
								<span class="symbol-label shadow">
									<i class="{{ $card->icon }} align-self-center text-{{ $card->color }} font-size-h5"></i>
								</span>
							</div> --}}
							<div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
								<div class="text-dark font-weight-bolder font-size-h5">
									{{ __($card->title) }}
								</div>
								<div class="text-muted font-weight-bold font-size-lg">
									<div class="d-flex justify-content-between">
										<span class="text-nowrap">Aktive/Not Active</span>
										<span class="text-nowrap">
											<span class="active">0</span>/<span class="not_active">0</span>
										</span>
									</div>
								</div>
							</div>
							<div class="d-flex flex-column w-100 mt-5">
								<div class="text-muted mr-2 font-size-lg font-weight-bolder pb-3">
									<div class="d-flex justify-content-between">
										<span class="percent-text">0%</span>
									</div>
								</div>
								<div class="progress progress-xs w-100">
									<div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>

@push('scripts')
	<script>
		$(function () {
			initCardProgress();
		});

		var initCardProgress = function () {
			var wrapper = $('.card-progress-wrapper');
			$.ajax({
				url: wrapper.data('url'),
				type: 'POST',
				data: {
					_token: BaseUtil.getToken(),
				},
				success: function (resp) {
					if (resp.data && resp.data.length) {
						$.each(resp.data, function (i, item) {
							console.log(item)
							var card = wrapper.find('.card[data-name="'+item.name+'"]');
							card.find('.active').html(item.active);
							card.find('.not_active').html(item.not_active);
							card.find('.percent-text').html(item.percent+'%');
							card.find('.progress-bar').css('width', item.percent+'%');
							card.find('.progress-bar').attr('aria-valuenow', item.percent);
							if (item.percent == 100){
								card.find('.progress-bar').removeClass('bg-danger');
								card.find('.progress-bar').addClass('bg-success');
							} else{
								card.find('.progress-bar').removeClass('bg-success');
								card.find('.progress-bar').addClass('bg-danger');
							}
						});
					}
				},
				error: function (resp) {
					console.log(resp)
					console.log("aaksk")
				},
			});
		}
	</script>
@endpush
