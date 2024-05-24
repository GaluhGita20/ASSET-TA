@extends('layouts.modal')


@section('action', route($routes . '.updateSummary', $record->id))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('No Surat') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="perbaikan_id" id="perbaikan_id" class="form-control base-plugin--select2-ajax trans_perbaikan_id"
                                        data-url="{{ route('ajax.selectPerbaikan') }}"
                                        data-placeholder="{{ __('Pilih Usulan Perbaikan') }}">
                                        @if ($record->codes)
                                        <option value="{{ $record->codes->id }}" selected>
                                            {{ $record->codes->code }}
                                        </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div> 

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tipe Perbaikan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                @php
                                    $menu = \App\Models\Perbaikan\UsulanSperpat::where('trans_perbaikan_id', $record->id)->count('id');
                                    $flag = $menu;
                                @endphp
                                @if($flag == 0)
                                    <div class="col-sm-10 col-form-label">
                                        <select class="form-control" name="repair_type" data-placeholder="Tipe Perbaikan">
                                            <option disabed value="">Jenis Perbaikan</option>
                                            <option value="sperpat" {{ $record->repair_type =='sperpat' ? 'selected' : '-' }}>Pembelian Sperpat</option>
                                            <option value="vendor"  {{ $record->repair_type =='vendor' ? 'selected' : '-' }} >Sewa Vendor</option>
                                        </select>
                                    </div>
                                @else
                                    <div class="col-sm-10 col-form-label">
                                        <select class="form-control" name="repair_type" data-placeholder="Tipe Perbaikan" disabled>
                                            <option disabed value="">Jenis Perbaikan</option>
                                            <option value="sperpat" {{ $record->repair_type =='sperpat' ? 'selected' : '-' }}>Pembelian Sperpat</option>
                                            <option value="vendor"  {{ $record->repair_type =='vendor' ? 'selected' : '-' }} >Sewa Vendor</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Tanggal Pengajuan') }}<span style=" color: red;margin-left: 5px;">*</span></label>

                                <div class="col-sm-10 col-form-label">
                                    <input name="submission_date" class="form-control base-plugin--datepicker"
                                        placeholder="{{ __('Tanggal Pengajuan') }}" value="{{ now()->format('Y-m-d') }}" data-date-end-date="{{ now() }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ __('Vendor') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                                <div class="col-sm-10 col-form-label">
                                    <select name="vendor_id" class="form-control base-plugin--select2-ajax vendor_id"
                                            data-url="{{ rut('ajax.selectVendor', [
                                                'search'=>'all'
                                            ]) }}"
                                            data-url-origin="{{ rut('ajax.selectVendor', [
                                                'search'=>'all'
                                            ]) }}"
                                            placeholder="{{ __('Pilih Salah Satu') }}" required>
                                            <option value="">{{ __('Pilih Salah Satu') }}</option>
                                            @if ($record->vendor_id)
                                            <option value="{{ $record->vendors->id }}" selected>
                                                {{ $record->vendors->name }}
                                            </option>
                                            @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                 
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
    <!-- end of header -->
@push('scripts')
    <script>
        $('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
    </script>
@endpush


