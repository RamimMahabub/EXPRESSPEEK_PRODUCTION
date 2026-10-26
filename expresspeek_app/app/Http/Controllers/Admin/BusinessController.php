<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessApplication;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $applications = BusinessApplication::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.business-applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = BusinessApplication::findOrFail($id);
        
        // Mark as reviewed if it's currently pending
        if ($application->status === 'pending') {
            $application->update(['status' => 'reviewed']);
        }

        return view('admin.business-applications.show', compact('application'));
    }
}
