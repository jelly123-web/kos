<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::orderBy('name')->get();
        return view('pages.super-admin.properties.index', compact('properties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_room_price' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
        ]);
        Property::create($data);
        return redirect()->back()->with('success', 'Data kos ditambahkan.');
    }

    public function update(Request $request, Property $property)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_room_price' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
        ]);
        $property->update($data);
        return redirect()->back()->with('success', 'Data kos diperbarui.');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->back()->with('success', 'Data kos dihapus.');
    }
}

