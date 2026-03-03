<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomRequest;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('number')->get();
        return view('pages.admin.rooms.index', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|string|max:50|unique:rooms,number',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
        ]);
        $data['status'] = 'empty';
        Room::create($data);
        return redirect()->back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
        ]);
        $room->update($data);
        return redirect()->back()->with('success', 'Data kamar diperbarui.');
    }

    public function toggleStatus(Room $room)
    {
        $room->status = $room->status === 'empty' ? 'occupied' : 'empty';
        $room->save();
        return redirect()->back()->with('success', 'Status kamar diperbarui.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->back()->with('success', 'Kamar dihapus.');
    }

    public function requests()
    {
        $requests = RoomRequest::with(['tenant', 'room'])->orderByDesc('created_at')->get();
        if (view()->exists('pages.admin.rooms.requests')) {
            return view('pages.admin.rooms.requests', compact('requests'));
        }
        return response()->json($requests);
    }

    public function approveRequest(RoomRequest $requestModel)
    {
        $requestModel->load(['tenant', 'room']);
        if ($requestModel->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan sudah diproses.');
        }
        $tenant = $requestModel->tenant;
        $room = $requestModel->room;
        $tenant->update(['room_id' => $room->id, 'status' => 'active']);
        $room->update(['status' => 'occupied']);
        $requestModel->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Permintaan kamar disetujui.');
    }

    public function rejectRequest(RoomRequest $requestModel)
    {
        if ($requestModel->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan sudah diproses.');
        }
        $requestModel->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Permintaan kamar ditolak.');
    }
}
