<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\inventaris\Aset;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KIBFController extends Controller
{
    //
    public function index(){
        $records = Aset::with('coad')->where('type','KIB F')
        ->get();

        $data = [];
        foreach ($records as $record) {
            $user = strip_tags($record->createsByRaw());
            $user = trim($user);
            $data[] = [
                // 'number' => request()->start,
                'name' => $record->usulans ? $record->usulans->asetd->name : '-',
                'kode_akun' => $record->coad ? $record->coad->kode_akun : '-',
                'nama_akun' => $record->coad ? $record->coad->nama_akun : '-',
                'nomor_register' => $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-',
                'tgl_register' => $record->book_date ? $record->book_date : '-',
                'bertingkat' => $record->is_graded_bld ? $record->is_graded_bld : '-',
                'berbeton' => $record->is_concreate_bld ? $record->is_concreate_bld : '-',
                'luas' => $record->wide ? $record->wide : '-',
                'nilai_residu' => $record->residual_value ? number_format($record->residual_value, 0, ',', ',') : '-',
                'nilai_beli' => $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : '-',
                'nilai_buku' => $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-',
                'masa_manfaat' => $record->useful ? $record->useful : '-',
                'akumulasi' => $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                'alamat' => $record->address ? $record->address : '-',
                'tahun_beli' => $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('d/m/Y') : '-',
                'status_tanah' => $record->statusTanah->name ? $record->statusTanah->name : '-',
                'nomor_dokumen' => $record->no_sertificate ? $record->no_sertificate : '-',
                'tgl_dokumen' => $record->sertificate_date ? date('d/m/Y', strtotime($record->sertificate_date)) : '-',
                'source_acq' => $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-',
                // 'source_acq' => $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ?
                //     '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' :
                //     '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>',
                'asal_usul' => $record->usulans->danad ? $record->usulans->danad->name : '-',
                'status' => $record->status ? ($record->status == 'actives' ? ucfirst('active'): ($record->status == 'notactive' ? ucfirst($record->status): ($record->status == 'in repair' ?ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))) : '-',
                'tanah_id' => $record->tanah_id ? $record->tanah_id : '-',
                'kondisi' => $record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-',
                // 'kondisi' => ucwords($record->condition == 'rusak berat' ? $record->condition : 'baik'),
                'keterangan' => $record->description ? $record->description : '-',
                'updated_by' => preg_replace('/\s+/', ' ', $user),
            ];
        }
        
        return response()->json([
            'status' => 200,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function detail(Request $request){
        $record = Aset::with('coad')->where('type','KIB F')->find($request->id);
        if ($record){
            $user = strip_tags($record->createsByRaw());
            $user = trim($user);

            $data[] = [
                // 'number' => request()->start,
                'id' => $record->id,
                'name' => $record->usulans ? $record->usulans->asetd->name : '-',
                'kode_akun' => $record->coad ? $record->coad->kode_akun : '-',
                'nama_akun' => $record->coad ? $record->coad->nama_akun : '-',
                'nomor_register' => $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-',
                'tgl_register' => $record->book_date ? $record->book_date : '-',
                'bertingkat' => $record->is_graded_bld ? $record->is_graded_bld : '-',
                'berbeton' => $record->is_concreate_bld ? $record->is_concreate_bld : '-',
                'luas' => $record->wide ? $record->wide : '-',
                'nilai_residu' => $record->residual_value ? number_format($record->residual_value, 0, ',', ',') : '-',
                'nilai_beli' => $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : '-',
                'nilai_buku' => $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-',
                'masa_manfaat' => $record->useful ? $record->useful : '-',
                'akumulasi' => $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                'alamat' => $record->address ? $record->address : '-',
                'tahun_beli' => $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('d/m/Y') : '-',
                'status_tanah' => $record->statusTanah->name ? $record->statusTanah->name : '-',
                'nomor_dokumen' => $record->no_sertificate ? $record->no_sertificate : '-',
                'tgl_dokumen' => $record->sertificate_date ? date('d/m/Y', strtotime($record->sertificate_date)) : '-',
                'source_acq' => $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-',
                // 'source_acq' => $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ?
                //     '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' :
                //     '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>',
                'asal_usul' => $record->usulans->danad ? $record->usulans->danad->name : '-',
                'status' => $record->status ? ($record->status == 'actives' ? ucfirst('active'): ($record->status == 'notactive' ? ucfirst($record->status): ($record->status == 'in repair' ?ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))) : '-',
                'tanah_id' => $record->tanah_id ? $record->tanah_id : '-',
                'kondisi' => $record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-',
                // 'kondisi' => ucwords($record->condition == 'rusak berat' ? $record->condition : 'baik'),
                'keterangan' => $record->description ? $record->description : '-',
                'updated_by' => preg_replace('/\s+/', ' ', $user),
            ];

            return response()->json([
                'response_code' => 200,
                'Message' =>$data,
            ], Response::HTTP_OK); 
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Data Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
