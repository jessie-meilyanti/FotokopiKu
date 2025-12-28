<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        $currentStaff = Staff::where('user_id', $currentUser->id)->first();
        $isOwner = $currentStaff && $currentStaff->role === 'owner';
        $isAdminRole = $currentStaff && $currentStaff->role === 'admin';
        $isSystemAdmin = (bool) ($currentUser->is_admin ?? false);

        // All staff can view all staff profiles, but edit permissions are controlled separately
        // Order by role hierarchy: owner > admin > staff > courier > manager
        $query = Staff::with('user')
            ->orderByRaw("CASE 
                WHEN role = 'owner' THEN 1 
                WHEN role = 'admin' THEN 2 
                WHEN role = 'staff' THEN 3 
                WHEN role = 'courier' THEN 4 
                WHEN role = 'manager' THEN 5 
                ELSE 6 END")
            ->orderBy('user_id');

        $staff = $query->paginate(12);
        return view('admin.staff.index', [
            'staff' => $staff,
            'isOwner' => $isOwner,
            'isAdmin' => $isAdminRole || $isSystemAdmin,
            'currentStaff' => $currentStaff,
            'isSystemAdmin' => $isSystemAdmin,
        ]);
    }

    public function edit($id)
    {
        $currentUser = auth()->user();
        $currentStaff = Staff::where('user_id', $currentUser->id)->first();
        $isOwner = $currentStaff && $currentStaff->role === 'owner';
        $isAdminRole = $currentStaff && $currentStaff->role === 'admin';
        $isSystemAdmin = (bool) ($currentUser->is_admin ?? false);

        $staff = Staff::with('user')->findOrFail($id);
        
        // Owner, admin role, and system admin can edit all staff; others can only edit themselves
        if (!($isOwner || $isAdminRole || $isSystemAdmin) && $staff->user_id !== $currentUser->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.staff.edit', [
            'staff' => $staff,
            'isOwner' => $isOwner,
            'isAdmin' => $isAdminRole || $isSystemAdmin,
            'currentStaff' => $currentStaff,
            'isSystemAdmin' => $isSystemAdmin,
        ]);
    }

    public function create()
    {
        $currentUser = auth()->user();
        $currentStaff = Staff::where('user_id', $currentUser->id)->first();
        $isOwner = $currentStaff && $currentStaff->role === 'owner';
        $isAdminRole = $currentStaff && $currentStaff->role === 'admin';
        $isSystemAdmin = (bool) ($currentUser->is_admin ?? false);

        if (!($isOwner || $isAdminRole || $isSystemAdmin)) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.staff.create', [
            'isOwner' => $isOwner,
            'isAdmin' => $isAdminRole || $isSystemAdmin,
            'currentStaff' => $currentStaff,
            'isSystemAdmin' => $isSystemAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $currentStaff = Staff::where('user_id', $currentUser->id)->first();
        $isOwner = $currentStaff && $currentStaff->role === 'owner';
        $isAdminRole = $currentStaff && $currentStaff->role === 'admin';
        $isSystemAdmin = (bool) ($currentUser->is_admin ?? false);

        if (!($isOwner || $isAdminRole || $isSystemAdmin)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'staff_id' => 'required|string|max:20|unique:staff,staff_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:owner,admin,staff,courier,manager',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Create user with default password 'password'
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // Handle photo upload
        $photoPath = '/images/user-placeholder.svg';
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $validated['staff_id'] . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/staff'), $photoName);
            $photoPath = '/images/staff/' . $photoName;
        }

        // Create staff record
        Staff::create([
            'user_id' => $user->id,
            'staff_id' => $validated['staff_id'],
            'role' => $validated['role'],
            'position' => $validated['position'],
            'salary' => 0,
            'phone' => $validated['phone'],
            'photo' => $photoPath,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();
        $currentStaff = Staff::where('user_id', $currentUser->id)->first();
        $isOwner = $currentStaff && $currentStaff->role === 'owner';
        $isAdminRole = $currentStaff && $currentStaff->role === 'admin';
        $isSystemAdmin = (bool) ($currentUser->is_admin ?? false);

        $staff = Staff::with('user')->findOrFail($id);
        
        // Owner, admin role, and system admin can edit all staff; others can only edit themselves
        if (!($isOwner || $isAdminRole || $isSystemAdmin) && $staff->user_id !== $currentUser->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remove_photo' => 'sometimes|boolean',
        ]);

        // Update user name
        $staff->user->update([
            'name' => $validated['name']
        ]);

        // Handle remove photo checkbox
        if ($request->boolean('remove_photo')) {
            if ($staff->photo && $staff->photo !== '/images/user-placeholder.svg') {
                $oldPhotoPath = public_path($staff->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            $validated['photo'] = '/images/user-placeholder.svg';
        }
        // Handle photo upload
        elseif ($request->hasFile('photo')) {
            // Delete old photo if exists and not placeholder
            if ($staff->photo && $staff->photo !== '/images/user-placeholder.svg') {
                $oldPhotoPath = public_path($staff->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            // Store new photo
            $photo = $request->file('photo');
            $photoName = time() . '_' . $staff->staff_id . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/staff'), $photoName);
            $validated['photo'] = '/images/staff/' . $photoName;
        }

        // Update staff (only update photo if it was changed)
        $updateData = [
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'notes' => $validated['notes'],
        ];
        
        if (isset($validated['photo'])) {
            $updateData['photo'] = $validated['photo'];
        }
        
        $staff->update($updateData);

        return redirect()->route('admin.staff.index')->with('success', 'Profil staff berhasil diperbarui.');
    }
}
