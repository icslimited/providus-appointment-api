<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [ 'date', 'startTime', 'endTime', 'location', 'user_id', 'guestName', 'guestEmail', 'guestContact', 'guestOrganization', 'reason', 'status' ];
}
