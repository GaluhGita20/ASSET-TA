<table id="dataFilters" class="width-full">
    <tbody>
        <tr>
            <td class="valign-top td-filter-reset width-80px pb-2">
                <div class="reset-filter hide mr-1">
                    <button class="btn btn-secondary btn-icon width-full reset button" data-toggle="tooltip"
                        data-original-title="Reset Filter"><i class="fas fa-sync"></i></button>
                </div>
                <div class="label-filter mr-1">
                    <button class="btn btn-secondary btn-icon width-full button filter" data-toggle="tooltip"
                        data-original-title="Filter"><i class="fas fa-filter"></i></button>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
                        <input type="text" class="form-control filter-control" data-post="no_spk" placeholder="{{ __('No SPK') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
                        <select class="form-control filter-control base-plugin--select2-ajax" name="ref_vendor" data-url="{{ route('ajax.selectVendor', 'all') }}"
                            data-placeholder="{{ __('Nama Vendor') }}" data-post="ref_vendor">
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 pb-2 mr-n6">
                        <div class="input-group">
                            <input name="spk_start_date"
                                class="form-control base-plugin--datepicker spk_start_date"
                                placeholder="{{ __('Mulai') }}"
                                data-orientation="bottom"
                                data-post="spk_start_date"
                                >
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-ellipsis-h"></i>
                                </span>
                            </div>
                            <input name="spk_end_date"
                                class="form-control filter-control base-plugin--datepicker spk_end_date"
                                placeholder="{{ __('Selesai') }}"
                                data-orientation="bottom"
                                data-post="spk_end_date"
                                >
                        </div>
                    </div>
                </div>
            </td>

            <td class="td-btn-create width-300px text-right">
                @if (request()->route()->getName() != $routes . '.show')
                        @include('layouts.forms.btnSubmitPage')
                @endif
            </td>
            {{-- <td>tes</td> --}}
        </tr>
    </tbody>
</table>
<div class="table-responsive">
    @if (isset($tableStruct['datatable_1']))
        <table id="datatable_1" class="table-bordered is-datatable table" style="width: 100%;"
            data-url="{{ $tableStruct['url'] }}" data-paging="{{ $paging ?? true }}" data-info="{{ $info ?? true }}">
            <thead>
                <tr>
                    @foreach ($tableStruct['datatable_1'] as $struct)
                        <th class="v-middle text-center" data-columns-name="{{ $struct['name'] ?? '' }}"
                            data-columns-data="{{ $struct['data'] ?? '' }}"
                            data-columns-label="{{ $struct['label'] ?? '' }}"
                            data-columns-sortable="{{ $struct['sortable'] === true ? 'true' : 'false' }}"
                            data-columns-width="{{ $struct['width'] ?? '' }}"
                            data-columns-class-name="{{ $struct['className'] ?? '' }}"
                            style="{{ isset($struct['width']) ? 'width: ' . $struct['width'] . '; ' : '' }}">
                            {{ $struct['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            
            <tbody>
            </tbody>
        </table>
    @endif
</div>