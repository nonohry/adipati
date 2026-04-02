<?php

namespace App\Http\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        $user = Auth::user();
        $roles = Session::get('roles', []);

        // Redirect Super Admin to admin panel
        if (in_array('super_admin', $roles)) {
            return $this->redirect('/admin');
        }

        // Editor/Chair Dashboard
        if (in_array('chair', $roles) || in_array('editor', $roles)) {
            $stats['pending_submissions'] = $this->db->fetch("SELECT COUNT(*) as count FROM submissions WHERE current_status = 'submitted'")->count ?? 0;
            return $this->view('dashboard.editor', ['user' => $user, 'stats' => $stats]);
        }

        // Reviewer Dashboard
        if (in_array('reviewer', $roles)) {
            $stats['pending_reviews'] = $this->db->fetch("SELECT COUNT(*) as count FROM reviewer_assignments WHERE reviewer_id = ? AND status = 'invited'", [$user->id])->count ?? 0;
            return $this->view('dashboard.reviewer', ['user' => $user, 'stats' => $stats]);
        }

        // Author Dashboard
        if (in_array('author', $roles)) {
            $stats['my_submissions'] = $this->db->fetchAll("SELECT id, title, current_status, created_at FROM submissions WHERE submitter_id = ? ORDER BY created_at DESC LIMIT 5", [$user->id]);
            return $this->view('dashboard.author', ['user' => $user, 'stats' => $stats]);
        }

        // Default Dashboard
        return $this->view('dashboard.home', ['user' => $user]);
    }
}
