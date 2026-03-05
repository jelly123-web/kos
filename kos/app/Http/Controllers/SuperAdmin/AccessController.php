<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Support\PermissionRegistry;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function index()
    {
        $perms = PermissionRegistry::all();
        $roles = ['admin','owner','manager','staff','tenant'];
        $matrix = [];
        foreach ($perms as $key => $label) {
            $row = ['label' => $label];
            foreach ($roles as $r) {
                $row[$r] = RolePermission::where('role',$r)->where('perm_key',$key)->value('allowed');
                if ($row[$r] === null) $row[$r] = true;
            }
            $matrix[$key] = $row;
        }
        return view('pages.super-admin.access.index', compact('perms','roles','matrix'));
    }

    public function save(Request $request)
    {
        $roles = ['admin','owner','manager','staff','tenant'];
        $perms = PermissionRegistry::all();
        foreach ($roles as $r) {
            $submitted = (array) $request->input("perm.$r", []);
            foreach ($perms as $key => $label) {
                $allowed = array_key_exists($key, $submitted) && (bool) $submitted[$key];
                RolePermission::updateOrCreate(
                    ['role' => $r, 'perm_key' => $key],
                    ['allowed' => $allowed]
                );
            }
        }
        return redirect()->back()->with('success', 'Hak akses diperbarui.');
    }
}
