<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGiftcardRequest;
use App\Http\Requests\UpdateGiftcardRequest;
use App\Models\Giftcard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class GiftCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $giftcards = Giftcard::all();
        return view('giftcard.index', ['giftcards' => $giftcards]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('giftcard.create', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateGiftcardRequest $request)
    {
        $input = $request->validated();
//        dd($input);
        Giftcard::create([
           'code' => $input['code'],
            'initial_balance' => $input['balance'],
            'balance' => $input['balance'],
            'status' => $input['status'],
            'user_id' => $input['user_id'],
            'expiry_date' => $input['expiry_date'],
        ]);
        return redirect()->route('giftcards.index')->with('success', 'giftcard added successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Giftcard $giftcard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Giftcard $giftcard)
    {
        return view('giftcard.edit', ['giftcard' => $giftcard]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGiftcardRequest $request, Giftcard $giftcard)
    {
        $input = $request->validated();

        $giftcard->update([
            'balance' => $input['balance'],
            'expiry_date' => $input['expiry_date'],
            'status' => $input['status'],
        ]);

        return redirect()->route('giftcards.index')
            ->with('success', 'Giftcard updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Giftcard $giftcard)
    {
        $giftcard->delete();
        return redirect()->route('giftcards.index')->with('success', 'Giftcard deleted successfully!');
    }
}
