<?php

namespace App\Feature\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Feature\User\Models\Role;
use App\Feature\User\Models\Privilege;

/**
 * Class RolePrivilege
 *
 * @package App\Feature\User\Models
 * @property int $role_id
 * @property int $privilege_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class RolePrivilege extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_privileges';

    /**
     * The primary key associated with the table.
     *
     * @var string[]
     */
    protected $primaryKey = ['role_id', 'privilege_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing IDs.
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
        'role_id',
        'privilege_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the role that owns the role privilege.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the privilege that owns the role privilege.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function privilege(): BelongsTo
    {
        return $this->belongsTo(Privilege::class, 'privilege_id');
    }

    /**
     * Get the user that created the role privilege.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that updated the role privilege.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName(); // Get the primary key(s) of the model.
        if (!is_array($keys)) { // Check if the primary key is not an array (i.e., single primary key).
            return parent::setKeysForSaveQuery($query); // Call the parent method for handling single primary key.
        }

        foreach ($keys as $keyName) { // Loop through each key name if it's a composite primary key.
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName)); // Add a where clause for each key.
        }

        return $query; // Return the modified query builder instance.
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param  string|null  $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) { // Check if a specific key name is provided.
            $keyName = $this->getKeyName(); // If not, get the primary key name(s).
        }

        if (isset($this->original[$keyName])) { // Check if the original value of the key is set.
            return $this->original[$keyName]; // Return the original value of the key.
        }

        return $this->getAttribute($keyName); // Otherwise, return the current attribute value of the key.
    }
}
