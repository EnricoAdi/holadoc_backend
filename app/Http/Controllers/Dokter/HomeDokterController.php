<?php

namespace App\Http\Controllers\Dokter;

use App\Enums\RoleEnum;
use App\Enums\SpesialistDictionary;
use App\Enums\StatusPasienEnum;
use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HomeDokterController extends Controller
{
    //TODO add constructor for checking role
    public function __construct()
    {
        // $this->middleware('role:'.RoleEnum::DOKTER->value);
    }
    public function GetProfile(Request $request){
        $user = $request->user();
        //get dokter
        $dokter = User::getProfileByRole($user->id_user,RoleEnum::DOKTER->value);
        if(!$dokter){
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak ditemukan',
            ], 404);
        }
        //get gelar
        $spesialistDict = new SpesialistDictionary();
        $gelar = $spesialistDict->get($dokter->spesialis);
        $dokter->name = "dr. $dokter->name$gelar";

        $pasiens = Pasien::where('id_user_dokter', $dokter->id_user)
            ->where('accepted', StatusPasienEnum::DITERIMA->value)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($pasiens as $key => $value) {
            $nama = User::find($value->id_user_pasien)->name;
            if($nama){
                $pasiens[$key]->name = $nama;
            }else{
                $pasiens[$key]->name = "-";
            }
        }
        return response()->json([
            'success' => true,
            'user'    => $dokter,
            'pasiens' => $pasiens,
        ], 200);
    }

    public function SetBiaya(Request $request){
        $validator = Validator::make($request->all(), [
            'biaya'     => 'required|numeric|min:0',
        ]);

        //if validation fails
        if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success'=>false, 'message' => $errorMessage], 400);
        }
        $user = $request->user();
        //set biaya
        $dokter = User::getProfileByRole($user->id_user,RoleEnum::DOKTER->value);
        if(!$dokter){
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak ditemukan',
            ], 404);
        }
        $dokter->biaya_konsultasi = $request->biaya;
        $dokter->save();
        return response()->json([
            'success' => true,
            'message' => 'Biaya konsultasi berhasil diubah',
        ], 200);
    }

    public function DeletePasien(Request $request){
        $id_pasien = $request->id_pasien;

        if(!$id_pasien){
            return response()->json([
                'success' => false,
                'message' => 'ID Pasien tidak ditemukan',
            ], 404);
        }
        $pasien = Pasien::find($id_pasien);
        if(!$pasien){
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan',
            ], 404);
        }
        $pasien->accepted = StatusPasienEnum::DITOLAK->value;
        $pasien->save();
        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil dihapus',
        ], 200);
    }
}
