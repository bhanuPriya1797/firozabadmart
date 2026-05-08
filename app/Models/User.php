<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getImageAttribute($value): array
    {
        if (!empty($value)) {
            $imagePath = public_path('storage/uploads/users/' . $value);

            if (file_exists($imagePath)) {
                return [
                    'path' => asset('storage/uploads/users/' . $value),
                    'name' => $value
                ];
            } else {
                return [
                    'path' => asset('admin/assets/img/blank_user.jpg'),
                    'name' => $value
                ];
            }
        }

        return [
            'path' => asset('admin/assets/img/blank_user.jpg'),
            'name' => ''
        ];
    }

    public function getRoleAttribute(): string
    {
        return $this->getRoleNames()->first() ?? '';
    }

    public function getEncryptIdAttribute(): string
    {
        return CustomHelper::encrypt($this->id);
    }

    public static function getPermissionGroups()
    {
        $permission_groups = DB::table('permissions')
            ->select('group_name as name')
            ->where('status', 1) // Only active permissions
            ->groupBy('group_name')
            ->get();
        return $permission_groups;
    }

    public static function getPermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->where('status', 1) // Only active permissions
            ->get();
        return $permissions;
    }

}
