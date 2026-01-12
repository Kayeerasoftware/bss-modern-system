@can and @cannot directives for permissions

Usage in Blade templates:
@can('view_members')
    <!-- Content visible only to users with view_members permission -->
@endcan

@cannot('delete_members')
    <!-- Content visible only to users WITHOUT delete_members permission -->
@endcannot

Usage in Controllers:
if (auth()->user()->hasPermission('view_members')) {
    // Allow access
}

if (auth()->user()->hasAnyPermission(['view_members', 'edit_members'])) {
    // Allow if user has any of these permissions
}

if (auth()->user()->hasAllPermissions(['view_members', 'edit_members'])) {
    // Allow only if user has all these permissions
}

Usage in Routes:
Route::get('/members', [MemberController::class, 'index'])->middleware('permission:view_members');
Route::post('/members', [MemberController::class, 'store'])->middleware('permission:create_members');
Route::put('/members/{id}', [MemberController::class, 'update'])->middleware('permission:edit_members');
Route::delete('/members/{id}', [MemberController::class, 'destroy'])->middleware('permission:delete_members');
