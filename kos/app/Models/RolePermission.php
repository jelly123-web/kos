<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Traits\SoftDeleteWithMeta;

class RolePermission extends Model
{
    use SoftDeleteWithMeta;
    protected $fillable = ['role', 'perm_key', 'allowed'];

    public static function allows(string $role, string $key): bool
    {
        if ($role === 'super_admin') {
            return true;
        }
        $cacheKey = "perm:$role:$key";
        return Cache::remember($cacheKey, 60, function () use ($role, $key) {
            $rec = self::where('role', $role)->where('perm_key', $key)->first();
            return $rec ? (bool) $rec->allowed : true;
        });
    }
}
