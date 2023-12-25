@extends('layouts.pageSubmit')

@section('action', route($routes . '.update', $record->id))

@section('card-body')
@section('page-content')
    @method('PATCH')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">@yield('card-title', $title)</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>
                
                <div class="card-body">
                    @include('globals.notes')
                    @csrf
                    @include('pengajuan.perencanaan-aset.includes.header')
                    <div class="row">
                        <div class="col-md-12">
                            {{-- <div class="form-group row">
                                <label class="col-md-2 col-form-label">{{ __('Diteruskan Kepada Yth') }}</label>
                                <div class="col-md-10 parent-group">
                                    <select name="cc[]" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ route('ajax.selectUser', ['search' => 'level_department']) }}" multiple
                                        placeholder="{{ __('Pilih Beberapa') }}" disabled>
                                        <option value="">{{ __('Pilih Beberapa') }}</option>
                                        @foreach ($record->cc as $user)
                                            <option value="{{ $user->id }}" selected>
                                                {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Keterangan Tambahan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea name="note_disposisi" class="base-plugin--summernote" placeholder="{{ __('Keeterangan Tambahan') }}" data-height="200" disabled>{!! $record->note  !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 2 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Lampiran Daftar Kebutuhan</h3>
                </div>
                <div class="card-body p-8">
                @include('pengajuan.perencanaan-aset.detail.index')
                </div>
            </div>
        </div>
    </div>

    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Surat Pengajuan Pembelian</h3>
                </div>
                <div class="card-body p-8">
                    <div class="row">
                        <div class="col-sm-12">
                        <div class="form-group row">
                                <label class="col-md-2 col-form-label">{{ __('Kepada Yth') }}</label>
                                <div class="col-md-10 parent-group">
                                    <select name="user_kepada" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ route('ajax.selectUser', ['search' => 'level_bod']) }}"
                                        placeholder="{{ __('Pilih Beberapa') }}" disabled>
                                        <option value="">{{ __('Pilih Beberapa') }}</option>
                                        @if ($user = $record->to_user)
                                            <option value="{{ $user->id }}" selected>
                                                {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Pembukaan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea name="sentence_start" value="{{ $record->sentence_start }}" class="base-plugin--summernote" placeholder="{{ __('Pembukaan') }}" data-height="200" disabled>
                                    {!! $record->sentence_start !!}
                                    </textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Penutupan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea name="sentence_end" value="{{ $record->sentence_end }}" class="base-plugin--summernote" placeholder="{{ __('Penutupan') }}" data-height="200" disabled>{!! $record->sentence_end !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                @if (request()->route()->getName() == $routes.'.approval')
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        @if ($record->checkAction('approval', $perms) || auth()->user()->position->location->level=='department')
                            @include('layouts.forms.btnBack')
                            @include('layouts.forms.btnDropdownApproval')
                            @include('layouts.forms.modalReject')
                        @endif
                        {{-- @if (auth()->user()->position->location->level=='department')
                            @include('layouts.forms.btnBack')
                             @include('layouts.forms.btnDropdownApproval')
                            @include('layouts.forms.modalReject')
                        @endif --}}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
{{-- @show --}}
@endsection
