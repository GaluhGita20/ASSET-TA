@extends('layouts.form')

@section('action', rut($routes.'.update', $record->id))

@section('card-title')
	Flow Approval |&nbsp;<span class="label label-xl label-danger label-inline text-nowrap">{{ $record->show_module }}</span>
@endsection

@section('card-body')
	@method('PATCH')
	<div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">#</th>
                    <th class="text-center">Berdasarkan Role</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Tipe Posisi</th>
                    <th class="text-center">Jabatan</th>
                    <th class="text-center" style="width: 100px;">Sekuensial</th>
                    <th class="text-center" style="width: 100px;">Paralel</th>
                    <th class="text-center valign-middle" style="width: 100px;">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-sm btn-icon btn-info btn-circle add-flow">
                            	<i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($record->flows as $flow)
                    <tr data-key="{{ $loop->iteration }}">
                        <td class="text-center no">{{ $loop->iteration }}</td>
						<td class="text-center parent-group">
                        	<label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
                                <input type="radio" class="approve check" name="flows[{{ $loop->iteration }}][with_role]" value="1" {{ $flow->with_role == 1 ? 'checked' : '' }}>
                                <span></span>
                            </label>
                        </td>
                        <td class="text-left parent-group">
                        	<select name="flows[{{ $loop->iteration }}][role_id]"
                        		class="form-control base-plugin--select2-ajax"
                        		data-url="{{ rut('ajax.selectRole', ['search'=>'approver', 'perms'=>$record->module]) }}"
                        		data-placeholder="{{ __('Pilih Salah Satu') }}">
                        		@if ($flow->role)
                        			<option value="{{ $flow->role->id }}">{{ $flow->role->name }}</option>
                        		@endif
                        	</select>
                        </td>
						<td class="text-left parent-group">
                        	<select name="flows[{{ $loop->iteration }}][type_position]"
                        		class="form-control base-plugin--select2 type_position"
                        		data-placeholder="{{ __('Pilih Salah Satu') }}">
                        		<option {{ $flow->type_position == 1 ? 'selected' : '' }} value="1">{{ __('ALL POSITION') }}</option>
                        		<option {{ $flow->type_position == 2 ? 'selected' : '' }} value="2">{{ __('BY HEAD DEPARTEMEN') }}</option>
                        		<option {{ $flow->type_position == 3 ? 'selected' : '' }} value="3">{{ __('BY POSITION') }}</option>
                        	</select>
                        </td>
						<td class="text-left parent-group">
                        	<select name="flows[{{ $loop->iteration }}][position_id]"
                        		class="form-control base-plugin--select2-ajax"
                        		data-url="{{ rut('ajax.selectPosition', 'all') }}"
                        		data-placeholder="{{ __('Pilih Salah Satu') }}">
                        		@if ($flow->position)
                        			<option value="{{ $flow->position->id }}">{{ $flow->position->name }}</option>
                        		@endif
                        	</select>
                        </td>
                        <td class="text-center parent-group">
                            <label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
                                <input type="radio" class="approve check" name="flows[{{ $loop->iteration }}][type]" value="1" {{ $flow->type == 1 ? 'checked' : '' }}>
                                <span></span>
                            </label>
                        </td>
                        <td class="text-center parent-group">
                            <label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
                                <input type="radio" class="approve check" name="flows[{{ $loop->iteration }}][type]" value="2" {{ $flow->type == 1 ? '' : 'checked' }}>
                                <span></span>
                            </label>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                {{-- <button type="button" class="btn btn-danger btn-sm btn-icon remove-flow" {{ $loop->count <= 1 ? 'disabled' : '' }}><i class="fa fa-trash"></i></button> --}}
                                <button type="button" class="btn  btn-circle btn-danger btn-sm btn-icon remove-flow"><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-key="1">
                        <td class="text-center no">1</td>
                        <td class="text-left parent-group">
                            <select name="flows[1][role_id]" class="form-control base-plugin--select2-ajax"
                            	data-url="{{ rut('ajax.selectRole', ['search'=>'approver', 'perms'=>$record->module]) }}"
                            	data-placeholder="{{ __('Pilih Salah Satu') }}">
                            </select>
                        </td>
                        <td class="text-center parent-group">
                            <label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
                                <input type="radio" class="approve check" name="flows[1][type]" value="1" checked>
                                <span></span>
                            </label>
                        </td>
                        <td class="text-center parent-group">
                            <label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
                                <input type="radio" class="approve check" name="flows[1][type]" value="2">
                                <span></span>
                            </label>
                        </td>
                        <td class="text-center justify-content-center">
                            <div class="d-flex justify-content-center">
                                {{-- <button type="button" class="btn btn-danger btn-sm btn-icon remove-flow" disabled><i class="fa fa-trash"></i></button> --}}
                                <button type="button" class="btn btn-circle btn-danger btn-sm btn-icon remove-flow"><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
	</div>
@endsection

@push('scripts')
	<script>
	    $(function () {
	        var refreshNo = function (tbody) {
	            $(tbody).find('.no').each(function (i) {
	                $(this).html(i+1);
	            });
	            // $(tbody).find('.remove-flow').prop('disabled', false);
	            // if ($(tbody).find('.remove-flow').length <= 1) {
	            //     $(tbody).find('.remove-flow').prop('disabled', true);
	            // }
	        }

	        $('.content-page').on('click', '.add-flow', function (e) {
	            var me = $(this),
	                tbody = me.closest('table').find('tbody').first(),
	                key = tbody.find('tr').length ? parseInt(tbody.find('tr').last().data('key')) + 1 : 1;

	            var template = `
	                <tr data-key="`+key+`">
	                    <td class="text-center no">`+key+`</td>
	                    <td class="text-left parent-group">
	                        <select name="flows[`+key+`][role_id]" class="form-control base-plugin--select2-ajax"
	                        	data-url="{{ rut('ajax.selectRole', ['search'=>'approver', 'perms'=>$record->module]) }}"
	                        	data-placeholder="{{ __('Pilih Salah Satu') }}">
	                        </select>
	                    </td>
	                    <td class="text-center parent-group">
	                        <label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
	                            <input type="radio" class="approve check" name="flows[`+key+`][type]" value="1" checked>
	                            <span></span>
	                        </label>
	                    </td>
	                    <td class="text-center parent-group">
	                        <label class="checkbox checkbox-lg d-inline-block checkbox-rounded">
	                            <input type="radio" class="approve check" name="flows[`+key+`][type]" value="2">
	                            <span></span>
	                        </label>
	                    </td>
	                    <td class="text-center justify-content-center">
	                        <div class="d-flex justify-content-center">
	                            <button type="button" class="btn btn-circle btn-danger btn-sm btn-icon remove-flow"><i class="fa fa-trash"></i></button>
	                        </div>
	                    </td>
	                </tr>
	            `;

	            tbody.append(template);
	            refreshNo(tbody);
	            BasePlugin.initSelect2();
	        });

	        $('.content-page').on('click', '.remove-flow', function (e) {
	            var me = $(this),
	                tbody = me.closest('table').find('tbody').first();

	            me.closest('tr').remove();
	            refreshNo(tbody);
	            BasePlugin.initSelect2();
	        });
	    });
	</script>
@endpush
