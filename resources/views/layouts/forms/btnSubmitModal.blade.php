@if(request()->route()->getName() == $routes . '.detailApprove')
    <button type="submit" onclick="submitForm()" class="btn btn-primary base-form--submit-modal" data-submit="1">
        <i class="fa fa-save mr-1"></i>
        {{ __('Simpan') }}
    </button>
@else
    <button type="submit" onclick="submitForm()" class="btn btn-primary base-form--submit-modal" data-submit="0">
        <i class="fa fa-save mr-1"></i>
        {{ __('Simpan') }}
    </button>
@endif
