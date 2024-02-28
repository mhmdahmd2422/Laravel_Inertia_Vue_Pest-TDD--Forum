<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public function setReadAt($timestamp = null){
        $this->update(['read_at' => $timestamp?? Carbon::now()]);
    }
}
