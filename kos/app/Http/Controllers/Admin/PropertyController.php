<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function show()
    {
        $property = Property::first();
        return view('pages.admin.property.index', compact('property'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'default_room_price' => 'nullable|integer|min:0',
            'facilities' => 'nullable|string',
        ]);
        $property = Property::first();
        if ($property) {
            $property->update($data);
        } else {
            $property = Property::create($data);
        }
        return redirect()->back()->with('success', 'Data kos diperbarui.');
    }
}
