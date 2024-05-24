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

class KIBAController extends Controller
{
    //
    public function index(){
        $records = Aset::with('coad')->where('type','KIB A')->get();

        // $data[] = [];
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
                'luas_tanah' => $record->wide ? number_format($record->wide, 0, ',', ',') : '-',
                'provinsi' => $record->province_id ? $record->provinsi->name : '-',
                'kota' => $record->city_id ? $record->city->name : '-',
                'daerah' => $record->district_id ? $record->district->name : '-',
                'alamat' => $record->address ? ucwords($record->address) : '-',
                'source_acq' => $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-',
                'tahun_beli' => $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : '-',
                'hak_tanah' => $record->hakTanah->name ? $record->hakTanah->name : '-',
                'kegunaan_tanah' => $record->land_use ? ucwords($record->land_use) : '-',
                'nomor_sertifikat' => $record->no_sertificate ? $record->no_sertificate : '-',
                'tgl_sertifikat' => $record->sertificate_date ? date('d/m/Y', strtotime($record->sertificate_date)) : '-',
                'asal_usul' => $record->usulans->danad ? $record->usulans->danad->name : '-',
                'nilai_beli' => $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ','),
                'status' => $record->status == 'actives' ? ucfirst('active') : ($record->status == 'notactive' ? ucfirst($record->status) : ($record->status == 'in repair' ? ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status): ucfirst($record->status)))),
                'keterangan' => $record->description ? $record->description : '-',
                'created_by' => preg_replace('/\s+/', ' ', $user),
            ];
        }
        
        // Mengirim data sebagai respons JSON
        return response()->json([
            'status' => 200,
            'data' => $data,
        ], Response::HTTP_OK);
        
    }

    public function detail(Request $request){
        $record = Aset::with('coad')->where('type','KIB A')->find($request->id);
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
                'luas_tanah' => $record->wide ? number_format($record->wide, 0, ',', ',') : '-',
                'provinsi' => $record->province_id ? $record->provinsi->name : '-',
                'kota' => $record->city_id ? $record->city->name : '-',
                'daerah' => $record->district_id ? $record->district->name : '-',
                'alamat' => $record->address ? ucwords($record->address) : '-',
                'source_acq' => $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-',
                'tahun_beli' => $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : '-',
                'hak_tanah' => $record->hakTanah->name ? $record->hakTanah->name : '-',
                'kegunaan_tanah' => $record->land_use ? ucwords($record->land_use) : '-',
                'nomor_sertifikat' => $record->no_sertificate ? $record->no_sertificate : '-',
                'tgl_sertifikat' => $record->sertificate_date ? date('d/m/Y', strtotime($record->sertificate_date)) : '-',
                'asal_usul' => $record->usulans->danad ? $record->usulans->danad->name : '-',
                'nilai_beli' => $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ','),
                'status' => $record->status == 'actives' ? ucfirst('active') : ($record->status == 'notactive' ? ucfirst($record->status) : ($record->status == 'in repair' ? ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status): ucfirst($record->status)))),
                'keterangan' => $record->description ? $record->description : '-',
                'created_by' => preg_replace('/\s+/', ' ', $user),
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

    // public function ViewDosen(){
    //     $data = Dosen::orderBy('updated_at', 'desc')->get();
    //     return response()->json([
    //         'status' => 200,
    //         'data' =>$data,
    //     ], Response::HTTP_OK); 
    // }



}
