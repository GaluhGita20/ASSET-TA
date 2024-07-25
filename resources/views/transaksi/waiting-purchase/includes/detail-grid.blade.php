<table id="dataFilters" class="width-full">
    <div class="alert alert-custom alert-light-primary fade show py-4" role="alert">
        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
        <div class="alert-text text-primary">
            <div class="text-bold">{{ __('Informasi') }}:</div>
            <div class="mb-10px" style="white-space: pre-wrap;">Silahkan Checklist Aset dengan Sumber Pendanaan Yang Sama Untuk Dibuatkan Laporan BAST (Berita ACara Serah Terima Aset), Gunakan Filter Untuk Memudahkan Menemukan Aset Sejenis (Nama Aset Yang Sama)</div>
        </div>
    </div>

    <tbody>
        <tr>
            <td class="pb-2 valign-top td-filter-reset width-80px">
                <div class="label-filter mr-1">
                    <button class="btn btn-secondary btn-icon width-full filter button" data-toggle="tooltip"
                        data-original-title="Filter"><i class="fas fa-filter"></i></button>
                </div>
            </td>
            <td>
                <div class="row">
                    <div class="col-12 col-sm-6 col-xl-3 pb-2">
                        <input type="text" class="form-control filter-control" data-post="jenis_aset"
                            placeholder="{{ __('Nama Aset') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 pb-2">
                        <select class="form-control base-plugin--select2-ajax filter-control"
                            data-post="procurement_year"
                            data-placeholder="{{ __('Periode Usulan') }}">
                            <option value="" selected>{{ __('Periode Usulan') }}</option>
                            @php
                                $startYear = 2020;
                                $currentYear = date('Y');
                                $endYear = $currentYear + 5;
                            @endphp
                            @for ($year = $startYear; $year <= $endYear; $year++)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

            </td>

            {{-- <td>tes</td> --}}
            @if(auth()->user()->hasRole('PPK') )
                <td class="td-btn-create width-300px text-right">
                    @if (request()->route()->getName() != $routes . '.show')
                            @include('layouts.forms.btnSubmitPage')
                    @endif
                </td>
            @endif
            
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

@push('script')

@endpush


