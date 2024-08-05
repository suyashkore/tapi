<?php

namespace App\Feature\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Contract\Models\CustContract;
use App\Feature\Station\Models\StationCoverage;

/**
 * Class CustContractOdaCharges
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int $tenant_id
 * @property int $cust_contract_id
 * @property string $ctr_num
 * @property int|null $from_place_id
 * @property string $from_place
 * @property int|null $to_place_id
 * @property string $to_place
 * @property float $rate
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CustContractOdaCharges extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cust_contract_oda_charges';

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
        'from_place_id',
        'from_place',
        'to_place_id',
        'to_place',
        'rate',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the tenant that owns the ODA charge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the customer contract that owns the ODA charge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function custContract(): BelongsTo
    {
        return $this->belongsTo(CustContract::class, 'cust_contract_id');
    }

    /**
     * Get the originating place (station coverage) that owns the ODA charge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromPlace(): BelongsTo
    {
        return $this->belongsTo(StationCoverage::class, 'from_place_id');
    }

    /**
     * Get the destination place (station coverage) that owns the ODA charge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toPlace(): BelongsTo
    {
        return $this->belongsTo(StationCoverage::class, 'to_place_id');
    }

    /**
     * Get the user who created the ODA charge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the ODA charge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
