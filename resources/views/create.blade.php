@extends('layouts.pageSubmit')

@section('action', route($routes . '.store'))

@section('card-body')
@section('page-content')
    @method('POST')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    {{-- <h3 class="card-title">@yield('card-title', $title)</h3> --}}
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>
                <div class="card-body">
                    {{-- @include('globals.notes')
                    @csrf --}}
                    <div class="row">
                        <div class="col-10 parent-group">
                            <input type="hidden" class="form-control" name="usulan_id[]" value="{{ $usulan_id }}">
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Nama Transaksi') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input type="text" class="form-control" name="trans_name" placeholder="{{ __('Nama Transaksi') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nama Vendor') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <select name="ref_vendor" class="form-control base-plugin--select2-ajax ref_vendor"
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
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Nomor Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="text" class="form-control" name="no_spk" placeholder="{{ __('Nomor Kontrak') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Mulai Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="date" class="form-control base-plugin--datepicker" name="spk_start_date" placeholder="{{ __('Tanggal Mulai Kontrak') }}" data-date-end-date="{{ now() }}" >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <div class="col-4 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Selesai Kontrak') }}</label>
                                </div>
                                <div class="col-8 parent-group">
                                    <input type="date" class="form-control base-plugin--datepicker" name="spk_end_date" placeholder="{{ __('Tanggal Selesai Kontrak') }}">
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Jenis Pengadaan') }}</label>
                                </div>
                                <div class="col-md-10 parent-group">
                                    <select name="ref_jenis_pengadaan" class="form-control base-plugin--select2-ajax ref_jenis_pengadaan"
                                        data-url="{{ rut('ajax.selectJenisPengadaan', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectJenisPengadaan', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-md-10 parent-group">
                                    <select id="aset_id" class="form-control base-plugin--select2-ajax aset_id"
                                        data-url="{{ rut('ajax.selectDetailUsulan', [
                                            'search'=>'all'
                                        ]) }}"
                                        data-url-origin="{{ rut('ajax.selectDetailUsulan', [
                                            'search'=>'all'
                                        ]) }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" required>
                                        <option value="">{{ __('Pilih Salah Satu') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Detail Pembelian Unit') }}</label>
                                </div>
                                <div class="col-md-10 parent-group">
                                    <select name="detailPembelian[]" id = "detailPembelian" class="form-control base-plugin--select2-ajax detailPembelian"
                                        data-url="{{ rut('ajax.selectAsetBeli', [
                                            'aset_id'
                                        ]) }}" 
                                        data-url-origin="{{ rut('ajax.selectAsetBeli', [
                                            'aset_id'
                                        ]) }}" multiple
                                        placeholder="{{ __('Pilih Beberapa') }}" required
                                        >
                                        <option value="">{{ __('Pilih Beberapa') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        @include('layouts.forms.btnBack')
                        @include('layouts.forms.btnSubmitModal')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->
@endsection

@push('scripts')

	<script>
		$(function () {
			$('.content-page').on('change', 'select.aset_id', function (e) {
				var me = $(this);
				if (me.val()) {
					var objectId = $('select.detailPembelian');
					var urlOrigin = objectId.data('url-origin');
					var urlParam = $.param({aset_id: me.val()});
					objectId.data('url', decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
					console.log(decodeURIComponent(decodeURIComponent(urlOrigin+'?'+urlParam)));
                    objectId.val(null).prop('disabled', false);
				}
				BasePlugin.initSelect2();
			});
		});
	</script>
    
@endpush
















                        {{-- @extends('transaksi.waiting-purchase.index') --}}




                    
                            {{-- <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Jenis Usulan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select class="form-control" name="is_repair" data-placeholder="is_repair" disabled>
                                        <option disabed value="">Jenis Pengadaan</option>
                                        @if(auth()->user()->roles =='Sarpras')
                                            <option value="yes" {{ $record->is_repair =='yes' ? 'selected':'-' }}>Pengajuan Perbaikan Aset</option>
                                        @endif
                                        <option value="no" {{ $record->is_repair =='no' ? 'selected':'-' }}>Pengajuan Pembelian Aset</option>
                                    </select>
                                </div>
                            </div> --}}
                    
                            {{-- <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Perihal') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input class="form-control" name="regarding" placeholder="{{ __('Perihal') }}" value="{{ $record->regarding }}" disabled>
                                </div>
                            </div> --}}
                    
                            {{-- <div class="form-group row">
                                <label class="col-2 col-form-label">{{ __('Lampiran') }}</label>
                                <div class="col-10 parent-group">
                                    @foreach ($record->files as $file)
                                    <div class="progress-container w-100" data-uid="{{ $file->id }}">
                                        <div class="alert alert-custom alert-light fade show py-2 px-3 mb-0 mt-2 success-uploaded" role="alert">
                                            <div class="alert-icon">
                                                <i class="{{ $file->file_icon }}"></i>
                                            </div>
                                            <div class="alert-text text-left">
                                                <input type="hidden" name="uploads[files_ids][]" value="{{ $file->id }}">
                                                <div>Uploaded File:</div>
                                                <a href="{{ $file->file_url }}" target="_blank" class="text-primary">
                                                    {{ $file->file_name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div> --}}
                        {{-- </div> --}}