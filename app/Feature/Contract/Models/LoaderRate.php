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
 * Class LoaderRate
 *
 * @package App\Feature\Contract\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property int $contracting_office_id
 * @property int|null $vendor_id
 * @property string $vendor_name
 * @property string $default_rate_type
 * @property float|null $reg_pkg_rate
 * @property float|null $crossing_pkg_rate
 * @property float|null $reg_weight_rate
 * @property float|null $crossing_weight_rate
 * @property float|null $monthly_sal
 * @property float|null $daily_allowance
 * @property float|null $daily_wage
 * @property int|null $daily_wage_pkg_capping
 * @property int|null $daily_wage_weight_capping
 * @property float|null $overtime_hourly_rate
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
class LoaderRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loader_rates';

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
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tenant_id' => 'integer',
        'contracting_office_id' => 'integer',
        'vendor_id' => 'integer',
        'reg_pkg_rate' => 'float',
        'crossing_pkg_rate' => 'float',
        'reg_weight_rate' => 'float',
        'crossing_weight_rate' => 'float',
        'monthly_sal' => 'float',
        'daily_allowance' => 'float',
        'daily_wage' => 'float',
        'daily_wage_pkg_capping' => 'integer',
        'daily_wage_weight_capping' => 'integer',
        'overtime_hourly_rate' => 'float',
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the loader rate.
     *
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the contracting office that owns the loader rate.
     *
     * @return BelongsTo
     */
    public function contractingOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'contracting_office_id');
    }

    /**
     * Get the vendor that owns the loader rate.
     *
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created the loader rate.
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the loader rate.
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
