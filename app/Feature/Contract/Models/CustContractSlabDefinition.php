<?php

namespace App\Feature\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\Contract\Models\CustContract;
use App\Feature\User\Models\User;
use App\Feature\Tenant\Models\Tenant;

/**
 * Class CustContractSlabDefinition
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int $tenant_id
 * @property int $cust_contract_id
 * @property string $ctr_num
 * @property array $slab_distance_type
 * @property string $slab_contract_type
 * @property string $slab_rate_type
 * @property string $slab_number
 * @property int $slab_lower_limit
 * @property int $slab_upper_limit
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CustContractSlabDefinition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_contract_slab_definitions';

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
        'slab_distance_type',
        'slab_contract_type',
        'slab_rate_type',
        'slab_number',
        'slab_lower_limit',
        'slab_upper_limit',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'slab_distance_type' => 'array',
        'slab_lower_limit' => 'integer',
        'slab_upper_limit' => 'integer',
    ];

    /**
     * Get the tenant that owns the slab definition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the customer contract that owns the slab definition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function custContract(): BelongsTo
    {
        return $this->belongsTo(CustContract::class, 'cust_contract_id');
    }

    /**
     * Get the user who created the slab definition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the slab definition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
