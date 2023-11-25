<?php

namespace App\Models\Pengajuan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Model;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use Carbon\Carbon;

class Pembelian extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_pengajuan_pembelian';

    protected $fillable = [
        'code',
        'date',
        'user_kepada',
        'struct_id',
        'regarding',
        'sentence_start',
        'sentence_end',
        'note_disposisi',
    
        'verification_status',
        'verification_user_id',
        'verification_at',

        'status',
        'version',
        'upgrade_reject'
    ];

    protected $casts = [
        'date'          => 'date',
    ];

    /*******************************
     ** MUTATOR
     *******************************/
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    /*******************************
     ** ACCESSOR
     *******************************/

    /*******************************
     ** RELATION
     *******************************/

    public function struct()
    {
        return $this->belongsTo(OrgStruct::class, 'struct_id');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id');
    }

    public function cc()
    {
        return $this->belongsToMany(User::class, 'trans_pengajuan_pembelian_cc', 'pembelian_id', 'user_id');
    }

    public function to_user()
    {
        return $this->belongsTo(User::class, 'user_kepada');
    }

    /*******************************
     ** SCOPE
     *******************************/
    public function scopeGrid($query)
    {
        return $query->with(['struct']);
    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query;
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {
            $data = $request->all();
            $this->fill($data);
            $this->status = 'draft';
            $this->save();
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
            $this->saveLogNotify();
            $this->cc()->sync($request->cc ?? []);


            if ($request->is_submit) {
                $this->handleSubmitSave($request);
            }

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDetailStoreOrUpdate($request, PembelianDetail $detail)
    {
        $this->beginTransaction();
        try {
            
            $detail->fill($request->all());
          
            $this->details()->save($detail);
            $detail->save();
            $this->saveLogNotify();
            return $this->commitSaved();
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            $this->saveLogNotify();
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleDetailDestroy(PembelianDetail $detail)
    {
        $this->beginTransaction();
        try {
            $this->saveLogNotify();
            $detail->delete();

            return $this->commitDeleted([
                'redirect' => route(request()->routes . '.detail', $this->id)
            ]);
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }


    public function handleApprove($request)
    {
        $this->beginTransaction();
        try {
            if ($this->status === 'waiting.approval.revisi') {
                $this->approveApproval($request->module . '_upgrade');
                if ($this->firstNewApproval($request->module . '_upgrade')) {
                    $this->update(['status' => 'waiting.approval.revisi']);
                    $this->saveLogNotify();
                } else {
                    $this->update(
                        [
                            'status' => 'draft',
                            'version' => $this->version + 1,
                        ]
                    );
                    $this->saveLogNotify();
                }
            } else {
                $this->approveApproval($request->module);
                if ($this->firstNewApproval($request->module)) {
                    $this->update(['status' => 'waiting.approval']);
                    $this->saveLogNotify();
                } else {
                    $this->update(['status' => 'completed']);
                    $this->saveLogNotify();
                    $this->_generateReport('completed');
                }
            }

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function saveLogNotify()
    {
        $data = 'Pengajuan Pembelian No Surat : ' . $this->code;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);
                }
                break;
            case $routes . '.update':
                $this->addLog('Mengubah ' . $data);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);
                }
                break;
            case $routes . '.destroy':
                $this->addLog('Menghapus ' . $data);
                break;
            case $routes . '.verify':
                $this->addNotify([
                    'message' => 'Waiting Approval ' . $data,
                    'url' => route($routes . '.approval', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                ]);
                break;
            case $routes . '.approve':
                if (in_array($this->status, ['draft', 'waiting.approval.revisi'])) {
                    $this->addLog('Menyetujui Revisi ' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval Revisi ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                } else {
                    $this->addLog('Menyetujui ' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                }
                break;
            case $routes . '.reject':
                if (in_array($this->status, ['rejected'])) {
                    $this->addLog('Menolak ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak ' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);
                } else {
                    $this->addLog('Menolak Revisi ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak Revisi ' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);
                }
                break;
            case $routes . '.revisi':
                $this->addLog('Revisi ' . $data);
                $this->addNotify([
                    'message' => 'Waiting Approval Revisi ' . $data,
                    'url' => route($routes . '.approval', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module') . "_upgrade"),
                ]);
                break;
        }
    }


    public function checkAction($action, $perms, $record = null)
    {
        $user = auth()->user();
        switch ($action) {
            case 'edit':
                $checkStatus = in_array($this->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.edit');
            case 'delete':
                $checkStatus = in_array($this->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.delete');
            case 'show':
            case 'history':
                return true;
            case 'verification':
                if ($this->status === 'waiting.verification') {
                    return $user->isVerificationKepalaDepartement($this->struct);
                }
                return false;
                break;
            case 'approval':
                if ($this->status === 'waiting.approval.revisi') {
                    if ($this->checkApproval(request()->get('module') . '_upgrade')) {
                        return $user->checkPerms($perms . '.approve');
                    }
                }
                if ($this->checkApproval(request()->get('module'))) {
                    $checkStatus = in_array($this->status, ['waiting.approval']);
                    return $checkStatus && $user->checkPerms($perms . '.approve');
                }
                break;
            case 'revisi':
                if ($user->checkPerms($perms . '.edit')) {
                    // if (isset($this->pembatalan->status) && in_array($this->pembatalan->status, ['waiting.approval', 'waiting.approval.revisi', 'completed'])) {
                    //     return false;
                    // }
                    // if (isset($this->pembayaran->status) &&  in_array($this->pembayaran->status, ['waiting.approval', 'waiting.approval.revisi', 'completed'])) {
                    //     return false;
                    // }
                    return $this->status === 'completed';
                }
                break;
            case 'tracking':
                $checkStatus = in_array($this->status, ['waiting.approval', 'completed', 'waiting.approval.revisi']);
                return $checkStatus;
            case 'print':
                $checkStatus = in_array($this->status, ['waiting.approval', 'rejected', 'completed']);
                return $checkStatus;
        }

        return false;
    }
}
