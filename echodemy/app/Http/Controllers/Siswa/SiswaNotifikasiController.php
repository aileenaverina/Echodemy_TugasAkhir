<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Notif;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaNotifikasiController extends Controller
{
    public function index(): View
    {
        $notifs = auth()->user()->notifs()->latest()->get();

        return view('siswa.notifikasi.index', compact('notifs'));
    }

    public function toggle(Notif $notif): JsonResponse
    {
        abort_unless($notif->user_id === auth()->id(), 403);

        $notif->update(['is_read' => ! $notif->is_read]);

        return response()->json(['is_read' => $notif->is_read]);
    }
}
