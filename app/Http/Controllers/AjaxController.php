<?php

namespace App\Http\Controllers;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Notification;
use App\Models\Globals\TempFiles;
use App\Models\Master\Coa\COA;
use App\Models\Master\Geografis\City;
use App\Models\Master\Geografis\Province;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use Illuminate\Http\Request;
use App\Models\Master\Vendor\TypeVendor;
// use App\Models\Geografis\Province;
use Illuminate\Support\Str;



class AjaxController extends Controller
{

    public function saveTempFiles(Request $request)
    {
        $this->beginTransaction();
        $mimes = null;
        if ($request->accept == '.xlsx') {
            $mimes = 'xlsx';
        }
        if ($request->accept == '.png, .jpg, .jpeg') {
            $mimes = 'png,jpg,jpeg';
        }
        if ($mimes) {
            $request->validate(
                ['file' => ['mimes:' . $mimes]]
            );
        }
        try {
            if ($file = $request->file('file')) {
                $file_path = str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName());
                $file_path .= '-' . time() . '.' . $file->getClientOriginalExtension();

                $temp = new TempFiles;
                $temp->file_name = $file->getClientOriginalName();
                $temp->file_path = $file->storeAs('temp-files', $file_path, 'public');
                // $temp->file_type = $file->extension();
                $temp->file_size = $file->getSize();
                $temp->flag = $request->flag;
                $temp->save();
                return $this->commit(
                    [
                        'file' => TempFiles::find($temp->id)
                    ]
                );
            }
            return $this->rollback(['message' => 'File not found']);
        } catch (\Exception $e) {
            return $this->rollback(['error' => $e->getMessage()]);
        }
    }
    public function testNotification($emails)
    {
        if ($rkia = Rkia::latest()->first()) {
            request()->merge(
                [
                    'module' => 'rkia_operation',
                ]
            );
            $emails = explode('--', trim($emails));
            $user_ids = User::whereIn('email', $emails)->pluck('id')->toArray();
            $rkia->addNotify(
                [
                    'message' => 'Waiting Approval RKIA ' . $rkia->show_category . ' ' . $rkia->year,
                    'url' => rut('rkia.operation.summary', $rkia->id),
                    'user_ids' => $user_ids,
                ]
            );
            $record = Notification::latest()->first();
            return $this->render('mails.notification', compact('record'));
        }
    }

    public function userNotification()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->simplePaginate(25);
        return $this->render('layouts.base.notification', compact('notifications'));
    }

    public function userNotificationRead(Notification $notification)
    {
        auth()->user()
            ->notifications()
            ->updateExistingPivot($notification, array('readed_at' => now()), false);
        return redirect($notification->full_url);
    }

    public function selectRole($search, Request $request)
    {
        $items = Role::where('name', '!=', 'Administrator')->keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;

            case 'approver':
                $perms = str_replace('_', '.', $request->perms) . '.approve';
                $items = $items->whereHas(
                    'permissions',
                    function ($q) use ($perms) {
                        $q->where('name', $perms);
                    }
                );
                break;

            default:
                $items = $items->whereNull('id');
                break;
        }
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectLevelPosition($search, Request $request)
    {
        $items = LevelPosition::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'find':
                return $items->find($request->id);
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectCostComponent($search, Request $request)
    {
        $items = CostComponent::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'find':
                return $items->find($request->id);

            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectLevelJabatan($search, Request $request){
        $items = OrgStruct::keywordBy('name')->orderBy('level')->orderBy('name');
        switch ($search) {
            case 'by_level':
                $items = $items->where('level', $request->level_id);
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');

    }

    public function selectStruct($search, Request $request)
    {
        $items = OrgStruct::keywordBy('name')->orderBy('level')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'object_aset':
                $items = $items->whereIn('level', ['department', 'subdepartment', 'subsection']);
                break;
            case 'parent_bod':
                $items = $items->whereIn('level', ['root']);
                break;
            case 'parent_department':
                $items = $items->whereIn('level', ['bod']);
                break;
            case 'parent_subdepartment':
                $items = $items->whereIn('level', ['department']);
                break;
            case 'parent_subsection':
                $items = $items->whereIn('level', ['subdepartment']);
                break;
            case 'by_level':
                $req = $request->input('level_id');
                $items = $items->whereIn('level', [$req]);
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->when(
            $not = $request->not,
            function ($q) use ($not) {
                $q->where('id', '!=', $not);
            }
        )->get();
        $results = [];
        $more = false;

        $levels = ['root','bod', 'department', 'subdepartment', 'subsection'];
        $i = 0;
        foreach ($levels as $level) {
            if ($items->where('level', $level)->count()) {
                foreach ($items->where('level', $level) as $item) {
                    $results[$i]['text'] = strtoupper($item->show_level);
                    $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->name];
                }
                $i++;
            }
        }
        return response()->json(compact('results', 'more'));
    }

    public function selectPosition($search, Request $request)
    {
        $items = Position::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'by_location':
                //dd($request->org_struct);
                $req = $request->input('org_struct');
                $items = $items->where('location_id', $req);
                break;
            case 'divisi_spi':
                $location_id = OrgStruct::where('name', 'Satuan Pengawas Internal')->firstOrFail();
                $items = $items->where('location_id', $location_id);
                break;
            case 'auditor':
                $items = $items->whereHas(
                    'location',
                    function ($qq) {
                        $qq->inAudit();
                    }
                );
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectUser($search, Request $request)
    {
   
        $items = User::keywordBy('name')
            ->has('position')
            ->where('status', 'active')
            ->orderBy('name');

        switch ($search) {
            case 'all':
                $items = $items
                    ->when(
                        $with_admin = $request->with_admin,
                        function ($q) use ($with_admin) {
                            $q->orWhere('id', 1);
                        }
                    );
                break;
            case 'level_bod':
                $items = $items->whereHas(
                    'position',
                    function ($q) {
                        $q->whereHas(
                            'location',
                            function ($qq) {
                                $qq->where('level', 'bod');
                            }
                        );
                    }
                );
                break;
            case 'level_department':
                $items = $items->whereHas(
                    'position',
                    function ($q) {
                        $q->whereHas(
                            'location',
                            function ($qq) {
                                $qq->where('level', 'department');
                            }
                        );
                    }
                );
                break;
            case 'org_struct':
                // $dep = $request->org_struct;
                $req = $request->input('org_struct');
                $items = $items->whereHas(
                    'position',
                    function ($q) use($req) {
                        $q->whereHas(
                             'struct',
                             function ($qq) use ($req) {
                                $qq->where('location_id', $req);
                            }
                        );
                    }
                );
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }
        $items = $items->paginate();

        $results = [];
        $more = $items->hasMorePages();
        foreach ($items as $item) {
            $results[] = ['id' => $item->id, 'text' => $item->name . ' (' . ($item->position->name ?? '') . ')'];
        }
        return response()->json(compact('results', 'more'));
    }

    public function selectCoa($search, Request $request){
        $items = COA::keywordBy('nama_akun')->orderBy('nama_akun');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->when(
            $not = $request->not,
            function ($q) use ($not) {
                $q->where('id', '!=', $not);
            }
        )->get();
        $results = [];
        $more = false;

        $tipe_akuns = ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'];
        $i = 0;
        foreach ($tipe_akuns as $tipe_akun) {
            if ($items->where('tipe_akun', $tipe_akun)->count()) {
                foreach ($items->where('tipe_akun', $tipe_akun) as $item) {
                    $results[$i]['text'] = strtoupper($item->show_tipe_akun);
                    $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->nama_akun];
                }
                $i++;
            }
        }
        return response()->json(compact('results', 'more'));

    }

    public function selectCity($search, Request $request){
        $req = $request->input('province_id');
        $items = City::where('province_id',$req);
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectProvince($search, Request $request){
        $items = Province::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');

    }

    public function selectJenisUsaha($search, Request $request){
        $items = TypeVendor::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name','id');
    }
}
