<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Collateral;
use Illuminate\Support\Facades\Auth;
class CollateralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
public function updatePass(Request $request, $id)
{
    $request->validate([
        'new_pass' => 'required|string|max:255',
        'loan_id' => 'required|integer|exists:loans,loan_id',
    ]);

    $collateral = Collateral::findOrFail($id);
    $oldPass = $collateral->pass;

    $collateral->pass = $request->new_pass;
    $collateral->save();

    // Log update in comments
    Comment::create([
        'loan_id' => $request->loan_id,
        'user_id' => Auth::id(),
        'comment' => "ძველი კოდი - {$oldPass} - განახლდა ახლით" ,
    ]);

    return redirect()->route('loans.show', ['loan' => $request->loan_id])
                     ->with('success', 'Collateral pass updated and logged.');
}
public function updateEmail(Request $request, $id)
{
    $request->validate([
        'new_email' => 'required|email|max:255',
        'loan_id' => 'required|integer|exists:loans,loan_id',
    ]);

    $collateral = Collateral::findOrFail($id);
    $oldEmail = $collateral->email;

    $collateral->email = $request->new_email;
    $collateral->save();

    // Log change in comments
    Comment::create([
        'loan_id' => $request->loan_id,
        'user_id' => Auth::id(),
        'comment' => "ძველი მეილი - {$oldEmail} - განახლდა ახლით" ,
    ]);

    return redirect()->route('loans.show', ['loan' => $request->loan_id])
                     ->with('success', 'Collateral email updated and logged.');
}

}
