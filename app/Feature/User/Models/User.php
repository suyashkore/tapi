<?php

namespace App\Feature\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\Role;
use App\Feature\User\Models\UserOtp;
use Illuminate\Support\Collection;

/**
 * Class User
 *
 * @package App\Feature\User\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string $name
 * @property string $login_id
 * @property string $mobile
 * @property string|null $email
 * @property string|null $email2
 * @property string|null $google_id
 * @property string $password_hash
 * @property string|null $profile_pic_url
 * @property string $user_type
 * @property int|null $role_id
 * @property string|null $sso_id
 * @property string|null $sso_ref
 * @property string|null $job_title
 * @property string|null $department
 * @property string|null $aadhaar
 * @property string|null $pan
 * @property string|null $epf_uan
 * @property string|null $epf_num
 * @property string|null $esic
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property \Illuminate\Support\Carbon|null $last_password_reset
 * @property int $failed_login_attempts
 * @property bool $active
 * @property string|null $remarks
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Tenant|null $tenant
 * @property Role|null $role
 */
class User extends Authenticatable implements JWTSubject
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
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
        'tenant_id',
        'name',
        'login_id',
        'mobile',
        'email',
        'email2',
        'google_id',
        'password_hash',
        'profile_pic_url',
        'user_type',
        'role_id',
        'sso_id',
        'sso_ref',
        'job_title',
        'department',
        'aadhaar',
        'pan',
        'epf_uan',
        'epf_num',
        'esic',
        'last_login',
        'last_password_reset',
        'failed_login_attempts',
        'active',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tenant_id' => 'integer',
        'role_id' => 'integer',
        'failed_login_attempts' => 'integer',
        'active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'last_login' => 'datetime',
        'last_password_reset' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the role that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the user that created this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that updated this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the OTPs associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function otps(): HasMany
    {
        return $this->hasMany(UserOtp::class, 'user_id');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Get the privileges associated with the user's role.
     *
     * @return \Illuminate\Support\Collection
     */
    public function privileges(): Collection
    {
        // Lazy load the role and its privileges to avoid N+1 issues
        $role = $this->load('role.privileges')->role;

        return $role ? $role->privileges->pluck('name') : collect();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        $tenant = $this->tenant; // Assuming there is a tenant relationship
        $role = $this->role;     // Assuming there is a role relationship

        return [
            'user_id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'tenant_name' => $tenant ? $tenant->name : null,
            'tenant_logo_url' => $tenant ? $tenant->logo_url : null,
            'name' => $this->name,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'profile_pic_url' => $this->profile_pic_url,
            'login_id' => $this->login_id,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'role_name' => $role ? $role->name : null,
            'privileges' => $this->privileges()->toArray(),
        ];
    }
}
