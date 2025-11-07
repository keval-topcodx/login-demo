<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\SendVerificationMail;
use App\Models\ProductVariant;
use App\Models\UserProducts;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Psy\Util\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('id')->paginate(10);
        return view('user.index' , ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->validated();
        $user = User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'hobbies' => json_encode($input['hobbies']),
            'gender' => $input['gender'],
            'phone_no' => $input['phone_no'],
        ]);

        $user
            ->addMedia($request->file('image'))
            ->toMediaCollection('users');

        Mail::to($user)->send(new SendVerificationMail($user));

        return redirect()->route('users.index')->with('success', 'User added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('user.edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $input = $request->validated();

        $user->update([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => $input['password'] ? Hash::make($input['password']) : $user->password,
            'hobbies' => json_encode($input['hobbies']),
            'gender' => $input['gender'],
            'phone_no' => $input['phone_no']
        ]);

        if ($request->hasFile('image')) {
            $user->clearMediaCollection('users');
                $user->addMedia($request->file('image'))->toMediaCollection('users');
        }

        $roles = $input['roles'] ?? [];

        $user->syncRoles($roles);

        $products = $input['products'];
        $variants = [];
        foreach ($products as $product) {
            if (empty($product['name']) && empty($product['price'])) {
                continue;
            }
            $variant = json_decode($product['variant']);
            $variantId = $variant->id;
            $selectedVariant = ProductVariant::find($variantId);
            $productId = $selectedVariant->product_id;
            $price = $product['price'];

            $variants[$variantId] = [
                'product_id' => $productId,
                'price' => $price,
            ];
        }
        $user->productVariants()->sync($variants);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    public function addCredits(Request $request, User $user)
    {

        $validated = $request->validate([
            'credit' => ['required', 'numeric', 'decimal:0,2', 'max:99999999.99'],
            'reason' => ['required'],
        ]);
        $previous_balance = (float) $user->credits ?? 0;
        $new_balance = (float) $previous_balance + $validated['credit'];
        $description = ucfirst(str_replace('_', ' ', $validated['reason']));
        $user->update([
           'credits' => $new_balance,
        ]);

        $user->logs()->create([
            'credit_amount' => $validated['credit'],
            'previous_balance' => $previous_balance,
            'new_balance' => $new_balance,
            'description' => $description,
        ]);
        return redirect()->back()->with('credit', '$' . $validated['credit'] . " added for " . $description);
    }

    public function searchUser(Request $request)
    {
        $search = $request->input("search");

        $users = User::with('media')->doesntHave('chat')
            ->whereRaw("CONCAT(first_name, last_name) LIKE ?", ["%{$search}%"])
            ->get();

        if ($users->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No users found.'
            ]);
        }


    }
}
