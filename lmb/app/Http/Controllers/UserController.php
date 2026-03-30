<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Loan;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated users can access these methods
        $this->middleware('auth');
        // You might also want to add middleware for role checking
    }
  

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        //dd(Branch::all()); // Fetch all branches
        // Assuming the 'status' column is used to mark a user as active or disabled
        $users = User::with(['role','branch'])->whereIn('status', [1, 2])->get();
        return view('users.index', compact('users'));
    }
    public function registerForm()
    {
        $branches = Branch::all();
        $roles = Role::all();
        return view('auth.register', compact('roles', 'branches'));
    }
    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update password from admin modal form.
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users.index')->with('success', $user->name . '-ის პაროლი წარმატებით შეიცვალა.');
    }

    /**
     * Reset the password for a user.
     */
    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);
        // Set a new random password or a fixed one, depending on your needs
        $newPassword = 'newpassword'; // Ideally, generate a random one
        $user->password = Hash::make($newPassword);
        $user->save();

        // Redirect back with a success message
        return redirect()->route('users.index')->with('success', 'Password reset successfully for ' . $user->name);
    }

    /**
     * Disable a user account.
     */
    public function disableUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 2; // Assuming '2' marks a user as disabled
        $user->save();

        return redirect()->route('users.index')->with('success', 'User disabled successfully.');
    }

    /**
     * Enable a user account.....
     */
    public function enableUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 1; // Assuming '1' marks a user as active
        $user->save();

        return redirect()->route('users.index')->with('success', 'User enabled successfully.');
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'], // Ensure the role exists
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'branch_id' => $data['branch_id'], // Save selected role
        ]);
    }

    /**
     * Handle a registration request for the application.
     * Override to redirect without auto-login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        //dd($request->all()); // Debugging (optional
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Comment out the following line to prevent auto-login
        // $this->guard()->login($user);

        // Redirect with success message instead of auto-login
        return redirect('/users')->with('status', 'Registration successful! Please login.');
    }
    public function userActivities(Request $request)
{
    $users = User::all(); // Get all users for the dropdown
    $userId = $request->user_id;
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    // Validate user input
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Filter loans with comments and payments by user and date range
    $loans = Loan::with(['comments' => function($query) use ($userId, $startDate, $endDate) {
                    $query->where('user_id', $userId)
                          ->whereBetween('created_at', [$startDate, $endDate]);
                }, 'payments' => function($query) use ($userId, $startDate, $endDate) {
                    $query->where('user_id', $userId)
                          ->whereBetween('payment_time', [$startDate, $endDate]);
                }])
                ->where('user_id', $userId)
                ->get();

    return view('user.activities', compact('loans', 'users'));
}


    // You might want to include other methods like create(), store(), update(), destroy() depending on your application's needs
}
