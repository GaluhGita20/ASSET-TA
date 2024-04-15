<div class="col-12">
	<div class="row card-progress-wrapper" data-url="{{ rut($routes.'.progressAset') }}">
		@php
			$cards = collect(json_decode(json_encode($progress)));
			$length = count($cards);
		@endphp
		@foreach ($cards as $card)
			<div class="col-xl-{{ $length > 3 ? '4' : '12' }} col-md-6 col-sm-12">
				<div class="card card-custom gutter-b card-stretch wave wave-{{ $card->color }}"
				data-name="{{ $card->name }}">
					<div class="card-body">
						<div class="d-flex flex-wrap align-items-center py-1">
							<div class="symbol symbol-40 symbol-light-{{ $card->color }} mr-5">
								<span class="symbol-label shadow">
									<i class="{{ $card->icon }} align-self-center text-{{ $card->color }} font-size-h5"></i>
								</span>
							</div>
							<div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
								<div class="text-dark font-weight-bolder font-size-h5">
									{{ __($card->title) }}
								</div>
								<div class="text-muted font-weight-bold font-size-lg">
									<div class="d-flex justify-content-between">
										<span class="text-nowrap">Active/Not Active</span>
										<span class="text-nowrap">
											<span class="actived">0</span>/<span class="not_actived">0</span>
										</span>
									</div>
								</div>
							</div>
							<div class="d-flex flex-column w-100 mt-5">
								<div class="text-dark mr-2 font-size-lg font-weight-bolder pb-3">
									<div class="d-flex justify-content-between">
										{{-- <span class="percent-text">0%</span> --}}
									</div>
								</div>
								{{-- <div class="progress progress-xs w-100">
									<div class="progress-bar percent-bar"
										role="progressbar"
										style="width: 0%;"></div>
								</div> --}}
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
    				// Ambil semua elemen card
					var cards = document.querySelectorAll('.card[data-name]');

					// Loop melalui setiap elemen card
					cards.forEach(function(card) {
						// Ambil nilai data-name dari elemen card saat ini
						var itemName = card.getAttribute('data-name');

						// Cari item yang sesuai dalam respons
						var item = resp.data.find(function(item) {
							return item.name === itemName;
						});

						// Periksa apakah item ditemukan dalam respons
						if (item) {
							// Perbarui nilai teks dalam elemen card
							card.querySelector('.actived').textContent = item.active;
							card.querySelector('.not_actived').textContent = item.not_active;
						} else {
							console.error("Data card dengan nama " + itemName + " tidak ditemukan dalam respons.");
						}
					});
				} else {
					console.error("Tidak ada data yang diterima atau data kosong.");
				}

				},
				error: function (resp) {
					console.log(resp);
				},
			});
		}
	</script>
@endpush
