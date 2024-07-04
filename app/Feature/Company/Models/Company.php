<?php

namespace App\Feature\Company\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;

/**
 * Class Company
 *
 * @package App\Feature\Company\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string $code
 * @property string $name
 * @property string|null $name_reg
 * @property string $address
 * @property string|null $address_reg
 * @property string|null $phone1
 * @property string|null $phone2
 * @property string|null $email1
 * @property string|null $email2
 * @property string|null $website
 * @property string|null $gst_num
 * @property string|null $cin_num
 * @property string|null $msme_num
 * @property string|null $pan_num
 * @property string|null $tan_num
 * @property string|null $logo_url
 * @property bool $active
 * @property int $seq_num
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

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
        'code',
        'name',
        'name_reg',
        'address',
        'address_reg',
        'phone1',
        'phone2',
        'email1',
        'email2',
        'website',
        'gst_num',
        'cin_num',
        'msme_num',
        'pan_num',
        'tan_num',
        'logo_url',
        'active',
        'seq_num',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'seq_num' => 'integer',
    ];

    /**
     * Get the tenant that owns the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the user who created the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
