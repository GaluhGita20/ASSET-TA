<div class="col-md-6">
	<div class="card card-custom card-stretch gutter-b chart-pelaporan3-wrapper">
		<div class="card-header h-auto py-3">
			<div class="card-title">
				<h3 class="card-label">
					<span class="d-block text-dark font-weight-bolder">{{ __('Gedung Bangunan') }}</span>
				</h3>
			</div>
			<div class="card-toolbar" style="max-width: 500px;">
				<form id="filter-chart-pelaporan3"
					action="{{ route($routes.'.chartAsetKIBC') }}"
					class="form-inline"
					role="form">
					<div class="row d-flex justify-content-end">
						<div class="col-md-6 col-sm-12">
							<div class="input-daterange input-group">
								<div class="input-group-append" data-toggle="tooltip" title="Filter">
									<span class="input-group-text">
										<i class="fa fa-filter"></i>
									</span>
								</div>
								<input type="text" 
									class="form-control stage_year" 
									name="stage_year" 
									value="{{ request()->stage_year ?? date('Y') }}">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="card-body">
			<div class="chart-wrapper">
				<div id="chart-pelaporan3">
					<div class="d-flex h-100">
						<div class="spinners m-auto my-auto">
							<div class="spinner-grow text-success" role="status">
								<span class="sr-only">Loading...</span>
							</div>
							<div class="spinner-grow text-danger" role="status">
								<span class="sr-only">Loading...</span>
							</div>
							<div class="spinner-grow text-warning" role="status">
								<span class="sr-only">Loading...</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@push('styles')
	<style>
		.chart-pelaporan-wrapper3 .apexcharts-menu-item.exportSVG,
		.chart-pelaporan-wrappe3 .apexcharts-menu-item.exportCSV {
			display: none;
		}
		.chart-pelaporan-wrapper3 .apexcharts-title-text {
			white-space: normal;
		}
	</style>
@endpush

@push('scripts')
	<script>
		$(function() {
		    iniFilterchartTerminu();
		    drawchartTerminu();
		});

		var iniFilterchartTerminu = function () {
		    $('input.stage_year').datepicker({
	            format: "yyyy",
			    viewMode: "years",
			    minViewMode: "years",
		        orientation: "bottom auto",
		        autoclose:true,
	        })
	        .on('changeDate', function (selected) {
	        	drawchartTerminu();
			});
		}

		var drawchartTerminu = function () {
			var filter = $('#filter-chart-pelaporan3');

			$.ajax({
				url: filter.attr('action'),
				method: 'POST',
				data: {
					_token: BaseUtil.getToken(),
					stage_year: filter.find('.stage_year').val(),
				},
				success: function (resp) {
					// $('.chart-pelaporan-wrapper .chart-wrapper').find('#chart-pelaporan1').remove();
					$('.chart-pelaporan-wrapper3 .chart-wrapper3').find('#chart-pelaporan3').remove();
					$('.chart-pelaporan-wrapper3 .chart-wrapper3').html(`<div id="chart-pelaporan3"></div>`);
					renderchartTerminu(resp);
				},
				error: function (resp) {
					console.log(resp)
				}
			})
			;
		}


		var renderchartTerminu = function (options = {}) {
			var element = document.getElementById('chart-pelaporan3');

	        var defaultsOptions = {
	        	title: {
	        		text: options.title.text ?? '',
	        		align: 'center',
	        		style: {
						fontSize:  '18px',
						fontWeight:  '500',
					},
	        	},
	            series: options.series ?? [],
	            chart: {
	                type: 'bar',
	                height: '400px',
	                stacked: true,
	                toolbar: {
	                    show: true,
	                    tools: {
							download: true,
							selection: false,
							zoom: false,
							zoomin: false,
							zoomout: false,
							pan: false,
							reset: false,
							customIcons: []
						},
	                }
	            },
	            plotOptions: {
	                bar: {
	                    horizontal: false,
	                    columnWidth: ['30%'],
	                    endingShape: 'rounded'
	                },
	            },
		        legend: {
		        	position: 'top',
		        	offsetY: 2
		        },
	            dataLabels: {
	                enabled: false
	            },
	            xaxis: {
	                categories: options.xaxis.categories ?? [],
	                axisBorder: {
	                    show: false,
	                },
	                axisTicks: {
	                    show: false
	                },
	                labels: {
	                    style: {
	                        colors: KTApp.getSettings()['colors']['gray']['gray-500'],
	                        fontSize: '12px',
	                        fontFamily: KTApp.getSettings()['font-family']
	                    }
	                }
	            },
	            yaxis: {
	                labels: {
	                    style: {
	                        colors: KTApp.getSettings()['colors']['gray']['gray-500'],
	                        fontSize: '12px',
	                        fontFamily: KTApp.getSettings()['font-family']
	                    }
	                }
	            },
	            fill: {
	                opacity: [1, 1, 1, 1, 1, 1],
					gradient: {
						inverseColors: false,
						shade: 'light',
						type: "vertical",
					}
	            },
	            tooltip: {
	                style: {
	                    fontSize: '12px',
	                    fontFamily: KTApp.getSettings()['font-family']
	                },
	                y: {
	                    formatter: function(val) {
	                        return val
	                    }
	                }
	            },
	            colors: options.colors ?? [
	            	KTApp.getSettings()['colors']['theme']['base']['secondary'],
	            	KTApp.getSettings()['colors']['theme']['base']['danger'],
	            	KTApp.getSettings()['colors']['theme']['light']['warning'],
	            	KTApp.getSettings()['colors']['theme']['base']['warning'],
	            	KTApp.getSettings()['colors']['theme']['light']['success'],
	            	KTApp.getSettings()['colors']['theme']['base']['success'],
	            ],
	            grid: {
	                borderColor: KTApp.getSettings()['colors']['gray']['gray-200'],
	                strokeDashArray: 4,
	                yaxis: {
	                    lines: {
	                        show: true
	                    }
	                }
	            },
	            noData: {
	            	text: 'Loading...'
	            }
	        };


	        var chart = new ApexCharts(element, defaultsOptions);
	        chart.render();

			
		}
	</script>

@endpush
