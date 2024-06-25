<?php

namespace App\Feature\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Feature\User\Models\User;
use App\Feature\User\Models\Role;
use App\Feature\User\Models\Privilege;
use App\Feature\User\Models\UserOtp;

/**
 * Class Tenant
 *
 * @package App\Feature\Tenant\Models
 * @property int $id
 * @property string $name
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property string $pincode
 * @property string $latitude
 * @property string $longitude
 * @property string|null $logo_url
 * @property string|null $description
 * @property bool $active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Tenant extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenants';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country',
        'state',
        'city',
        'pincode',
        'address',
        'latitude',
        'longitude',
        'logo_url',
        'description',
        'active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the users associated with the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the roles associated with the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get the privileges associated with the tenant through roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function privileges(): HasManyThrough
    {
        return $this->hasManyThrough(Privilege::class, Role::class);
    }

    /**
     * Get the user OTPs associated with the tenant through users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function userOtps(): HasManyThrough
    {
        return $this->hasManyThrough(UserOtp::class, User::class);
    }

    /**
     * Get the tenant KYC associated with the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tenantKyc(): HasOne
    {
        return $this->hasOne(TenantKyc::class);
    }

    /**
     * Get the user who created the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
