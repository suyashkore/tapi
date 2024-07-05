<?php

namespace App\Feature\Station\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\Office\Models\Office;
use App\Feature\User\Models\User;

/**
 * Class StationCoverage
 *
 * @package App\Feature\Station\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string $name
 * @property string|null $name_reg
 * @property string|null $post_name
 * @property string|null $post_name_reg
 * @property string $pincode
 * @property string|null $taluka
 * @property string|null $taluka_reg
 * @property string $district
 * @property string|null $district_reg
 * @property string $state
 * @property string|null $state_reg
 * @property string $country
 * @property string $latitude
 * @property string $longitude
 * @property int $servicing_office_id
 * @property int|null $service_office_tat
 * @property int|null $servicing_office_dist
 * @property string|null $name_gmap
 * @property string|null $zone
 * @property string|null $route_num
 * @property int|null $route_sequence
 * @property bool $oda
 * @property string|null $nr_state_highway
 * @property string|null $nr_national_highway
 * @property bool $active
 * @property string $status
 * @property string|null $note
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class StationCoverage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'station_coverage';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
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
        'name',
        'name_reg',
        'post_name',
        'post_name_reg',
        'pincode',
        'taluka',
        'taluka_reg',
        'district',
        'district_reg',
        'state',
        'state_reg',
        'country',
        'latitude',
        'longitude',
        'servicing_office_id',
        'service_office_tat',
        'servicing_office_dist',
        'name_gmap',
        'zone',
        'route_num',
        'route_sequence',
        'oda',
        'nr_state_highway',
        'nr_national_highway',
        'active',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'oda' => 'boolean',
        'active' => 'boolean',
        'servicing_office_id' => 'integer',
        'service_office_tat' => 'integer',
        'servicing_office_dist' => 'integer',
        'route_sequence' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the station coverage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the servicing office for the station coverage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function servicingOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'servicing_office_id');
    }

    /**
     * Get the user who created the station coverage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the station coverage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
