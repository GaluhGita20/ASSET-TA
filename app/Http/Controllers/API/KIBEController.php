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

class KIBEController extends Controller
{
    //
    public function index(){
        $records = Aset::with('coad')->where('type','KIB E')
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
                'judul' => $record->title ? ucwords($record->title) : '-',
                'pencipta' => $record->creators ? ucwords($record->creators) : '-',
                'spesifikasi' => $record->spesifikasi ? $record->spesifikasi : '-',
                'bahan' => $record->materials->name ? $record->materials->name : '-',
                'jenis' => $record->tipe_animal ? $record->tipe_animal : '-',
                'ukuran' => $record->size_animal ? $record->size_animal : '-',
                'tahun_beli' => $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : '-',
                'asal_usul' => $record->usulans->danad ? $record->usulans->danad->name : '-',
                'nilai_residu' => $record->residual_value ? number_format($record->residual_value, 0, ',', ',') : '-',
                'nilai_beli' => $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') :
                    number_format($record->usulans->HPS_unit_cost, 0, ',', ','),
                'nilai_buku' => $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-',
                'masa_manfaat' => $record->useful ? $record->useful : '-',
                'akumulasi' => $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                'source_acq' => $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-',
                // 'source_acq' => $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ?
                //     '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' :
                //     '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>',
                'kondisi' => $record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-',
                // 'kondisi' => $record->condition == 'baik' ?
                //     '<span class="badge bg-success text-white">'.ucwords($record->condition).'</span>' :
                //     ($record->condition == 'rusak berat' ?
                //         '<span class="badge bg-danger text-white">'.ucwords($record->condition).'</span>' :
                //         '<span class="badge bg-warning text-white">'.ucwords($record->condition).'</span>'),
                'status' => $record->status ? ($record->status == 'actives' ? ucfirst('active'): ($record->status == 'notactive' ? ucfirst($record->status): ($record->status == 'in repair' ?ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))) : '-',
                'unit' => !empty($record->usulans->perencanaan->struct) ?
                    ($record->usulans->perencanaan->struct->name ? $record->usulans->perencanaan->struct->name : '-') :
                    ($record->location_hibah_aset ? $record->deps->name : '-'),
                'keterangan' => $record->description ? $record->description : '-',
                'location' => $record->locations ? $record->locations->name : $record->non_room_location,
                'updated_by' => preg_replace('/\s+/', ' ', $user),
            ];
        }
        
        return response()->json([
            'status' => 200,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function detail(Request $request){
        $record = Aset::with('coad')->where('type','KIB E')->find($request->id);
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
                'judul' => $record->title ? ucwords($record->title) : '-',
                'pencipta' => $record->creators ? ucwords($record->creators) : '-',
                'spesifikasi' => $record->spesifikasi ? $record->spesifikasi : '-',
                'bahan' => $record->materials->name ? $record->materials->name : '-',
                'jenis' => $record->tipe_animal ? $record->tipe_animal : '-',
                'ukuran' => $record->size_animal ? $record->size_animal : '-',
                'tahun_beli' => $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : '-',
                'asal_usul' => $record->usulans->danad ? $record->usulans->danad->name : '-',
                'nilai_residu' => $record->residual_value ? number_format($record->residual_value, 0, ',', ',') : '-',
                'nilai_beli' => $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') :
                    number_format($record->usulans->HPS_unit_cost, 0, ',', ','),
                'nilai_buku' => $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-',
                'masa_manfaat' => $record->useful ? $record->useful : '-',
                'akumulasi' => $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                'source_acq' => $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-',
                // 'source_acq' => $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ?
                //     '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' :
                //     '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>',
                'kondisi' => $record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-',
                // 'kondisi' => $record->condition == 'baik' ?
                //     '<span class="badge bg-success text-white">'.ucwords($record->condition).'</span>' :
                //     ($record->condition == 'rusak berat' ?
                //         '<span class="badge bg-danger text-white">'.ucwords($record->condition).'</span>' :
                //         '<span class="badge bg-warning text-white">'.ucwords($record->condition).'</span>'),
                'status' => $record->status ? ($record->status == 'actives' ? ucfirst('active'): ($record->status == 'notactive' ? ucfirst($record->status): ($record->status == 'in repair' ?ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))) : '-',
                'unit' => !empty($record->usulans->perencanaan->struct) ?
                    ($record->usulans->perencanaan->struct->name ? $record->usulans->perencanaan->struct->name : '-') :
                    ($record->location_hibah_aset ? $record->deps->name : '-'),
                'keterangan' => $record->description ? $record->description : '-',
                'location' => $record->locations ? $record->locations->name : $record->non_room_location,
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
