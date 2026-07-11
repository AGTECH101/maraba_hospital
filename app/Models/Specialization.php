<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialization extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'specialization_service');
    }

    public function staffMembers(): BelongsToMany
    {
        return $this->belongsToMany(StaffMember::class, 'specialization_staff_member');
    }
}
