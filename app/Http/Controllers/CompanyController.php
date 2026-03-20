<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['country', 'state', 'city'])->latest()->get();
        if (request()->ajax()) {
            return response()->json(['data' => $companies]);
        }
        return view('admin.companies.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:companies',
            'legal_name' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'currency' => 'nullable|string',
            'timezone' => 'nullable|string',
            'about' => 'nullable|string'
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('companies', $filename, 'public');
            $data['logo'] = $filename;
        }

        Company::create($data);
        return response()->json(['success' => true]);
    }

    public function show(Company $company)
    {
        return response()->json($company->load(['country', 'state', 'city']));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|unique:companies,name,' . $company->id,
            'legal_name' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'country' => 'nullable|exists:countries,id',
            'state' => 'nullable|exists:states,id',
            'city' => 'nullable|exists:cities,id',
            'postal_code' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'currency' => 'nullable|string',
            'timezone' => 'nullable|string',
            'about' => 'nullable|string'
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete('public/companies/' . $company->logo);
            }

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('companies', $filename, 'public');
            $data['logo'] = $filename;
        }

        $company->update($data);
        return response()->json(['success' => true]);
    }

    public function destroy(Company $company)
    {
        if ($company->logo) {
            Storage::delete('public/companies/' . $company->logo);
        }

        $company->delete();
        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = Company::select('id', 'name')
            ->orderBy('name', 'asc');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

        $companies = $query->take(10)->get();

        return response()->json($companies);
    }
}