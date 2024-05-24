<div class="row">
    <div class="col-sm-12">
    {{-- <div class="form-group row">
            <label class="col-md-2 col-form-label">{{ __('Kepada Yth') }}</label>
            <div class="col-md-10 parent-group">
                <select name="user_kepada" class="form-control base-plugin--select2-ajax"
                    data-url="{{ route('ajax.selectUser', ['search' => 'level_bod']) }}"
                    placeholder="{{ __('Pilih Beberapa') }}">
                    <option value="">{{ __('Pilih Beberapa') }}</option>
                    @if ($user = $record->to_user)
                        <option value="{{ $user->id }}" selected>
                            {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                        </option>
                    @endifaf
                </select>
            </div>
        </div> --}}
        <div class="form-group row">
            <div class="col-2 pr-0">
                <label class="col-form-label">{{ __('Pembukaan') }}</label>
            </div>
            <div class="col-10 parent-group">
                <textarea name="sentence_start" class="base-plugin--summernote" placeholder="{{ __('Pembukaan') }}" data-height="200">
                    @if(isset($record->sentence_start)) 
                        {!! $record->sentence_start !!}
                    @else
                        <p>Dengan hormat,<br>Dalam rangka meningkatkan kualitas mutu pelayanan di {{ Config::get("base.company.name") }}, kami mengajukan pembelian aset di {{ $record->struct->name }}. Adapun daftar kebutuhan aset yang kami ajukan terlampir.</p>
                    @endif
                </textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2 pr-0">
                <label class="col-form-label">{{ __('Penutupan') }}</label>
            </div>
            <div class="col-10 parent-group">
                <textarea name="sentence_end" class="base-plugin--summernote" placeholder="{{ __('Penutupan') }}" data-height="200">{!! isset($record->sentence_end) ? $record->sentence_end : "<p>Demikian surat pengajuan ini kami buat, besar harapan agar dapat ditindak lanjuti dan direalisasikan, atas perhatiannya kami sampaikan terima kasih.</p>"  !!}</textarea>
            </div>
        </div>
        
    </div>
</div>
