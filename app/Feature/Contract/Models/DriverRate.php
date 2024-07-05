<?php

namespace App\Feature\Contract\Models;

use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Office\Models\Office;
use App\Feature\Vendor\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class DriverRate
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property int $contracting_office_id
 * @property int|null $vendor_id
 * @property string $vendor_name
 * @property string $default_rate_type
 * @property float|null $daily_rate
 * @property float|null $hourly_rate
 * @property float|null $overtime_hourly_rate
 * @property float|null $daily_allowance
 * @property float|null $per_km_rate
 * @property float|null $per_extra_km_rate
 * @property float|null $night_halt_rate
 * @property float|null $per_trip_rate
 * @property float|null $trip_allowance
 * @property float|null $incentive_per_trip
 * @property float|null $monthly_sal
 * @property float|null $monthly_incentive
 * @property float|null $per_trip_penalty_percent
 * @property float|null $per_trip_penalty_fixed_amount
 * @property bool $active
 * @property string $status
 * @property string|null $note
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DriverRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_rates';

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
        'contracting_office_id',
        'vendor_id',
        'vendor_name',
        'default_rate_type',
        'daily_rate',
        'hourly_rate',
        'overtime_hourly_rate',
        'daily_allowance',
        'per_km_rate',
        'per_extra_km_rate',
        'night_halt_rate',
        'per_trip_rate',
        'trip_allowance',
        'incentive_per_trip',
        'monthly_sal',
        'monthly_incentive',
        'per_trip_penalty_percent',
        'per_trip_penalty_fixed_amount',
        'active',
        'status',
        'note',
        'start_date',
        'end_date',
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
        'contracting_office_id' => 'integer',
        'vendor_id' => 'integer',
        'daily_rate' => 'float',
        'hourly_rate' => 'float',
        'overtime_hourly_rate' => 'float',
        'daily_allowance' => 'float',
        'per_km_rate' => 'float',
        'per_extra_km_rate' => 'float',
        'night_halt_rate' => 'float',
        'per_trip_rate' => 'float',
        'trip_allowance' => 'float',
        'incentive_per_trip' => 'float',
        'monthly_sal' => 'float',
        'monthly_incentive' => 'float',
        'per_trip_penalty_percent' => 'float',
        'per_trip_penalty_fixed_amount' => 'float',
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the driver rate.
     *
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the contracting office that owns the driver rate.
     *
     * @return BelongsTo
     */
    public function contractingOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'contracting_office_id');
    }

    /**
     * Get the vendor that owns the driver rate.
     *
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created the driver rate.
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the driver rate.
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
