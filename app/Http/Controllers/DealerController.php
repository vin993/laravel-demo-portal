<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = Dealer::latest()->get();
        if (request()->ajax()) {
            return response()->json(['data' => $dealers]);
        }
        return view('admin.dealers.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:dealers',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        Dealer::create($request->all());
        return response()->json(['success' => true]);
    }

    public function show(Dealer $dealer)
    {
        return response()->json($dealer);
    }

    public function update(Request $request, Dealer $dealer)
    {
        $request->validate([
            'name' => 'required|unique:dealers,name,' . $dealer->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $dealer->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(Dealer $dealer)
    {
        $dealer->delete();
        return response()->json(['success' => true]);
    }
    public function search(Request $request)
    {
        $query = Dealer::select('id', 'name')
            ->orderBy('name', 'asc');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

        $dealers = $query->take(10)->get();

        return response()->json($dealers);
    }
}