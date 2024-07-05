<?php

namespace App\Feature\Office\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\Company\Models\Company;
use App\Feature\Office\Models\CpKyc;
use App\Feature\User\Models\User;

/**
 * Class Office
 *
 * @package App\Feature\Office\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string|null $company_tag
 * @property string $code
 * @property string $name
 * @property string|null $name_reg
 * @property string|null $gst_num
 * @property string|null $cin_num
 * @property bool $owned
 * @property string $o_type
 * @property int|null $cp_kyc_id
 * @property string|null $country
 * @property string|null $state
 * @property string|null $district
 * @property string|null $taluka
 * @property string|null $city
 * @property string $pincode
 * @property string $latitude
 * @property string $longitude
 * @property string $address
 * @property string|null $address_reg
 * @property bool $active
 * @property string|null $description
 * @property int|null $parent_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Office extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offices';

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
        'tenant_id',
        'company_tag',
        'code',
        'name',
        'name_reg',
        'gst_num',
        'cin_num',
        'owned',
        'o_type',
        'cp_kyc_id',
        'country',
        'state',
        'district',
        'taluka',
        'city',
        'pincode',
        'latitude',
        'longitude',
        'address',
        'address_reg',
        'active',
        'description',
        'parent_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_tag' => 'integer',
        'owned' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the company that owns the office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_tag');
    }

    /**
     * Get the CP KYC that owns the office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cpKyc(): BelongsTo
    {
        return $this->belongsTo(CpKyc::class, 'cp_kyc_id');
    }

    /**
     * Get the parent office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child offices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the user who created the office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
