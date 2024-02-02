{{-- {{ dd(session('remember_username')) }} --}}
{{-- @extends('layouts.page')

@section('page')
 
@endsection --}}

@extends('layouts.page')

@section('page')
    {{-- <div class="row">
        @include($views . '._card-progress')
    </div> --}}
    <div class="row">
        @include($views . '._chart-aset_a')
        {{-- @include($views . '._chart-aset_b') --}}
        {{-- @include($views . '._chart-termin') --}}
    </div>
    <div class="row">
        {{-- @include($views . '._chart-aset_c')
        @include($views . '._chart-aset_d') --}}
        {{-- @include($views . '._chart-termin') --}}
    </div>
    <div class="row">
        {{-- @include($views . '._chart-aset_e')
        @include($views . '._chart-aset_f') --}}
        {{-- @include($views . '._chart-termin') --}}
    </div>
   
@endsection

