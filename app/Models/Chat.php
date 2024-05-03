<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    public $primaryKey = "id_chat";
    protected $table = "chat";
    public $timestamps = true;
    protected $fillable = [
        'id_user',
        'id_pasien',
        'message',
        'role',
    ];
    public function pasien(){
        return $this->belongsTo(Pasien::class,"id_pasien","id_pasien");
    }
    public function user(){
        return $this->belongsTo(User::class,"id_user","id_user");
    }
}
