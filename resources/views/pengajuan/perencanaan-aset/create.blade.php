@extends('layouts.modal')

@section('action', route($routes . '.store'))

@section('modal-body')
    @method('POST')
    {{-- @csrf --}}
    {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Failed!</strong> Terdapat beberapa kesalahan saat memproses formulir:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    <input type="hidden" name="is_submit" value="0">
    {{-- @dump($errors) --}}
    <div class="row">

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Unit Kerja') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    @if($departemen->location->level == 'department')
                        <input type="hidden" id="departemen_idt" value="{{ $departemen->location->id }}">
                        <select name="struct_id" class="form-control base-plugin--select2-ajax struct_id"
                            data-url="{{ rut('ajax.selectStruct', ['departemen_id']) }}"
                            data-url-origin="{{ rut('ajax.selectStruct', ['departemen_id']) }}"
                            placeholder="{{ __('Pilih Salah Satu') }}" required>
                            <option value="">{{ __('Pilih Struktur Organisasi') }}</option>
                        </select>   
                    @endif
                    @if($departemen->location->name == 'Sub Bagian Program Perencanaan dan Pelaporan')
                        <select name="struct_id" class="form-control base-plugin--select2-ajax"
                            data-url="{{ route('ajax.selectStruct', 'object_aset') }}"
                            data-placeholder="{{ __('Unit Kerja') }}">
                            <option value="">{{ __('Pilih Struktur Organisasi') }}</option>
                        </select>  
                    @endif
                    @if($departemen->location->level == 'subdepartmen' || $departemen->location->name != 'Sub Bagian Program Perencanaan dan Pelaporan' && $departemen->location->level != 'department')
                        <select class="form-control"  name="struct_id">
                            <option value="{{ $departemen->location->id}}" selected> {{ $departemen->location->name }} </option>
                        </select>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <div class="col-2 pr-0 mt-2">
                    <label class="col-form-label">{{ __('Perihal') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control @error('regarding') is-invalid @enderror" name="regarding" placeholder="{{ __('Perihal') }}">
                    @error('regarding')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>


            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tgl Pengajuan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input name="date" class="form-control base-plugin--datepicker"
                        placeholder="{{ __('Tgl Surat') }}" value="{{ now()->format('Y-m-d') }}" data-date-end-date="{{ now() }}" disabled>
                </div>
            </div>


            <div class="form-group row">
                <div class="col-2 pr-0">
                    <label class="col-form-label">{{ __('Tahun Perencanaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" type="number" min="{{ now()->format('Y') }}" max="2100" name="procurement_year" placeholder="{{ __('Tahun Perencanaan') }}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label">{{ __('Lampiran') }}</label>
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
                    <div class="form-text text-muted">*Maksimal 20MB</div>
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



{{-- <div class="col-sm-6">
    <div class="form-group row">
        {{-- <div class="col-4">
            <label class="col-form-label">{{ __('No Surat') }}</label>
        </div>
        <div class="col-8 parent-group">
            <input class="form-control" name="code" id="code" placeholder="{{ __('No Surat') }}" disabled>
            @error('code')
                <div class="invalid-feedback" style="display:block;">
                    {{  $message }}
                </div>
            @enderror
        </div> --}}
    {{-- </div>
</div>  --}}
{{-- <div class="col-sm-6">
    <div class="form-group row">
        <div class="col-4">
            <label class="col-form-label">{{ __('Tgl Pengajuan') }}</label>
        </div>
        <div class="col-8 parent-group">
            <input name="date" class="form-control base-plugin--datepicker"
                placeholder="{{ __('Tgl Surat') }}" data-date-end-date="{{ now() }}">
        </div>
    </div>
</div> --}}
