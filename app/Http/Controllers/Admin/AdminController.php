<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\AdminController as BackpackAdminController;

class AdminController extends BackpackAdminController
{
    /**
     * Redirect to users list instead of dashboard.
     */
    public function dashboard()
    {
        return redirect(backpack_url('user'));
    }

    /**
     * Redirect /admin to /admin/user.
     */
    public function redirect()
    {
        return redirect(backpack_url('user'));
    }
}
