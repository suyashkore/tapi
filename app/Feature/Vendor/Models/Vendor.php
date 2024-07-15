<?php

namespace App\Feature\Vendor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Office\Models\Office;
use App\Feature\Company\Models\Company;

/**
 * Class Vendor
 *
 * @package App\Feature\Vendor\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string|null $company_tag
 * @property string $code
 * @property string $name
 * @property string|null $name_reg
 * @property string|null $legal_name
 * @property string|null $legal_name_reg
 * @property string $v_type
 * @property string $mobile
 * @property string|null $email
 * @property string $contracting_office_id
 * @property string|null $erp_code
 * @property bool $active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Vendor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vendors';

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
        'legal_name',
        'legal_name_reg',
        'v_type',
        'mobile',
        'email',
        'contracting_office_id',
        'erp_code',
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
        'tenant_id' => 'integer',
        'company_tag' => 'integer',
        'active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the vendor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the company tag that owns the vendor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companyTag(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_tag');
    }

    /**
     * Get the office that owns the vendor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contractingOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'contracting_office_id');
    }

    /**
     * Get the user who created the vendor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the vendor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the vendor KYC for the vendor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendorKyc(): HasOne
    {
        return $this->hasOne(VendorKyc::class, 'vendor_id');
    }
}
