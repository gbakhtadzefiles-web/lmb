<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $branches = Branch::withCount('users')->get();
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
        ]);

        Branch::create(['name' => $request->name]);

        return redirect()->route('branches.index')->with('success', 'ფილიალი წარმატებით დაემატა.');
    }
}
