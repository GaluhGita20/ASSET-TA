
<table id="dataFilters" class="width-full">
    <div class="alert alert-custom alert-light-primary fade show py-4" role="alert">
        <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
        <div class="alert-text text-primary">
            <div class="text-bold">{{ __('Informasi') }}:</div>
            <div class="mb-10px" style="white-space: pre-wrap;">Silahkan Checklist 1 Aset Untuk Diinventarisasikan Sesusai Jenis Aset</div>
        </div>
    </div>
    <tbody>
        <tr>
            <td class="pb-2 valign-top td-filter-reset width-80px">
                {{-- <div class="reset-filter mr-1 hide">
                    <button class="btn btn-secondary btn-icon width-full reset button" data-toggle="tooltip"
                        data-original-title="Reset Filter"><i class="fas fa-sync"></i></button>
                </div> --}}
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
                </div>
            </td>

        @if (auth()->user()->checkPerms('registrasi.inventaris-aset.create'))
            <div style="display: flex; justify-content: flex-end; margin-right: 2px;">
                <input type="hidden" id="customValue" name="customValue" value="">

                <button type="submit" class="btn btn-primary base-form--submit-page" style="margin-right: 8px;" onclick="setCustomValue('A')">
                    <i class="fa fa-save mr-1" ></i>
                    {{ __('KIB A') }}
                </button>

                <button type="submit" class="btn btn-primary base-form--submit-page" style="margin-right: 8px;" onclick="setCustomValue('B')">
                    <i class="fa fa-save mr-1"></i>
                    {{ __('KIB B') }}
                </button>
                <button type="submit" class="btn btn-primary base-form--submit-page" style="margin-right: 8px;" onclick="setCustomValue('C')">
                    <i class="fa fa-save mr-1"></i>
                    {{ __('KIB C') }}
                </button>
                <button type="submit" class="btn btn-primary base-form--submit-page" style="margin-right: 8px;" onclick="setCustomValue('D')">
                    <i class="fa fa-save mr-1"></i>
                    {{ __('KIB D') }}
                </button>
                <button type="submit" class="btn btn-primary base-form--submit-page" style="margin-right: 8px;" onclick="setCustomValue('E')">
                    <i class="fa fa-save mr-1"></i>
                    {{ __('KIB E') }}
                </button>
                <button type="submit" class="btn btn-primary base-form--submit-page" onclick="setCustomValue('F')">
                    <i class="fa fa-save mr-1"></i>
                    {{ __('KIB F') }}
                </button>
            </div>
        @endif
                
            </td>
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

@push('scripts')

<script>
    // Function to set the custom value to the hidden input field
    function setCustomValue(value) {
        //alert('kalkalkkalaklak');
        $('#customValue').val(value);
    }
</script>
@endpush


