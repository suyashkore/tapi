<?php

namespace App\Feature\Office\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\User;
use App\Feature\Office\Models\Office;
use App\Feature\Company\Models\Company;

/**
 * Class CpKyc
 *
 * @package App\Feature\Office\Models
 * @property int $id
 * @property int|null $tenant_id
 * @property string|null $company_tag
 * @property string $legal_name
 * @property string $owner1_name
 * @property string|null $photo1_url
 * @property string|null $owner1_aadhaar
 * @property string|null $owner1_aadhaar_url
 * @property string|null $owner1_pan
 * @property string|null $owner1_pan_url
 * @property string|null $owner1_email
 * @property string|null $owner1_mobile
 * @property string|null $owner2_name
 * @property string|null $photo2_url
 * @property string|null $owner2_aadhaar
 * @property string|null $owner2_aadhaar_url
 * @property string|null $owner2_pan
 * @property string|null $owner2_pan_url
 * @property string|null $owner2_email
 * @property string|null $owner2_mobile
 * @property string|null $country
 * @property string|null $state
 * @property string|null $district
 * @property string|null $taluka
 * @property string|null $city
 * @property string $pincode
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string $address
 * @property string|null $address_reg
 * @property string|null $addr_doc_url
 * @property string|null $gst_num
 * @property string|null $gst_cert_url
 * @property string|null $cin_num
 * @property string|null $company_reg_cert_url
 * @property string|null $pan_num
 * @property string|null $pan_card_url
 * @property string|null $tan_num
 * @property string|null $tan_card_url
 * @property string|null $msme_num
 * @property string|null $msme_reg_cert_url
 * @property string|null $aadhaar_num
 * @property string|null $aadhaar_card_url
 * @property string|null $bank1_name
 * @property string|null $bank1_accnt_holder
 * @property string|null $bank1_account_type
 * @property string|null $bank1_account_num
 * @property string|null $bank1_ifsc_code
 * @property string|null $bank1_doc_url
 * @property string|null $bank2_name
 * @property string|null $bank2_accnt_holder
 * @property string|null $bank2_account_type
 * @property string|null $bank2_account_num
 * @property string|null $bank2_ifsc_code
 * @property string|null $bank2_doc_url
 * @property \Illuminate\Support\Carbon|null $date_of_reg
 * @property string|null $doc1_name
 * @property string|null $doc1_url
 * @property \Illuminate\Support\Carbon|null $doc1_date
 * @property string|null $doc2_name
 * @property string|null $doc2_url
 * @property \Illuminate\Support\Carbon|null $doc2_date
 * @property string|null $doc3_name
 * @property string|null $doc3_url
 * @property \Illuminate\Support\Carbon|null $doc3_date
 * @property string|null $doc4_name
 * @property string|null $doc4_url
 * @property \Illuminate\Support\Carbon|null $doc4_date
 * @property string|null $key_personnel1_name
 * @property string|null $key_personnel1_job_title
 * @property string|null $key_personnel1_mobile
 * @property string|null $key_personnel1_email
 * @property string|null $key_personnel2_name
 * @property string|null $key_personnel2_job_title
 * @property string|null $key_personnel2_mobile
 * @property string|null $key_personnel2_email
 * @property string|null $key_personnel3_name
 * @property string|null $key_personnel3_job_title
 * @property string|null $key_personnel3_mobile
 * @property string|null $key_personnel3_email
 * @property string|null $key_personnel4_name
 * @property string|null $key_personnel4_job_title
 * @property string|null $key_personnel4_mobile
 * @property string|null $key_personnel4_email
 * @property \Illuminate\Support\Carbon|null $kyc_date
 * @property bool $kyc_completed
 * @property bool $active
 * @property string $status
 * @property string|null $note
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
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'company_tag',
        'legal_name',
        'owner1_name',
        'photo1_url',
        'owner1_aadhaar',
        'owner1_aadhaar_url',
        'owner1_pan',
        'owner1_pan_url',
        'owner1_email',
        'owner1_mobile',
        'owner2_name',
        'photo2_url',
        'owner2_aadhaar',
        'owner2_aadhaar_url',
        'owner2_pan',
        'owner2_pan_url',
        'owner2_email',
        'owner2_mobile',
        'country',
        'state',
        'district',
        'taluka',
        'city',
        'pincode',
        'latitude',
        'longitude',
        'address',
        'address_reg',
        'addr_doc_url',
        'gst_num',
        'gst_cert_url',
        'cin_num',
        'company_reg_cert_url',
        'pan_num',
        'pan_card_url',
        'tan_num',
        'tan_card_url',
        'msme_num',
        'msme_reg_cert_url',
        'aadhaar_num',
        'aadhaar_card_url',
        'bank1_name',
        'bank1_accnt_holder',
        'bank1_account_type',
        'bank1_account_num',
        'bank1_ifsc_code',
        'bank1_doc_url',
        'bank2_name',
        'bank2_accnt_holder',
        'bank2_account_type',
        'bank2_account_num',
        'bank2_ifsc_code',
        'bank2_doc_url',
        'date_of_reg',
        'doc1_name',
        'doc1_url',
        'doc1_date',
        'doc2_name',
        'doc2_url',
        'doc2_date',
        'doc3_name',
        'doc3_url',
        'doc3_date',
        'doc4_name',
        'doc4_url',
        'doc4_date',
        'key_personnel1_name',
        'key_personnel1_job_title',
        'key_personnel1_mobile',
        'key_personnel1_email',
        'key_personnel2_name',
        'key_personnel2_job_title',
        'key_personnel2_mobile',
        'key_personnel2_email',
        'key_personnel3_name',
        'key_personnel3_job_title',
        'key_personnel3_mobile',
        'key_personnel3_email',
        'key_personnel4_name',
        'key_personnel4_job_title',
        'key_personnel4_mobile',
        'key_personnel4_email',
        'kyc_date',
        'kyc_completed',
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
        'tenant_id' => 'integer',
        'company_tag' => 'integer',
        'kyc_completed' => 'boolean',
        'active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'kyc_date' => 'datetime',
        'date_of_reg' => 'datetime',
        'doc1_date' => 'datetime',
        'doc2_date' => 'datetime',
        'doc3_date' => 'datetime',
        'doc4_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the CP KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the offices associated with this CP KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offices(): HasMany
    {
        return $this->hasMany(Office::class, 'cp_kyc_id');
    }

    /**
     * Get the company that owns the CpKyc.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_tag');
    }

    /**
     * Get the user who created the CP KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the CP KYC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
