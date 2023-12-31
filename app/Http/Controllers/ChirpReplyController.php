<?php

namespace App\Http\Controllers;

use App\Models\ChirpReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChirpReplyController extends Controller
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

    public function getReplies(int $chirpId) {

        return ChirpReply::where('chirp_id', $chirpId)
            ->with('user')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reply' => 'required|string|max:255',
            'chirp_id' => 'required|exists:chirps,id',
        ]);

        $request->user()->ChirpReply()->create($validated);

        return redirect(route('chirps.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ChirpReply $chirpReply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChirpReply $chirpReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChirpReply $chirpReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChirpReply $chirpReply)
    {
        //
    }
}
