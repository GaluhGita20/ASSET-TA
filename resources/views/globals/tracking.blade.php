@extends('layouts.modal')

@if($record->status === 'waiting.approval.revisi')
@section('modal-title', __('Tracking Approval Revisi'))
@elseif($record->status === 'waiting.approval.upgrade')
@section('modal-title', __('Tracking Approval Upgrade'))
@elseif($module =='transaksi_pengadaan-aset' || $module == 'pemeliharaan-aset' || $module == 'transaksi_non-pengadaan-aset' || $module == 'pemutihan-aset' || $module == 'trans-sperpat')
@section('modal-title', __('Tracking Verify'))
@else
@section('modal-title', __('Tracking Approval'))
@endif

@section('modal-body')
    <div class="timeline timeline-2">
        <div class="timeline-bar"></div>
        @php
            $approvals = $record->approval($module)->get();
            // dd($module, json_decode($approvals));
        @endphp

        @if($module == 'transaksi_pengadaan-aset' || $module == 'pemeliharaan-aset' || $module == 'transaksi_non-pengadaan-aset' || $module == 'pemutihan-aset' || $module == 'trans-sperpat' )
            @forelse($approvals as $val)
            <div class="timeline-item d-flex align-items-start">
                <span class="timeline-badge bg-{{ $val->show_color }} mt-5px"></span>
                <div class="timeline-content d-flex align-items-start justify-content-between">
                    <span class="mr-3">
                        @if($val->role->name == 'Umum')
                            Departemen
                        @elseif($val->role->name == 'BPKAD' && $val->module =='pemutihan-aset')
                            Kepala Badan
                        @elseif($val->role->name == 'BPKAD' && $val->module =='penghapusan-aset')
                            Bidang Pengelolaan Aset Daerah
                        @else
                            {{ $val->role->name }}
                        @endif
                        <span class="text-{{ $val->show_color }}">({{ $val->show_type }})</span>
                        @if ($val->status == 'approved' && $val->user)
                            <div class="text-muted font-italic">
                                <div>{{ __('Verified by:') }} {{ $val->user->name }}</div>
                                <div>{{ __('Verified at:') }} {{ $val->approved_at->diffForHumans() }}</div>
                            </div>
                        @else
                            <div class="text-muted font-italic">
                                <div>{{ __('Verified by:') }} {{ $val->creatorName() }}</div>
                                <div>{{ __('Verified at:') }} {{ $val->creationDate() }}</div>
                            </div>
                        @endif
                    </span>
                    @if($val->status == 'approved')
                        <span class="text-muted font-italic text-right">
                            {!! '<span class="badge bg-success text-white">Verified</span>' !!}
                        </span>
                    @else
                        <span class="text-muted font-italic text-right">
                            {!! $val->labelStatus() !!}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-custom alert-light-danger align-items-center mb-0">
                <div class="alert-text">{{ __('Data tidak tersedia!') }}</div>
            </div>
        @endforelse
        @else
            @forelse($approvals as $val)
                <div class="timeline-item d-flex align-items-start">
                    <span class="timeline-badge bg-{{ $val->show_color }} mt-5px"></span>
                    <div class="timeline-content d-flex align-items-start justify-content-between">
                        <span class="mr-3">

                            @if($val->role->name == 'Umum')
                                Departemen
                            @elseif($val->role->name == 'BPKAD' && $val->module =='pemutihan-aset')
                                Kepala Badan
                            @elseif($val->role->name == 'BPKAD' && $val->module =='penghapusan-aset')
                                Bidang Pengelolaan Aset Daerah
                            @else
                                {{ $val->role->name }}
                            @endif
                            <span class="text-{{ $val->show_color }}">({{ $val->show_type }})</span>
                            @if ($val->status == 'approved' && $val->user)
                                <div class="text-muted font-italic">
                                    <div>{{ __('Approved by:') }} {{ $val->user->name }}</div>
                                    <div>{{ __('Approved at:') }} {{ $val->approved_at->diffForHumans() }}</div>
                                </div>
                            @else
                                <div class="text-muted font-italic">
                                    <div>{{ __('Created by:') }} {{ $val->creatorName() }}</div>
                                    <div>{{ __('Created at:') }} {{ $val->creationDate() }}</div>
                                </div>
                            @endif
                        </span>
                        <span class="text-muted font-italic text-right">
                            {!! $val->labelStatus() !!}
                        </span>
                    </div>
                </div>
            @empty
                <div class="alert alert-custom alert-light-danger align-items-center mb-0">
                    <div class="alert-text">{{ __('Data tidak tersedia!') }}</div>
                </div>
            @endforelse
        @endif
    </div>
@endsection

@section('buttons')
@endsection
