<?php

namespace App\Feature\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\Company\Models\Company;
use App\Feature\Customer\Models\Customer;
use App\Feature\User\Models\User;
use App\Feature\Contract\Models\CustContractSlabDefinition;
use App\Feature\Contract\Models\CustContractSlabRate;
use App\Feature\Contract\Models\CustContractExcessWeightRate;
use App\Feature\Contract\Models\CustContractOdaCharges;

/**
 * Class CustContract
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int $tenant_id
 * @property int|null $company_tag
 * @property string $ctr_num
 * @property int|null $customer_group_id
 * @property int $customer_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property array $payment_type
 * @property array $load_type
 * @property array $distance_type
 * @property array $rate_type
 * @property array $pickup_delivery_mode
 * @property bool $excess_wt_chargeable
 * @property bool $oda_del_chargeable
 * @property int $credit_period
 * @property float $docu_charges_per_invoice
 * @property float $loading_charges_per_pkg
 * @property float $fuel_surcharge
 * @property float $oda_min_del_charges
 * @property float $reverse_pick_up_charges
 * @property float $insurance_charges
 * @property float $minimum_chargeable_wt
 * @property bool $active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CustContract extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_contracts';

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
        'ctr_num',
        'customer_group_id',
        'customer_id',
        'start_date',
        'end_date',
        'payment_type',
        'load_type',
        'distance_type',
        'rate_type',
        'pickup_delivery_mode',
        'excess_wt_chargeable',
        'oda_del_chargeable',
        'credit_period',
        'docu_charges_per_invoice',
        'loading_charges_per_pkg',
        'fuel_surcharge',
        'oda_min_del_charges',
        'reverse_pick_up_charges',
        'insurance_charges',
        'minimum_chargeable_wt',
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
        'payment_type' => 'array',
        'load_type' => 'array',
        'distance_type' => 'array',
        'rate_type' => 'array',
        'pickup_delivery_mode' => 'array',
        'excess_wt_chargeable' => 'boolean',
        'oda_del_chargeable' => 'boolean',
        'credit_period' => 'integer',
        'docu_charges_per_invoice' => 'float',
        'loading_charges_per_pkg' => 'float',
        'fuel_surcharge' => 'float',
        'oda_min_del_charges' => 'float',
        'reverse_pick_up_charges' => 'float',
        'insurance_charges' => 'float',
        'minimum_chargeable_wt' => 'float',
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the tenant that owns the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the company associated with this.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_tag');
    }

    /**
     * Get the customer group that owns the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_group_id');
    }

    /**
     * Get the customer that owns the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the user who created the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the slab definitions for the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function slabDefinitions(): HasMany
    {
        return $this->hasMany(CustContractSlabDefinition::class, 'cust_contract_id');
    }

    /**
     * Get the slab rates for the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function slabRates(): HasMany
    {
        return $this->hasMany(CustContractSlabRate::class, 'cust_contract_id');
    }

    /**
     * Get the excess weight rates for the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function excessWeightRates(): HasMany
    {
        return $this->hasMany(CustContractExcessWeightRate::class, 'cust_contract_id');
    }

    /**
     * Get the ODA charges for the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function odaCharges(): HasMany
    {
        return $this->hasMany(CustContractOdaCharges::class, 'cust_contract_id');
    }
}
