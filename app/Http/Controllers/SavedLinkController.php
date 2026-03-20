<?php
namespace App\Http\Controllers;

use App\Models\SavedLink;
use Illuminate\Http\Request;

class SavedLinkController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $draw = request()->get('draw');
            $start = request()->get('start');
            $length = request()->get('length');
            $search = request()->get('search')['value'];
            $order = request()->get('order')[0];
            $column = request()->get('columns')[$order['column']]['data'];
            $direction = $order['dir'];
    
            $query = SavedLink::where('user_id', auth()->id());
    

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('url', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }
    

            $totalRecords = $query->count();
            $filteredRecords = $totalRecords;
    

            $query->orderBy($column, $direction);

            $links = $query->skip($start)
                          ->take($length)
                          ->get();
    
            $data = $links->map(function($link) {
                return [
                    'id' => $link->id,
                    'title' => $link->title,
                    'url' => $link->url,
                    'description' => $link->description,
                    'actions' => '<div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary edit-btn" data-id="'.$link->id.'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="'.$link->id.'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>'
                ];
            });
    
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
    
        return view('saved_links.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'category' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        
        SavedLink::create($data);
        return response()->json(['success' => true]);
    }

    public function show(SavedLink $savedLink)
    {
        if ($savedLink->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json($savedLink);
    }

    public function update(Request $request, SavedLink $savedLink)
    {
        if ($savedLink->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'category' => 'nullable|string'
        ]);

        $savedLink->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(SavedLink $savedLink)
    {
        if ($savedLink->user_id !== auth()->id()) {
            abort(403);
        }
        
        $savedLink->delete();
        return response()->json(['success' => true]);
    }
}