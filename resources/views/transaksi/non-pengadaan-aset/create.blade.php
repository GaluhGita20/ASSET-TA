@extends('layouts.modal')

@section('action', route($routes . '.store'))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    <input type="hidden" name="is_submit" value="0">
    {{-- @dump($errors) --}}
    <div class="row">

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Nama Vendor') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <select name="vendor_id" class="form-control base-plugin--select2-ajax vendor_id"
                        data-url="{{ rut('ajax.selectVendor', [
                            'search'=>'all'
                        ]) }}"
                        data-url-origin="{{ rut('ajax.selectVendor', [
                            'search'=>'all'
                        ]) }}"
                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                    </select>   
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0 mt-2">
                    <label class="col-form-label">{{ __('Nama Transaksi') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" name="trans_name" placeholder="{{ __('Nama Transaksi') }}">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tanggal Penerimaan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control base-plugin--datepicker" name="receipt_date" placeholder="{{ __('Tanggal Penerimaan') }}">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Lokasi Penerimaan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" name="location_receipt" placeholder="{{ __('Lokasi Penerimaan') }}">
                </div>
            </div>
  
            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Jenis Penerimaan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <select name="source_acq" class="form-control">
                        <option value="Hibah">{{ __('Hibah') }}</option>
                        <option value="Sumbangan">{{ __('Sumbangan') }}</option>
                    </select>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')

<script>
    var nilai = document.getElementById('departemen_idt').value;
    // console.log(nilai);
    $(function () {
      //  $('.content-page').on('change', 'select.departemen_id', function (e) {
            var me = nilai;
            if (me != null) {
                console.log(me.val);
                var objectId = $('select.struct_id');
                var urlOrigin = objectId.data('url-origin');
                var urlParam = $.param({departemen_id: me});
                console.log(objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam))));
                objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                objectId.val(null).prop('disabled', false);
            }
            BasePlugin.initSelect2();
       // });
    });
</script>
	

<script>
	$('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
</script>
@endpush

@push('script')


@endpush
