@extends('layouts.modal')

@section('action', route($routes . '.detailStore', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <input type="hidden" name="pemeliharaan_id" id ="pemeliharaan" value="{{ $record->id }}">
    <input type="hidden" name="lokasi" id="lokasi" value="{{ $record->departemen_id }}">
    <div class="row">
        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Nama Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="kib_id" id="kib_id" class="form-control base-plugin--select2-ajax kib_id"
                    data-url="{{ rut('ajax.selectAsetKib', ['lokasi,pem']) }}"
                    data-url-origin="{{ rut('ajax.selectAsetKib', ['lokasi,pem']) }}"
                        {{-- data-url="{{ route('ajax.selectAsetKib', 'lokasi') }}" --}}
                        placeholder="{{ __('Pilih Salah Satu') }}">
                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                        
                    </select>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Kondisi Awal Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="first_condition" placeholder="{{ __('Kondisi Awal Aset') }}"></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Kondisi Setelah Pemeliharaan Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="latest_condition" placeholder="{{ __('Kondisi Setelah Pemeliharaan Aset') }}"></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Tindakan Pemeliharaan Dilakukan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <textarea class="form-control" name="maintenance_action" placeholder="{{ __('Tindakan Pemeliharaan Dilakukan') }}"></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">  
                <div class="col-sm-12 col-md-5 pr-0">
                    <label class="col-form-label">{{ __('Penanggung Jawab Pemeliharaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-sm-12 col-md-7 parent-group">
                    <select name="repair_officer" class="form-control base-plugin--select2-ajax"
                            data-url="{{ route('ajax.selectUser', ['search' => 'sarpras']) }}"
                            data-url-origin="{{ route('ajax.selectUser', ['search' => 'sarpras']) }}"
                            placeholder="{{ __('Pilih Petugas') }}" required>
                        <option value="">{{ __('Pilih Petugas') }}</option>

                    </select>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    

    $(function () {
    var $loc;
    var objectId = $('select.kib_id');
    
    
    $loc = document.getElementById('lokasi');
    $pem = document.getElementById('pemeliharaan');
    
    if ($loc) {
        handleDepartemenChange($loc,$pem,objectId);
    }
    
    
    function handleDepartemenChange(loc,pem, objectId) {

        var urlOrigin = objectId.data('url-origin');
        var urlParam = $.param({ lokasi: loc.value, pem :pem.value });
        objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        console.log(decodeURIComponent(decodeURIComponent(urlOrigin + '?' + urlParam)));
        objectId.val(null).prop('disabled', false);
        BasePlugin.initSelect2();
    }


});

</script>
    <script src="{{ '/assets/js/global.js' }}"></script>
    <script>
	    $('.modal-dialog-right-bottom').removeClass('modal-lg').addClass('modal-md');
    </script>
@endpush