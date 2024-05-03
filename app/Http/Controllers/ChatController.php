<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function SendChatDokter(Request $request){
        $validator = Validator::make($request->all(), [
            'message'     => 'required',
            'id_pasien'     => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success'=>false, 'message' => $errorMessage], 400);
        }
        $user = $request->user();
        //get dokter
        $dokter = User::getProfileByRole($user->id_user,RoleEnum::DOKTER->value);
        if(!$dokter){
            return response()->json([
                'success' => false,
                'message' => 'Dokter tidak ditemukan',
            ], 404);
        }
        //send chat
        $chat = Chat::create([
            'id_user' => $dokter->id_user,
            'id_pasien' => $request->id_pasien,
            'message' => $request->message,
            'role' => RoleEnum::DOKTER->value,
        ]);
        if(!$chat){
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim chat',
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengirim chat',
        ], 201);
    }

    public function SendChatPasien(Request $request){
        $validator = Validator::make($request->all(), [
            'message'     => 'required',
            'id_pasien'     => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success'=>false, 'message' => $errorMessage], 400);
        }
        $user = $request->user();
        //get pasien
        $userpasien = User::getProfileByRole($user->id_user,RoleEnum::PASIEN->value);
        if(!$userpasien){
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan',
            ], 404);
        }
        //send chat
        $chat = Chat::create([
            'id_user' => $userpasien->id_user,
            'id_pasien' => $request->id_pasien,
            'message' => $request->message,
            'role' => RoleEnum::PASIEN->value,
        ]);
        if(!$chat){
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim chat',
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengirim chat',
        ], 200);
    }

    public function FetchChat(Request $request){
        $id_pasien = $request->id_pasien;

        if(!$id_pasien){
            return response()->json([
                'success' => false,
                'message' => 'ID Pasien tidak ditemukan',
            ], 400);
        }
        //get chat
        $chat = Chat::where('id_pasien',$request->id_pasien)->orderBy("created_at","desc")->get();

        return response()->json([
            'success' => true,
            'chat' => $chat,
        ], 200);
    }
}
