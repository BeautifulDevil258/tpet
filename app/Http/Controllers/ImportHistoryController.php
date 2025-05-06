<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImportHistory;

class ImportHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ImportHistory::with('product')->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('product', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })->orWhere('id', 'like', "%{$searchTerm}%");
        }
    
        $importHistories = $query->paginate(10); // Thêm phân trang
    
        return view('admin.import_history', compact('importHistories'));
    }
}
