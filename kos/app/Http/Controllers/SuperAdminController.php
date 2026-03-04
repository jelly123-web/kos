<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // Data for dashboard
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'owners' => User::where('role', 'owner')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'tenants' => User::where('role', 'tenant')->count(),
        ];

        return view('pages.dashboard.ecommerce', compact('stats'));
    }

    public function users()
    {
        $users = User::all();
        return view('pages.users.index', compact('users'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,owner,staff,tenant',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->role === 'super_admin') {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status Super Admin.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil di-reset.');
    }

    public function settings()
    {
        $settings = [
            'app_name' => Setting::getValue('app_name', 'Kos Management System'),
            'app_slogan' => Setting::getValue('app_slogan', 'Kelola Kos Jadi Lebih Mudah'),
            'app_rules' => Setting::getValue('app_rules', "1. Pembayaran maksimal tanggal 5 setiap bulan.\n2. Tamu dilarang menginap tanpa ijin pengelola.\n3. Menjaga kebersihan dan ketenangan area kos."),
            'deposit_fee' => Setting::getValue('deposit_fee', '500000'),
            'late_fee_percent' => Setting::getValue('late_fee_percent', '5'),
            'electricity_fee' => Setting::getValue('electricity_fee', '150000'),
            'water_fee' => Setting::getValue('water_fee', '50000'),
            'security_session_timeout' => Setting::getValue('security_session_timeout', '120'),
            'security_max_attempts' => Setting::getValue('security_max_attempts', '5'),
        ];

        return view('pages.super-admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_slogan' => 'nullable|string|max:255',
            'app_rules' => 'nullable|string',
            'deposit_fee' => 'required|numeric|min:0',
            'late_fee_percent' => 'required|numeric|min:0|max:100',
            'electricity_fee' => 'required|numeric|min:0',
            'water_fee' => 'required|numeric|min:0',
            'security_session_timeout' => 'required|numeric|min:1',
            'security_max_attempts' => 'required|numeric|min:1',
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function activityLog()
    {
        return view('pages.super-admin.activity-log');
    }

    public function maintenance()
    {
        $logPreview = '';
        try {
            $path = storage_path('logs/laravel.log');
            if (file_exists($path)) {
                $lines = @file($path);
                $logPreview = implode('', array_slice($lines, -150));
            }
        } catch (\Throwable $e) {}
        return view('pages.super-admin.maintenance.index', compact('logPreview'));
    }

    public function backup()
    {
        $dump = [
            'properties' => \App\Models\Property::all(),
            'rooms' => \App\Models\Room::all(),
            'tenants' => \App\Models\Tenant::all(),
            'payments' => \App\Models\Payment::all(),
            'issues' => \App\Models\IssueReport::all(),
            'inspections' => \App\Models\RoomInspection::all(),
            'settings' => \App\Models\Setting::all(),
        ];
        $json = json_encode($dump, JSON_PRETTY_PRINT);
        $filename = 'backup_'.now()->format('Ymd_His').'.json';
        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimetypes:application/json,text/plain',
        ]);
        $content = file_get_contents($request->file('backup_file')->getRealPath());
        $data = json_decode($content, true);
        if (!is_array($data)) {
            return redirect()->back()->with('error', 'File backup tidak valid.');
        }
        DB::transaction(function () use ($data) {
            foreach (['properties','rooms','tenants','payments','issues','inspections','settings'] as $key) {
                if (!empty($data[$key]) && is_array($data[$key])) {
                    foreach ($data[$key] as $row) {
                        $model = match ($key) {
                            'properties' => new \App\Models\Property(),
                            'rooms' => new \App\Models\Room(),
                            'tenants' => new \App\Models\Tenant(),
                            'payments' => new \App\Models\Payment(),
                            'issues' => new \App\Models\IssueReport(),
                            'inspections' => new \App\Models\RoomInspection(),
                            'settings' => new \App\Models\Setting(),
                        };
                        $attrs = $row;
                        unset($attrs['id']);
                        $model->fill($attrs);
                        $model->save();
                    }
                }
            }
        });
        return redirect()->back()->with('success', 'Data berhasil direstore.');
    }

    public function systemUpdate()
    {
        return redirect()->back()->with('success', 'Sistem diperbarui (placeholder).');
    }

    public function reports()
    {
        $nf = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');

        $totalRevenue = \App\Models\Payment::where('status', 'paid')->sum('amount');
        $revenueThisMonth = \App\Models\Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $roomsTotal = \App\Models\Room::count();
        $roomsOccupied = \App\Models\Room::where('status', 'occupied')->count();
        $occupancyRate = $roomsTotal > 0 ? round(($roomsOccupied / $roomsTotal) * 100) : 0;

        $activeProperties = \App\Models\Property::count();

        $byProperty = \App\Models\Payment::select(
                \DB::raw('rooms.property_id as pid'),
                \DB::raw('SUM(payments.amount) as revenue')
            )
            ->join('rooms', 'payments.room_id', '=', 'rooms.id')
            ->where('payments.status', 'paid')
            ->groupBy('pid')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $topProperties = [];
        foreach ($byProperty as $row) {
            $prop = \App\Models\Property::find($row->pid);
            if (!$prop) continue;
            $rt = \App\Models\Room::where('property_id', $prop->id)->count();
            $ro = \App\Models\Room::where('property_id', $prop->id)->where('status', 'occupied')->count();
            $occ = $rt > 0 ? round(($ro / $rt) * 100) : 0;
            $topProperties[] = [
                'name' => $prop->name,
                'revenue' => $nf($row->revenue ?? 0),
                'occupancy' => $occ.'%',
            ];
        }

        if (empty($topProperties)) {
            $fallback = \App\Models\Property::orderBy('name')->limit(3)->get();
            foreach ($fallback as $prop) {
                $rt = \App\Models\Room::where('property_id', $prop->id)->count();
                $ro = \App\Models\Room::where('property_id', $prop->id)->where('status', 'occupied')->count();
                $occ = $rt > 0 ? round(($ro / $rt) * 100) : 0;
                $topProperties[] = [
                    'name' => $prop->name,
                    'revenue' => $nf(0),
                    'occupancy' => $occ.'%',
                ];
            }
        }

        $reports = [
            'total_revenue' => $nf($totalRevenue),
            'revenue_this_month' => $nf($revenueThisMonth),
            'occupancy_rate' => $occupancyRate.'%',
            'active_properties' => $activeProperties,
            'top_properties' => $topProperties,
        ];

        return view('pages.super-admin.reports', compact('reports'));
    }

    public function lineChart()
    {
        return view('pages.charts.line-chart');
    }

    public function barChart()
    {
        return view('pages.charts.bar-chart');
    }

    public function chat(Request $request)
    {
        $contacts = User::where('id', '!=', auth()->id())
            ->whereIn('role', ['admin', 'owner', 'staff', 'manager'])
            ->get();

        $unread = Message::select('sender_id', \DB::raw('COUNT(*) as c'))
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->pluck('c', 'sender_id');

        $selectedContact = null;
        $messages = collect();

        if ($request->has('user_id')) {
            $selectedContact = User::findOrFail($request->user_id);
            $messages = Message::where(function ($query) use ($selectedContact) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $selectedContact->id);
            })->orWhere(function ($query) use ($selectedContact) {
                $query->where('sender_id', $selectedContact->id)
                    ->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();

            // Mark as read
            Message::where('sender_id', $selectedContact->id)
                ->where('receiver_id', auth()->id())
                ->update(['is_read' => true]);
        }

        return view('pages.super-admin.chat', compact('contacts', 'selectedContact', 'messages', 'unread'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function fetchMessages(User $user)
    {
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
