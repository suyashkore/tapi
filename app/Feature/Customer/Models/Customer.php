<?php

namespace App\Feature\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\Office\Models\Office;
use App\Feature\Company\Models\Company;
use App\Feature\User\Models\User;

/**
 * Class Customer
 *
 * @package App\Feature\Customer\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string|null $company_tag
 * @property string|null $parent_id
 * @property string $code
 * @property string $name
 * @property string|null $name_reg
 * @property array $payment_types
 * @property string|null $industry_type
 * @property string $c_type
 * @property string|null $c_subtype
 * @property string|null $pan_num
 * @property string|null $gst_num
 * @property string|null $country
 * @property string|null $state
 * @property string|null $district
 * @property string|null $taluka
 * @property string|null $city
 * @property string|null $pincode
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $address
 * @property string|null $address_reg
 * @property string|null $mobile
 * @property string|null $tel_num
 * @property string|null $email
 * @property string|null $billing_contact_person
 * @property string $billing_mobile
 * @property string $billing_email
 * @property string $billing_address
 * @property string|null $billing_address_reg
 * @property string $primary_servicing_office
 * @property array|null $other_servicing_offices
 * @property Carbon|null $erp_entry_date
 * @property bool $active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

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
        'parent_id',
        'code',
        'name',
        'name_reg',
        'payment_types',
        'industry_type',
        'c_type',
        'c_subtype',
        'pan_num',
        'gst_num',
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
        'mobile',
        'tel_num',
        'email',
        'billing_contact_person',
        'billing_mobile',
        'billing_email',
        'billing_address',
        'billing_address_reg',
        'primary_servicing_office_id',
        'other_servicing_offices',
        'erp_entry_date',
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
        'company_tag' => 'integer',
        'payment_types' => 'array',
        'primary_servicing_office_id' => 'integer',
        'other_servicing_offices' => 'array',
        'erp_entry_date' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the company that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_tag');
    }

    /**
     * Get the parent customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child customers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the primary servicing office that serves the Customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryServicingOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'primary_servicing_office_id');
    }

    /**
     * Get the user who created the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
