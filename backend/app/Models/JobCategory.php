<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function jobTitles(): HasMany
    {
        return $this->hasMany(JobTitle::class);
    }
}
