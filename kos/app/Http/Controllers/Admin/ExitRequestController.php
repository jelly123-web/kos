<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExitRequest;
use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExitRequestController extends Controller
{
    public function index()
    {
        $requests = ExitRequest::with(['tenant', 'tenant.room'])->orderByDesc('created_at')->get();
        return view('pages.admin.exits.index', compact('requests'));
    }

    public function approve(ExitRequest $exitRequest)
    {
        if ($exitRequest->status !== 'pending') {
            return redirect()->back()->with('info', 'Pengajuan sudah diproses.');
        }
        $tenant = Tenant::find($exitRequest->tenant_id);
        $room = $tenant?->room;
        $exitRequest->update(['status' => 'approved', 'approved_at' => Carbon::now()]);
        if ($tenant) {
            $tenant->update([
                'status' => 'inactive',
                'room_id' => null,
                'end_date' => Carbon::now()->toDateString(),
            ]);
        }
        if ($room) {
            $room->update(['status' => 'empty']);
        }
        return redirect()->back()->with('success', 'Pengajuan keluar disetujui. Penghuni diputus dari kamar.');
    }

    public function reject(ExitRequest $exitRequest)
    {
        if ($exitRequest->status !== 'pending') {
            return redirect()->back()->with('info', 'Pengajuan sudah diproses.');
        }
        $exitRequest->update(['status' => 'rejected', 'rejected_at' => Carbon::now()]);
        return redirect()->back()->with('success', 'Pengajuan keluar ditolak.');
    }
}

