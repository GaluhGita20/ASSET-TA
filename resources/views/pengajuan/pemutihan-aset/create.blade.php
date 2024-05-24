@extends('layouts.modal')

@section('action', route($routes . '.store'))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    <input type="hidden" name="is_submit" value="0">
    {{-- @dump($errors) --}}
    <div class="row">

        <div class="col-sm-12">

            <div class="card card-custom">

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nama Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <select name="kib_id" id="kib_id" class="form-control base-plugin--select2-ajax kib_id"
                                    data-url="{{ route('ajax.selectKib') }}"
                                        data-placeholder="{{ __('Pilih Aset') }}">
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- yang ditampilkan dilistt filter yaitu nama aset (no seri ) jika no seri kosong maka qty dapat muncul lebih dari 1  --}}
                        
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Merek') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" id="tempMerk" class="form-control" placeholder="{{ __('Merek') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Rangka') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" id="tempNoFrame" class="form-control" placeholder="{{ __('No Frame') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Type Aset') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" id="type" class="form-control" placeholder="{{ __('Type') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Nomor Pabrik') }}</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" id="tempNoFactory" class="form-control" placeholder="{{ __('No Factory') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="number" class="form-control" id="qty" name="qty" placeholder="{{ __('Jumlah Aset') }}" min="1">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

     <!-- end of header -->

    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pemutihan Aset</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Tanggal Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input class="form-control base-plugin--datepicker" name="submission_date" placeholder="{{ __('Tanggal Pemutihan') }}">
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Jenis Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <select name="clean_type" class="form-control base-plugin--select2-ajax ref_jenis_pemutihan"
                                        data-url="{{ rut('ajax.selectJenisPemutihan', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectJenisPemutihan', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Target Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="target" placeholder="Target Pemutihan">
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Lokasi Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" class="form-control" name="location" placeholder="Lokasi Pemutihan">
                                </div>
                            </div>
                        </div>
    
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">{{ __('Pendapatan Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-8 parent-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control base-plugin--inputmask_currency text-right" id="valued" name="valued" oninput="updateTotal()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">  
                                <label class="col-sm-4 col-form-label">{{ __('Penanggung Jawab Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-8 parent-group">
                                    <select name="pic" class="form-control base-plugin--select2-ajax"
                                            data-url="{{ route('ajax.selectUser', ['search' => 'BPKAD']) }}"
                                            data-url-origin="{{ route('ajax.selectUser', ['search' => 'BPKAD']) }}"
                                            placeholder="{{ __('Pilih Petugas') }}" required>
                                        <option value="">{{ __('Pilih Petugas') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>    

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Bukti Pemutihan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-10 parent-group">
                                    <div class="custom-file">
                                        <input type="hidden"
                                            name="uploads[uploaded]"
                                            class="uploaded"
                                            value="0">
                                        <input type="file" multiple
                                            class="custom-file-input base-form--save-temp-files"
                                            data-name="uploads"
                                            data-container="parent-group"
                                            data-max-size="30024"
                                            data-max-file="100"
                                            accept="*">
                                        <label class="custom-file-label" for="file">Choose File</label>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')



<script>
    
        function updateTotal() {
            var valued = document.getElementById('valued').value;

            valued= valued.replace(/[^0-9]/g, '');

            valued = parseInt(valued);
        }

        $("#kib_id").on('change', function() {
            var me = $(this);

            console.log(me.val());

            $.ajax({
                type: 'POST',
                url: '/ajax/getKibById',
                data: {
                    _token: BaseUtil.getToken(),
                    id: me.val(),
                },
                success: function(resp) {
                    var coa = resp[0].coa_id;
                    var merk = resp[0].merek_type_item;
                    var no_frame = resp[0].no_frame;
                    var no_factory = resp[0].no_factory_item;
                    var type = resp[0].type;

                    $('#tempName').val(coa);
                    $('#tempNoFrame').val(no_frame);
                    $('#tempMerk').val(merk);
                    $('#tempNoFactory').val(no_factory);
                    $('#type').val(type);
                    if (no_factory !== null || no_frame !== null) {
                        $('#qty').val(1);
                    }

                    console.log(resp);
                },
                error: function(resp) {
                    console.log(resp)
                    console.log('error')
                },
            });
        });
</script>

<script>
	$('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
</script>
@endpush

