<?php

namespace App\Feature\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\Customer\Models\Customer;
use App\Feature\Station\Models\StationCoverage;
use App\Feature\User\Models\User;

/**
 * Class CustContractSlabRate
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int $tenant_id
 * @property int $cust_contract_id
 * @property string $ctr_num
 * @property string|null $zone
 * @property int|null $from_place_id
 * @property string $from_place
 * @property int|null $to_place_id
 * @property string $to_place
 * @property int|null $tat
 * @property array $slab_distance_type
 * @property string $slab_contract_type
 * @property float $slab1
 * @property float $slab2
 * @property float $slab3
 * @property float $slab4
 * @property float $slab5
 * @property float $slab6
 * @property float $slab7
 * @property float $slab8
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CustContractSlabRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_contract_slab_rates';

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
        'zone',
        'from_place_id',
        'from_place',
        'to_place_id',
        'to_place',
        'tat',
        'slab_distance_type',
        'slab_contract_type',
        'slab1',
        'slab2',
        'slab3',
        'slab4',
        'slab5',
        'slab6',
        'slab7',
        'slab8',
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
        'slab1' => 'float',
        'slab2' => 'float',
        'slab3' => 'float',
        'slab4' => 'float',
        'slab5' => 'float',
        'slab6' => 'float',
        'slab7' => 'float',
        'slab8' => 'float',
    ];

    /**
     * Get the tenant that owns the slab rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the contract that owns the slab rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(CustContract::class, 'cust_contract_id');
    }

    /**
     * Get the user who created the slab rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the slab rate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the from place coverage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromPlace(): BelongsTo
    {
        return $this->belongsTo(StationCoverage::class, 'from_place_id');
    }

    /**
     * Get the to place coverage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toPlace(): BelongsTo
    {
        return $this->belongsTo(StationCoverage::class, 'to_place_id');
    }
}
