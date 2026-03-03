<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function reports()
    {
        // Mock data for reports
        $reports = [
            'total_revenue' => 'Rp 158.400.000',
            'revenue_this_month' => 'Rp 24.500.000',
            'occupancy_rate' => '88%',
            'active_properties' => 12,
            'top_properties' => [
                ['name' => 'Kos Melati', 'revenue' => 'Rp 45.000.000', 'occupancy' => '95%'],
                ['name' => 'Kos Mawar', 'revenue' => 'Rp 32.500.000', 'occupancy' => '100%'],
                ['name' => 'Kos Anggrek', 'revenue' => 'Rp 28.900.000', 'occupancy' => '80%'],
            ]
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
            ->whereIn('role', ['admin', 'owner', 'staff'])
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
