@extends('layouts.modal')

@section('action', route($routes . '.updateHarga', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <div class="row">

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Nomor Surat') }}</label>
                </div>

                <div class="col-10 parent-group">
                    <select name="perencanaan_id" class="form-control base-plugin--select2-ajax perencanaan_id"
                        data-url="{{ rut('ajax.selectCodePerencanaan') }}"
                        data-url-origin="{{ rut('ajax.selectCodePerencanaan') }}"
                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                        <option value="">{{ __('Pilih Nomor Surat Perencanaan') }}</option>
                        @if (!empty($record->detailUsulan->perencanaan_id))
                            <option value="{{ $record->detailUsulan->perencanaan_id }}" selected>{{ $record->detailUsulan->perencanaan->code}}</option>
                        @endif
                    </select>   
                </div>
            </div>

            <div class="form-group row">
                
                <div class="col-2">
                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                </div>

                <div class="col-10 parent-group">
                    <select name="usulan_id" class="form-control base-plugin--select2-ajax usulan_id"
                        data-url="{{ rut('ajax.selectUsulanDetail') }}"
                        data-url-origin="{{ rut('ajax.selectUsulanDetail') }}"
                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                        <option value="">{{ __('Pilih Detail Aset') }}</option>
                        @if (!empty($record->detailUsulan->asetd->name))
                            <option value="{{ $record->usulan_id }}" selected>{{ $record->detailUsulan->asetd->name}}</option>
                        @endif  
                    </select> 
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <textarea class="form-control" name="spesifikasi" id="spesifikasi" value ="{{$record->detailUsulan->desc_spesification}}" disabled>{{$record->detailUsulan->desc_spesification}}</textarea>
                    <span style="font-size: 11px">{{ __('*Contoh Bahan: Kaca
                        Ukuran: 100 Ml
                        Panjang: 20 cm
                        Lebar : 20 cm
                        Frekuensi: 100Hz') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Jumlah Disetujui') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input name="jumlah_disetujui" id="jumlah_disetujui" class="form-control base-plugin--datepicker"
                        placeholder="{{ __('Tanggal Pengajuan Surat') }}"  value ="{{$record->detailUsulan->qty_agree}}" disabled>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Harga Unit Aset') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <div class="input-group">
                        
                        <input type="text" min=0 id="pagu_unit" name="pagu_unit" class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Harga Unit Aset') }}" value ="{{$record->detailUsulan->HPS_unit_cost}}" oninput="updateTotal()">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Rupiah
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Harga Total Aset') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <div class="input-group">
                        <input type="text" id="pagu_total" name="pagu_total" min=0 class="form-control base-plugin--inputmask_currency text-right"
                            placeholder="{{ __('Harga Total Aset') }}" value ="{{$record->detailUsulan->HPS_total_cost}}" disabled>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Rupiah
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tanggal Perubahan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input name="date" class="form-control base-plugin--datepicker"
                        placeholder="{{ __('Tanggal Pengajuan Surat') }}"  value="{{ $record->update_date }}" data-date-end-date="{{ now() }}" disabled>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Catatan Perubahan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <textarea class="base-plugin--summernote" name="note" placeholder="{{ __('Catatan Alasan Penolakan') }}" value="{{$record->note}}" disabled>{{$record->note}}</textarea>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 offset-md-4">
                        <span style="font-size: 11px">{{ __('*Tambahkan Juga Spesifikasi Aset Yang Tersedia dan Harga Yang Tertera') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')


<script>
    $(function () {
        $('.content-page').on('change', 'select.perencanaan_id', function (e) {
            var me = $(this);
            if (me.val()) {
                var objectId = $('select.usulan_id');
                var urlOrigin = objectId.data('url-origin');
                var urlParam = $.param({usulan_id: me.val()});
                objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                objectId.val(null).prop('disabled', false);
            }
            BasePlugin.initSelect2();
        });
    });

    $(".usulan_id").on('change', function() {
        var me = $(this);

        console.log(me.val());

        $.ajax({
            type: 'POST',
            url: '/ajax/getUsulanAsetById',
            data: {
                _token: BaseUtil.getToken(),
                id: me.val(),
            },
            success: function(resp) {
                var spesifikasi = resp[0].desc_spesification;
                var jumlah_disetujui = resp[0].qty_agree;
                var pagu_unit = resp[0].HPS_unit_cost;
                var pagu_total = resp[0].HPS_total_cost; 

                $('#spesifikasi').val(spesifikasi);
                $('#jumlah_disetujui').val(jumlah_disetujui);
                $('#pagu_unit').val(pagu_unit);
                $('#pagu_total').val(pagu_total);

                console.log(resp);
            },
            error: function(resp) {
                console.log(resp)
                console.log('error')
            },
        });
    });

    function updateTotal() {

        var jumlah = document.getElementById('jumlah_disetujui').value;
        var harga = document.getElementById('pagu_unit').value;
        var total_cost = document.getElementById('pagu_total').value;
        // var pagus =  document.getElementById('pagus').value;

        jumlah= jumlah.replace(/[^0-9]/g, '');
        harga= harga.replace(/[^0-9]/g, '');
        total= total_cost.replace(/[^0-9]/g, '');

        jumlah = parseInt(jumlah);
        harga = parseInt(harga);
        total = parseInt(total);

        if (harga > 0) {
            console.log('a');
            hasil = jumlah * harga;
            document.getElementById('pagu_total').value = parseInt(hasil);
        }else {
            document.getElementById('pagu_total').value = parseInt(0);
        }
    }

</script>

<script>
	$('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
</script>
@endpush