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
                    <label class="col-form-label">{{ __('Unit Departemen') }}<span style=" color: red;margin-left: 5px;">*</span></label>
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
                    <label class="col-form-label">{{ __('Tanggal Pemeliharaan') }}<span style=" color: red;margin-left: 5px;">*</span></label>
                </div>
                <div class="col-10 parent-group">
                    <input class="form-control base-plugin--datepicker" name="maintenance_date" placeholder="{{ __('Tanggal Pemeliharaan') }}" value="{{ $record->maintenance_date->format('d/m/Y') }}">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
	$('.modal-dialog-right-bottom').removeClass('modal-md').addClass('modal-lg');
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Mendapatkan tanggal saat ini
    var today = new Date();
    var currentYear = today.getFullYear();
    var currentMonth = today.getMonth() + 1; // Perhatikan bahwa JavaScript menghitung bulan dimulai dari 0 (Januari adalah bulan 0)

    // Mendefinisikan tanggal awal dan akhir untuk rentang tanggal
    var minDate = currentYear + '-' + currentMonth + '-01';
    var maxDate = currentYear + '-' + currentMonth + '-' + new Date(currentYear, currentMonth, 0).getDate();

    // Menginisialisasi flatpickr dengan rentang tanggal dinamis
    flatpickr("#datepickered", {
        dateFormat: "Y-m-d",
        defaultDate: "today",
        minDate: "2024-03-01",
        maxDate: "2024-03-31"
    });
</script>

@endpush

@push('script')


@endpush

