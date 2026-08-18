<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $listings = Listing::when($q, function($query) use ($q){
            $query->where('title', 'like', "%{$q}%")->orWhere('address', 'like', "%{$q}%");
        })->orderBy('is_vip', 'desc')->paginate(12);

        return response()->json($listings);
    }

    public function show($id)
    {
        $listing = Listing::findOrFail($id);
        return response()->json($listing);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'=>'required|string',
            'price'=>'required|numeric',
            'type'=>'required|string',
            'address'=>'nullable|string',
        ]);

        $listing = Listing::create(array_merge($data, ['photos'=>$request->input('photos', [])]));

        // TODO: send SMS using Eskiz for owner verification or confirmation

        return response()->json($listing, 201);
    }

    public function update(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $listing->update($request->all());
        return response()->json($listing);
    }

    public function destroy($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();
        return response()->json(['message'=>'deleted']);
    }
}
