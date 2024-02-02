@extends('layouts.modal')

@section('action', route($routes . '.updateSummary', $record->id))

@section('modal-body')
    @method('POST')
    <input type="hidden" name="is_submit" value="0">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('No Surat') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control" name="code" placeholder="{{ __('No Surat') }}" value="{{ $record->code }}" readonly>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Unit Departemen') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <select class="form-control" name="departemen_id">
                        @if(!empty($record->departemen_id))
                            <option value="{{ $record->deps->id}}" selected> {{ $record->deps->name }} </option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-2">
                    <label class="col-form-label">{{ __('Tanggal Pemeliharaan') }}</label>
                </div>
                <div class="col-10 parent-group">
                    <input name="dates" class="form-control base-plugin--datepicker"
                        placeholder="{{ __('Tanggal Pemeliharaan') }}"  value="{{ $record->dates->format('d/m/Y') }}" max="{{ now()->addMonths(1) }}" >
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
	$('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
</script>
@endpush

@push('script')


@endpush

