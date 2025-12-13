<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoriesExport;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-categories')->only(['index', 'show']);
        $this->middleware('permission:create-categories')->only(['create', 'store']);
        $this->middleware('permission:edit-categories')->only(['edit', 'update']);
        $this->middleware('permission:delete-categories')->only('destroy');
        $this->middleware('permission:export-categories')->only('export');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::query();

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $categories->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $totalRecords = Category::count();
            $filteredRecords = $categories->count();

            $categories = $categories
                ->withCount('items')
                ->orderBy($request->input('columns.'.$request->input('order.0.column').'.data'), $request->input('order.0.dir'))
                ->offset($request->start)
                ->limit($request->length)
                ->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $categories
            ]);
        }

        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Category::create($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('items');
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code,'.$category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category->update($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has items
        $itemCount = $category->items()->count();
        
        if ($itemCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kategori '{$category->name}' tidak dapat dihapus karena masih memiliki {$itemCount} item. Hapus atau pindahkan item terlebih dahulu."
            ], 400);
        }

        try {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete category.'], 500);
        }
    }

    /**
     * Export categories to Excel
     */
    public function export()
    {
        return Excel::download(new CategoriesExport, 'categories_'.date('Y-m-d').'.xlsx');
    }
}
