<?php

namespace App\Feature\Office\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Office\Models\Office;

/**
 * Class CpKyc
 *
 * @package App\Feature\CpKyc\Models
 * @property int $id
 * @property int $tenant_id
 * @property string|null $office_id
 * @property string $legal_name
 * @property string|null $gst_num
 * @property string|null $cin_num
 * @property string $pan_num
 * @property string $bank_name
 * @property string $bank_account_num
 * @property string $bank_ifsc_code
 * @property string|null $owner_aadhaar
 * @property string|null $owner_pan
 * @property string|null $owner_photo_url
 * @property string|null $owner_email
 * @property string|null $owner_mobile
 * @property string|null $finance_head_email
 * @property string|null $finance_head_mobile
 * @property bool $kyc_completed
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CpKyc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cp_kyc';

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
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'office_id',
        'legal_name',
        'gst_num',
        'cin_num',
        'pan_num',
        'bank_name',
        'bank_account_num',
        'bank_ifsc_code',
        'owner_aadhaar',
        'owner_pan',
        'owner_photo_url',
        'owner_email',
        'owner_mobile',
        'finance_head_email',
        'finance_head_mobile',
        'kyc_completed',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'kyc_completed' => 'boolean',
    ];

    /**
     * Get the tenant that owns the KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the office associated with the KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get the user who created the KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
