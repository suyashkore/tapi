<?php

namespace App\Feature\Station\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Feature\User\Models\User;

class GeoHierarchy extends Model
{
    // use SoftDeletes;

    // The table associated with the model
    protected $table = 'geo_hierarchy';

    // The attributes that are mass assignable
    protected $fillable = [
        'country',
        'state',
        'district',
        'taluka',
        'po_name',
        'pincode',
        'po_lat',
        'po_long',
        'place',
        'place_lat',
        'place_long',
        'created_by',
        'updated_by'
    ];

    // Indicates if the model should be timestamped
    public $timestamps = true;

    // Cast attributes to specific types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
