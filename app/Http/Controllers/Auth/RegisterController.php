<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\SpesialistDictionary;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function RegisterPasien(Request $request){

        //set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'username'     => 'required|unique:users',
            'password'  => 'required|confirmed'
        ]);
        //min:8
         //if validation fails
         if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success'=>false, 'message' => $errorMessage], 400);
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'username'     => $request->username,
            'password'  => bcrypt($request->password),
            'role'      => RoleEnum::PASIEN,
            'spesialis' => "",
            'deskripsi' => "",
            'tahun_praktek' => 0,
            'biaya_konsultasi' => 0,
            'total_pendapatan' => 0,
        ]);
        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,
            ], 201);
        }

        //return JSON process insert failed
        return response()->json([
            'success' => false,
        ], 500);
    }
    public function RegisterDokter(Request $request){

        //set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'username'     => 'required|unique:users',
            'password'  => 'required|confirmed',
            'spesialis' => 'required',
            'deskripsi' => 'required',
            'tahun_praktek' => 'required|numeric|max:2024',
        ]);
        //min:8
         if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success'=>false, 'message' => $errorMessage], 400);
        }


        //cek spesialis di dictionary
        $dictionary = new SpesialistDictionary();
        try {
            $dictionary->get($request->spesialis);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success'=>false, 'message' => 'Spesialis tidak ditemukan'], 400);
        }
        //create user
        $user = User::create([
            'name'      => $request->name,
            'username'     => $request->username,
            'password'  => bcrypt($request->password),
            'role'      => RoleEnum::DOKTER,
            'spesialis' => $request->spesialis,
            'deskripsi' => $request->deskripsi,
            'tahun_praktek' => $request->tahun_praktek,
            'biaya_konsultasi' => 100000,
            'total_pendapatan' => 0,
        ]);
        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,
            ], 201);
        }

        //return JSON process insert failed
        return response()->json([
            'success' => false,
        ], 500);
    }
}
