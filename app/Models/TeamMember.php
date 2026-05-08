<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\CustomHelper;

class TeamMember extends Model
{
    protected $table = 'team_members';

    protected $fillable = [
        'name',
        'designation',
        'bio',
        'image',
        'featured',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('status', 1);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return asset('admin/assets/img/blank_user.jpg');
        }
        if (\Illuminate\Support\Str::startsWith($this->image, 'media/')) {
            return asset('storage/' . $this->image);
        }
        return CustomHelper::getImageUrl($this->image, 'uploads/team', 'large');
    }
}
