<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentCard extends Model
{
    use HasFactory;

    protected $fillable = [ 'appointment_id', 'card_id', 'timeIssued', 'timeReturned' ];
}
