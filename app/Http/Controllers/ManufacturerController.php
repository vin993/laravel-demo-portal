<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function index()
    {
        $manufacturers = Manufacturer::latest()->get();
        if (request()->ajax()) {
            return response()->json(['data' => $manufacturers]);
        }
        return view('admin.manufacturers.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:manufacturers',
            // 'email' => 'nullable|email',
            // 'phone' => 'nullable|string'
        ]);

        Manufacturer::create($request->all());
        return response()->json(['success' => true]);
    }

    public function show(Manufacturer $manufacturer)
    {
        return response()->json($manufacturer);
    }

    public function update(Request $request, Manufacturer $manufacturer)
    {
        $request->validate([
            'name' => 'required|unique:manufacturers,name,' . $manufacturer->id,
            // 'email' => 'nullable|email',
            // 'phone' => 'nullable|string'
        ]);

        $manufacturer->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();
        return response()->json(['success' => true]);
    }

    public function searchManufacturers(Request $request)
    {
        $query = Manufacturer::select('id', 'name')
            ->orderBy('name', 'asc');
    
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }
        
        if ($request->filled('selected')) {
            $selectedIds = (array) $request->input('selected');
            $query->whereNotIn('id', $selectedIds); 
        }
    
        $manufacturers = $query->take(10)->get();
    
        return response()->json($manufacturers);
    }
}