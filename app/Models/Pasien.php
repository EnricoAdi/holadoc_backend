<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;
    public $primaryKey = "id_pasien";
    protected $table = "pasien";
    public $timestamps = true;
    protected $fillable = [
        'keluhan',
        'biaya_konsultasi',
        'id_user_dokter',
        'id_user_pasien',
        'accepted',
    ];
    public function dokter(){
        return $this->belongsTo(User::class,"id_user_dokter","id_user");
    }
    public function pasien(){
        return $this->belongsTo(User::class,"id_user_pasien","id_user");
    }

}
