<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Industry;

class IndustryController extends Controller
{
    public function index()
    {
        $industries = Industry::latest()->get();
        if (request()->ajax()) {
            return response()->json(['data' => $industries]);
        }
        return view('admin.industries.index');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:industries']);
        Industry::create($request->all());
        return response()->json(['success' => true]);
    }
    public function show(Industry $industry)
    {
        return response()->json($industry);
    }

    public function update(Request $request, Industry $industry)
    {
        $request->validate(['name' => 'required|unique:industries,name,' . $industry->id]);
        $industry->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();
        return response()->json(['success' => true]);
    }
}