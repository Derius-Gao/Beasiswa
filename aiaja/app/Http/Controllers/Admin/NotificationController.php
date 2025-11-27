<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:100',
            'channel' => 'required|string|in:app,email,whatsapp,sms',
            'message' => 'required|string',
            'data' => 'nullable',
        ]);

        $payload = $validated;
        if (!empty($payload['data'])) {
            $decoded = json_decode($payload['data'], true);
            $payload['data'] = json_last_error() === JSON_ERROR_NONE ? $decoded : ['raw' => $payload['data']];
        } else {
            $payload['data'] = null;
        }

        Notification::create($payload + [
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification created and logged.');
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'is_read' => 'required|boolean',
        ]);

        $notification->update(['is_read' => $request->boolean('is_read')]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification status updated.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification deleted.');
    }
}


