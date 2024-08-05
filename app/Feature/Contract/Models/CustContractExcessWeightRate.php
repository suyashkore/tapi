<?php

namespace App\Feature\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Contract\Models\CustContract;

/**
 * Class CustContractExcessWeightRate
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int $tenant_id
 * @property int $cust_contract_id
 * @property string $ctr_num
 * @property float $lower_limit
 * @property float $upper_limit
 * @property float $rate
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CustContractExcessWeightRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_contract_excess_weight_rates';

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
        'cust_contract_id',
        'ctr_num',
        'lower_limit',
        'upper_limit',
        'rate',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the tenant that owns the excess weight rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the contract that owns the excess weight rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function custContract(): BelongsTo
    {
        return $this->belongsTo(CustContract::class, 'cust_contract_id');
    }

    /**
     * Get the user who created the excess weight rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the excess weight rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
