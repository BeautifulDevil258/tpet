<?php
namespace App\Http\Controllers;

use App\Models\LargeCategory;
use App\Models\SmallCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CategoryController extends Controller
{
    // Hiển thị danh mục lớn
    public function index()
    {
        $largeCategories = LargeCategory::all();
        return view('admin.large_categories.index', compact('largeCategories'));
    }
    // Hiển thị form thêm danh mục lớn
public function create()
{
    return view('admin.large_categories.create');
}
// Thêm danh mục lớn
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    LargeCategory::create($request->only('name'));

    return redirect()->route('large_categories.index')->with('success', 'Thêm danh mục lớn thành công!');
}
    // Sửa danh mục lớn
    public function edit($id)
    {
        $largeCategory = LargeCategory::find($id);
        return view('admin.large_categories.edit', compact('largeCategory'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $largeCategory = LargeCategory::find($id);
        $largeCategory->update($request->only('name'));

        return redirect()->route('large_categories.index')->with('success', 'Cập nhật thành công!');
    }

    // Xóa danh mục lớn
    public function destroy($id)
    {
        $largeCategory = LargeCategory::find($id);
        $largeCategory->delete();

        return redirect()->route('large_categories.index')->with('success', 'Xóa thành công!');
    }

    // Hiển thị danh mục nhỏ
    public function smallCategoryIndex()
    {
        $smallCategories = SmallCategory::all();
        return view('admin.small_categories.index', compact('smallCategories'));
    }
    // Hiển thị form thêm danh mục nhỏ
public function smallCategoryCreate()
{
    // Lấy danh sách các danh mục lớn để chọn
    $largeCategories = LargeCategory::all();
    return view('admin.small_categories.create', compact('largeCategories'));
}

// Thêm danh mục nhỏ
public function smallCategoryStore(Request $request)
{
    // Validate the request data
    $request->validate([
        'name' => 'required|string|max:255',
        'large_category_id' => 'required|exists:large_categories,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
    ]);

    // Handle image upload if present
    $imageName = null;
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension(); // Generate a unique image name
        $request->image->move(public_path('images'), $imageName); // Store the image in the public/images folder
    }

    // Create the small category
    SmallCategory::create([
        'name' => $request->name,
        'large_category_id' => $request->large_category_id,
        'image' => $imageName, // Store the image name in the database
    ]);

    return redirect()->route('small_categories.index')->with('success', 'Thêm danh mục nhỏ thành công!');
}

    // Sửa danh mục nhỏ
    public function smallCategoryEdit($id)
    {
        // Find the small category by its ID
    $smallCategory = SmallCategory::findOrFail($id);

    // Get all large categories for the dropdown
    $largeCategories = LargeCategory::all();

    // Pass the small category and large categories to the view
    return view('admin.small_categories.edit', compact('smallCategory', 'largeCategories'));
    }
    public function smallCategoryUpdate(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
        ]);
    
        // Find the small category
        $smallCategory = SmallCategory::find($id);
    
        // Handle image upload if present
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($smallCategory->image) {
                unlink(public_path('images/' . $smallCategory->image)); // Delete old image
            }
    
            // Upload the new image
            $imageName = time() . '.' . $request->image->extension(); // Generate a unique image name
            $request->image->move(public_path('images'), $imageName); // Store the new image in the public/images folder
    
            // Update the image in the database
            $smallCategory->image = $imageName;
        }
    
        // Update the small category data
        $smallCategory->update([
            'name' => $request->name,
            // The image field is updated only if a new image is uploaded
        ]);
    
        return redirect()->route('small_categories.index')->with('success', 'Cập nhật thành công!');
    }
    
    // Xóa danh mục nhỏ
    public function smallCategoryDestroy($id)
    {
        $smallCategory = SmallCategory::find($id);
        $smallCategory->delete();

        return redirect()->route('small_categories.index')->with('success', 'Xóa thành công!');
    }
    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            if ($file->getClientOriginalExtension() == 'xlsx' || $file->getClientOriginalExtension() == 'xls') {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
    
                foreach ($rows as $row) {
                    // Bỏ qua các dòng có dữ liệu không hợp lệ (Tiêu đề hoặc dữ liệu không hợp lệ)
                    if (empty($row[0]) || $row[0] == 'ID' || empty($row[1])) {
                        continue;
                    }
    
                    $smallCategoryName = trim($row[1]);
                    $largeCategoryName = trim($row[2]);
    
                    \Log::info('Searching for Large Category: ' . $largeCategoryName);
    
                    // Tìm ID danh mục lớn
                    $largeCategoryId = LargeCategory::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($largeCategoryName)])->value('id');
    
                    // In ra ID tìm thấy
                    \Log::info('Found Large Category ID: ' . $largeCategoryId);
    
                    if (!$largeCategoryId) {
                        return back()->with('error', 'Danh mục lớn không hợp lệ: ' . $largeCategoryName);
                    }
    
                    // Lưu danh mục nhỏ vào cơ sở dữ liệu
                    SmallCategory::create([
                        'name' => $smallCategoryName,
                        'large_category_id' => $largeCategoryId,
                        'image' => isset($row[3]) ? $row[2] : null, // Nếu có ảnh, lưu vào cơ sở dữ liệu
                    ]);
                }
    
                return redirect()->route('small_categories.index')->with('success', 'Nhập danh mục nhỏ thành công!');
            } else {
                return back()->with('error', 'Chỉ hỗ trợ file Excel (.xlsx hoặc .xls)!');
            }
        }
    
        return back()->withErrors('error', 'Không có file nào được chọn!');
    }    
}