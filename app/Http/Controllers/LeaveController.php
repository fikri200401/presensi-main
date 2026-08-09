<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user', 'approverKadiv', 'approverHr', 'approverDireksi']);
        $user = auth()->user();

        // Role-based visibility
        if ($user->hasRole('employee')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('kepala_divisi')) {
            // Kepala Divisi: own leaves + leaves from same division employees
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('user', function ($q2) use ($user) {
                      $q2->where('division', $user->division);
                  });
            });
        }
        // direksi, admin, super_admin see all

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->latest()->paginate(15);

        return view('leave.index', compact('leaves'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole(['super_admin', 'admin'])) {
            $users = User::all();
        } else {
            $users = User::where('id', $user->id)->get();
        }

        return view('leave.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $request->merge([
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|string',
        ]);

        Leave::create($validated);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil dibuat');
    }

    public function edit(Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['super_admin', 'admin']) && $leave->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $users = $user->hasRole(['super_admin', 'admin'])
            ? User::all()
            : User::where('id', $user->id)->get();

        return view('leave.edit', compact('leave', 'users'));
    }

    public function update(Request $request, Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['super_admin', 'admin']) && $leave->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$user->hasRole(['super_admin', 'admin'])) {
            $request->merge(['user_id' => $leave->user_id]);
            if ($leave->status !== 'pending') {
                return redirect()->route('leave.index')
                    ->with('error', 'Pengajuan yang sudah diproses tidak bisa diubah.');
            }
            $request->merge(['status' => 'pending']);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|string',
        ]);

        $leave->update($validated);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil diperbarui');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil dihapus');
    }

    /**
     * Approve leave - Layer 1: Kepala Divisi
     */
    public function approveKadiv(Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['kepala_divisi', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki akses untuk approval tahap ini.');
        }

        // Kepala Divisi hanya boleh approve cuti karyawan di divisi yang sama
        if ($user->hasRole('kepala_divisi') && $leave->user->division !== $user->division) {
            abort(403, 'Anda hanya bisa menyetujui cuti karyawan di divisi Anda.');
        }

        if ($leave->status !== Leave::STATUS_PENDING) {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu approval Kepala Divisi.');
        }

        $leave->update([
            'status' => Leave::STATUS_APPROVED_KADIV,
            'approved_by_kadiv' => $user->id,
            'approved_at_kadiv' => now(),
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti disetujui oleh Kepala Divisi. Menunggu approval HR.');
    }

    /**
     * Reject leave - Layer 1: Kepala Divisi
     */
    public function rejectKadiv(Request $request, Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['kepala_divisi', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki akses untuk menolak pada tahap ini.');
        }

        // Kepala Divisi hanya boleh reject cuti karyawan di divisi yang sama
        if ($user->hasRole('kepala_divisi') && $leave->user->division !== $user->division) {
            abort(403, 'Anda hanya bisa menolak cuti karyawan di divisi Anda.');
        }

        if ($leave->status !== Leave::STATUS_PENDING) {
            return back()->with('error', 'Pengajuan ini tidak dalam status menunggu approval Kepala Divisi.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $leave->update([
            'status' => Leave::STATUS_REJECTED_KADIV,
            'approved_by_kadiv' => $user->id,
            'approved_at_kadiv' => now(),
            'note_kadiv' => $validated['rejection_reason'],
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti ditolak oleh Kepala Divisi.');
    }

    /**
     * Approve leave - Layer 2: HR / Admin
     */
    public function approveHr(Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki akses untuk approval tahap ini.');
        }

        if ($leave->status !== Leave::STATUS_APPROVED_KADIV) {
            return back()->with('error', 'Pengajuan ini belum disetujui oleh Kepala Divisi.');
        }

        $leave->update([
            'status' => Leave::STATUS_APPROVED_HR,
            'approved_by_hr' => $user->id,
            'approved_at_hr' => now(),
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti disetujui oleh HR. Menunggu approval Direksi.');
    }

    /**
     * Reject leave - Layer 2: HR / Admin
     */
    public function rejectHr(Request $request, Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki akses untuk menolak pada tahap ini.');
        }

        if ($leave->status !== Leave::STATUS_APPROVED_KADIV) {
            return back()->with('error', 'Pengajuan ini belum disetujui oleh Kepala Divisi.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $leave->update([
            'status' => Leave::STATUS_REJECTED_HR,
            'approved_by_hr' => $user->id,
            'approved_at_hr' => now(),
            'note_hr' => $validated['rejection_reason'],
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti ditolak oleh HR.');
    }

    /**
     * Approve leave - Layer 3: Direksi (Final)
     */
    public function approveDireksi(Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['direksi', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki akses untuk approval final.');
        }

        if ($leave->status !== Leave::STATUS_APPROVED_HR) {
            return back()->with('error', 'Pengajuan ini belum disetujui oleh HR.');
        }

        $leave->update([
            'status' => Leave::STATUS_APPROVED,
            'approved_by_direksi' => $user->id,
            'approved_at_direksi' => now(),
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti disetujui secara final oleh Direksi.');
    }

    /**
     * Reject leave - Layer 3: Direksi
     */
    public function rejectDireksi(Request $request, Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole(['direksi', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki akses untuk menolak pada tahap ini.');
        }

        if ($leave->status !== Leave::STATUS_APPROVED_HR) {
            return back()->with('error', 'Pengajuan ini belum disetujui oleh HR.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $leave->update([
            'status' => Leave::STATUS_REJECTED_DIREKSI,
            'approved_by_direksi' => $user->id,
            'approved_at_direksi' => now(),
            'note_direksi' => $validated['rejection_reason'],
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti ditolak oleh Direksi.');
    }

    /**
     * Legacy approve - for super_admin quick approve (all layers at once)
     */
    public function approve(Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin')) {
            abort(403, 'Unauthorized action.');
        }

        $leave->update([
            'status' => Leave::STATUS_APPROVED,
            'approved_by_kadiv' => $leave->approved_by_kadiv ?? $user->id,
            'approved_at_kadiv' => $leave->approved_at_kadiv ?? now(),
            'approved_by_hr' => $leave->approved_by_hr ?? $user->id,
            'approved_at_hr' => $leave->approved_at_hr ?? now(),
            'approved_by_direksi' => $user->id,
            'approved_at_direksi' => now(),
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti disetujui (fast-track oleh Super Admin).');
    }

    /**
     * Legacy reject - for super_admin
     */
    public function reject(Request $request, Leave $leave)
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $status = match ($leave->status) {
            Leave::STATUS_PENDING => Leave::STATUS_REJECTED_KADIV,
            Leave::STATUS_APPROVED_KADIV => Leave::STATUS_REJECTED_HR,
            Leave::STATUS_APPROVED_HR => Leave::STATUS_REJECTED_DIREKSI,
            default => Leave::STATUS_REJECTED_HR,
        };

        $leave->update([
            'status' => $status,
            'note' => $validated['rejection_reason'],
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti ditolak.');
    }
}
