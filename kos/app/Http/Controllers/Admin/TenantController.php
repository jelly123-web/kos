<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('room')->orderBy('name')->get();
        $rooms = Room::orderBy('number')->get();
        return view('pages.admin.tenants.index', compact('tenants', 'rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'room_id' => 'nullable|exists:rooms,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
        $data['status'] = 'active';
        $tenant = Tenant::create($data);
        if ($tenant->room_id) {
            $tenant->room->update(['status' => 'occupied']);
        }
        return redirect()->back()->with('success', 'Penghuni ditambahkan.');
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'phone' => 'nullable|string|max:50',
            'room_id' => 'nullable|exists:rooms,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);
        $tenant->update($data);
        if ($tenant->room_id) {
            $tenant->room->update(['status' => 'occupied']);
        }
        return redirect()->back()->with('success', 'Data penghuni diperbarui.');
    }

    public function destroy(Tenant $tenant)
    {
        $room = $tenant->room;
        $tenant->delete();
        if ($room) {
            $room->update(['status' => 'empty']);
        }
        return redirect()->back()->with('success', 'Penghuni dihapus.');
    }
}
