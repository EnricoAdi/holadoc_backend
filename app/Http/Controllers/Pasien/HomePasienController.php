<?php

namespace App\Http\Controllers\Pasien;

use App\Enums\RoleEnum;
use App\Enums\SpesialistDictionary;
use App\Enums\StatusPasienEnum;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HomePasienController extends Controller
{
    public function GetDoctors(Request $request){
        $filterSpesialis = $request->query("spesialisasi");
        $filterName = $request->query("name");

        $doctors = [];
        $doctorQ = User::where('role', RoleEnum::DOKTER->value)->where("biaya_konsultasi",">",0);
        if($filterSpesialis!="Semua" && $filterSpesialis!=""){
            $doctorQ = $doctorQ->where("spesialis",$filterSpesialis);
        }
        if($filterName!=""){
            $doctors = $doctorQ->where("name","like","%$filterName%");
        }

        $doctors = $doctorQ->get();
        $spesialistDict = new SpesialistDictionary();

        $year = date("Y");
        foreach ($doctors as $key => $value) {
            $dokter = $doctors[$key];
            $doctors[$key]->pengalaman = $year - $value->tahun_praktek;
            //get gelar
            $gelar = $spesialistDict->get($dokter->spesialis);
            $doctors[$key]->name = "dr. $dokter->name$gelar";
        }
        //todo get the latest consultation
        $id = $request->user()->id_user;
        $latest = Pasien::where('id_user_pasien', $id)
            ->orderBy('created_at', 'desc')->first();
        // Log::info(print_r($latest,true));
        // Log::info($latest==null);

        $isConsulting = StatusPasienEnum::SELESAI;
        if($latest!=null && $latest->accepted != StatusPasienEnum::SELESAI->value){
            $isConsulting = $latest->accepted;
        }

        return response()->json([
            'success' => true,
            'doctors' => $doctors,
            'is_consulting' => $isConsulting,
        ], 200);
    }

    public function CreateAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'keluhan'     => 'required|string',
            'dokter'     => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success'=>false, 'message' => $errorMessage], 400);
        }

        $user = $request->user();
        $dokter = User::find($request->dokter)->where('role', RoleEnum::DOKTER->value)->first();
        if(!$dokter){
            return response()->json([
                'success' => false,
                'message' => "Dokter tidak ditemukan",
            ], 404);
        }
        $biaya = $dokter->biaya_konsultasi;

        $pasien = Pasien::create([
            'keluhan'      => $request->keluhan,
            'biaya_konsultasi'      => $biaya,
            'id_user_dokter'     => $request->dokter,
            'id_user_pasien'  => $user->id_user,
            'accepted' => StatusPasienEnum::DITERIMA->value,
        ]);
        //return response JSON user is created
        if($pasien) {
            return response()->json([
                'success' => true,
                'pasien'    => $pasien,
            ], 201);
        }

        //return JSON process insert failed
        return response()->json([
            'success' => false,
            'message' => "Gagal membuat konsultasi",
        ], 500);
    }

    public function GetRecentAppointmentPasien(Request $request){
        $user = $request->user();
        $latest = Pasien::where('id_user_pasien', $user->id_user)
            ->where("accepted", StatusPasienEnum::DITERIMA->value)
            ->orderBy('created_at', 'desc')
            ->first();

        // Log::info(print_r($latest,true));
        if(!$latest){
            return response()->json([
                'success' => false,
                'message' => 'Konsultasi tidak ditemukan',
            ], 404);
        }
        $dokter = User::where("id_user",$latest->id_user_dokter)->first();

        $spesialistDict = new SpesialistDictionary();
        $gelar = $spesialistDict->get($dokter->spesialis);
        $dokter->name = "dr. $dokter->name$gelar";

        if(!$dokter){
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak ditemukan',
            ], 404);
        }

        //get chat
        $chat = Chat::where('id_pasien',$latest->id_pasien)->orderBy("created_at","desc")->get();

        return response()->json([
            'success' => true,
            'dokter' => $dokter,
            'appointment' => $latest,
            'chat' => $chat,
        ], 200);
    }

    public function DoneAppointment(Request $request){
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
        if($pasien->id_user_pasien != $request->user()->id_user){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        $pasien->accepted = StatusPasienEnum::SELESAI->value;
        $pasien->save();

        //tambah pendapatan dokter
        $dokter = User::find($pasien->id_user_dokter);
        $dokter->total_pendapatan += $pasien->biaya_konsultasi;
        $dokter->save();

        return response()->json([
            'success' => true,
            'message' => 'Konsultasi selesai',
        ], 200);
    }
}
