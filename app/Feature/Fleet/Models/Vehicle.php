<?php

namespace App\Feature\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Company\Models\Company;
use App\Feature\Office\Models\Office;
use App\Feature\Vendor\Models\Vendor;

/**
 * Class Vehicle
 *
 * @package App\Feature\Fleet\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property int|null $company_tag
 * @property int $base_office_id
 * @property int|null $vendor_id
 * @property string $rc_num
 * @property string|null $vehicle_num
 * @property string $vehicle_ownership
 * @property string $make
 * @property string|null $model
 * @property float|null $gvw
 * @property float|null $capacity
 * @property string|null $gvw_capacity_unit
 * @property float|null $length
 * @property float|null $width
 * @property float|null $height
 * @property string|null $lwh_unit
 * @property string|null $specification
 * @property string|null $sub_specification
 * @property string|null $fuel_type
 * @property \Illuminate\Support\Carbon|null $rto_reg_expiry
 * @property string|null $rc_url
 * @property string|null $insurance_policy_num
 * @property \Illuminate\Support\Carbon|null $insurance_expiry
 * @property string|null $insurance_doc_url
 * @property string|null $fitness_cert_num
 * @property \Illuminate\Support\Carbon|null $fitness_cert_expiry
 * @property string|null $fitness_cert_url
 * @property string|null $vehicle_contact_mobile1
 * @property string|null $vehicle_contact_mobile2
 * @property bool $active
 * @property string $status
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Vehicle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicles';

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
     * @var array
     */
    protected $fillable = [
        'tenant_id',
        'company_tag',
        'base_office_id',
        'vendor_id',
        'rc_num',
        'vehicle_num',
        'vehicle_ownership',
        'make',
        'model',
        'gvw',
        'capacity',
        'gvw_capacity_unit',
        'length',
        'width',
        'height',
        'lwh_unit',
        'specification',
        'sub_specification',
        'fuel_type',
        'rto_reg_expiry',
        'rc_url',
        'insurance_policy_num',
        'insurance_expiry',
        'insurance_doc_url',
        'fitness_cert_num',
        'fitness_cert_expiry',
        'fitness_cert_url',
        'vehicle_contact_mobile1',
        'vehicle_contact_mobile2',
        'active',
        'status',
        'note',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tenant_id' => 'integer',
        'company_tag' => 'integer',
        'base_office_id' => 'integer',
        'vendor_id' => 'integer',
        'gvw' => 'float',
        'capacity' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'rto_reg_expiry' => 'datetime',
        'insurance_expiry' => 'datetime',
        'fitness_cert_expiry' => 'datetime',
        'active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the company that owns the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_tag');
    }

    /**
     * Get the base office that owns the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function baseOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'base_office_id');
    }

    /**
     * Get the vendor that owns the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the user who created the vehicle record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the vehicle record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
