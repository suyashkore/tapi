<?php

namespace App\Feature\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Office\Models\Office;

/**
 * Class Customer
 *
 * @package App\Feature\Customer\Models
 * @property string $id
 * @property int $tenant_id
 * @property string|null $parent_id
 * @property string $name
 * @property string|null $name_reg
 * @property string $payment_types
 * @property string|null $industry_type
 * @property string|null $customer_type
 * @property string|null $pan
 * @property string|null $gst_num
 * @property string|null $cin_num
 * @property string|null $country
 * @property string|null $state
 * @property string|null $district
 * @property string $city
 * @property string $pincode
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
 * @property string|null $other_servicing_offices
 * @property string $primary_servicing_office
 * @property \Illuminate\Support\Carbon|null $erp_entry_date
 * @property bool $active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'tenant_id',
        'parent_id',
        'name',
        'name_reg',
        'payment_types',
        'industry_type',
        'customer_type',
        'pan',
        'gst_num',
        'cin_num',
        'country',
        'state',
        'district',
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
        'other_servicing_offices',
        'primary_servicing_office',
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
        'payment_types' => 'json',
        'other_servicing_offices' => 'json',
        'active' => 'boolean',
        'erp_entry_date' => 'datetime',
    ];

    /**
     * Get the tenant that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
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

    /**
     * Get the parent customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'parent_id');
    }

    /**
     * Get the child customers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Customer::class, 'parent_id');
    }

    /**
     * Get the primary servicing office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function primaryServicingOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'primary_servicing_office');
    }
}
