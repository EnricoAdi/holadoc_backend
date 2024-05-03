<?php

namespace App\Enums;

enum StatusPasienEnum: int
{
    case DITERIMA = 0;
    case SELESAI= 1;
    case DITOLAK = -1;
}
