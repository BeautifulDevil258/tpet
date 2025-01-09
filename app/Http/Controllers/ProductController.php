<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\LargeCategory;
use App\Models\SmallCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Hiển thị tất cả sản phẩm
    public function index()
    {
        $products = Product::all();  // Lấy tất cả sản phẩm từ cơ sở dữ liệu
        $smallCategories = SmallCategory::all();
        return view('admin.products.index', compact('products', 'smallCategories'));  // Truyền dữ liệu vào view
    }
     // Hiển thị form thêm sản phẩm
     public function create()
     {
         // Lấy tất cả danh mục nhỏ
    $smallCategories = SmallCategory::all();
    return view('admin.products.create', compact('smallCategories'));
     }
     public function store(Request $request)
     {
         // Validate dữ liệu
         $request->validate([
             'name' => 'required|string|max:255',
             'price' => 'required|numeric',
             'quantity' => 'required|integer',
             'description' => 'nullable|string',
             'discount_price' => 'nullable|numeric',
             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
             'small_category_id' => 'required|exists:small_categories,id',  // Validate danh mục nhỏ
         ]);
     
         // Xử lý hình ảnh
         if ($request->hasFile('image')) {
             $imageName = time().'.'.$request->image->extension();  
             $request->image->move(public_path('images'), $imageName);
         } else {
             $imageName = null;  // Nếu không chọn hình ảnh, để giá trị là null
         }
     
         // Tạo sản phẩm mới
         Product::create([
             'name' => $request->name,
             'price' => $request->price,
             'quantity' => $request->quantity,
             'description' => $request->description,
             'discount_price' => $request->discount_price,
             'image' => $imageName, // Lưu tên hình ảnh vào database
             'small_category_id' => $request->small_category_id,  // Lưu danh mục nhỏ vào sản phẩm
         ]);
     
         return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
     }
    // Sửa sản phẩm
    public function edit($id)
    {
        // Tìm sản phẩm theo ID
        $product = Product::find($id);
    
        // Kiểm tra nếu không tìm thấy sản phẩm
        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Sản phẩm không tồn tại!');
        }
    
        // Lấy tất cả danh mục nhỏ
        $smallCategories = SmallCategory::all();
    
        // Trả về view chỉnh sửa sản phẩm với thông tin sản phẩm và danh mục nhỏ
        return view('admin.products.edit', compact('smallCategories', 'product'));
    }
    

public function update(Request $request, $id)
{
    // Validate form data
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'description' => 'nullable|string',
        'discount_price' => 'nullable|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'small_category_id' => 'required|exists:small_categories,id', // Validate small category
    ]);

    // Find the product
    $product = Product::find($id);

    // Check if the product exists
    if (!$product) {
        return redirect()->route('products.index')->with('error', 'Product not found!');
    }

    // Handle image upload if provided
    if ($request->hasFile('image')) {
        if ($product->image && file_exists(public_path('images/' . $product->image))) {
            unlink(public_path('images/' . $product->image));
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
    } else {
        $imageName = $product->image;
    }

    // Update the product details
    $product->update([
        'name' => $request->name,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'description' => $request->description,
        'discount_price' => $request->discount_price,
        'image' => $imageName,
        'small_category_id' => $request->small_category_id,  // Update the small category
    ]);

    return redirect()->route('products.index')->with('success', 'Product updated successfully!');
}

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Xóa thành công!');
    }
    // Hiển thị tất cả sản phẩm nhóm theo danh mục lớn
    public function showProducts(Request $request)
    {
        // Lấy tất cả danh mục lớn cùng các danh mục nhỏ và sản phẩm của chúng
        $largeCategories = LargeCategory::with('smallCategories.products')->get();
    
        // Kiểm tra nếu không có danh mục lớn nào
        if ($largeCategories->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Không có danh mục lớn nào');
        }
    
        // Lấy danh mục lớn theo tham số truyền vào
        $largeCategoryId = $request->get('large_category_id');
        $defaultCategory = LargeCategory::find($largeCategoryId);
    
        // Nếu không có danh mục lớn, lấy danh mục đầu tiên trong danh sách
        if (!$defaultCategory) {
            $defaultCategory = $largeCategories->first();
        }
    
        // Thiết lập tiêu đề danh mục mặc định
        $selectedCategoryTitle = $defaultCategory->name;
    
        // Nếu có lọc theo danh mục nhỏ, lấy danh mục nhỏ đó và sản phẩm của nó
        $smallCategoryId = $request->get('small_category_id');
        if ($smallCategoryId) {
            $smallCategory = SmallCategory::find($smallCategoryId);
    
            // Kiểm tra nếu danh mục nhỏ không tồn tại
            if (!$smallCategory) {
                return redirect()->route('products.index')->with('error', 'Danh mục nhỏ không tồn tại!');
            }
    
            // Thiết lập tiêu đề danh mục theo danh mục nhỏ đã chọn
            $selectedCategoryTitle = $smallCategory->name;
    
            // Lọc sản phẩm theo danh mục nhỏ đã chọn và phân trang
            $products = $smallCategory->products()->paginate($request->get('per_page', 12));
        } else {
            // Nếu không có lọc theo danh mục nhỏ, lấy tất cả sản phẩm của danh mục lớn
            $productQuery = Product::whereIn('small_category_id', $defaultCategory->smallCategories->pluck('id'));
    
            // Xử lý sắp xếp theo các trường hợp khác nhau
            if ($request->get('sort_by') == 'name_asc') {
                $productQuery->orderBy('name', 'asc');
            } elseif ($request->get('sort_by') == 'name_desc') {
                $productQuery->orderBy('name', 'desc');
            } elseif ($request->get('sort_by') == 'price_asc') {
                $productQuery->orderBy('price', 'asc');
            } elseif ($request->get('sort_by') == 'price_desc') {
                $productQuery->orderBy('price', 'desc');
            } elseif ($request->get('sort_by') == 'latest') {
                $productQuery->orderBy('created_at', 'desc');
            } elseif ($request->get('sort_by') == 'oldest') {
                $productQuery->orderBy('created_at', 'asc');
            }
    
            // Phân trang sản phẩm theo số lượng người dùng chọn
            $products = $productQuery->paginate($request->get('per_page', 12));
        }
    
        // Trả dữ liệu về view
        return view('product.index', compact('largeCategories', 'products', 'defaultCategory', 'selectedCategoryTitle'));
    }    
    public function search(Request $request)
{
    // Lấy từ khóa tìm kiếm từ yêu cầu
    $query = $request->input('query');

    // Lấy tất cả danh mục lớn và các danh mục nhỏ kèm sản phẩm
    $largeCategories = LargeCategory::with('smallCategories.products')->get();

    // Tìm kiếm sản phẩm theo tên hoặc mô tả
    $products = Product::where('name', 'like', '%' . $query . '%')
                       ->orWhere('description', 'like', '%' . $query . '%')
                       ->paginate(12); // Thêm phân trang để không tải hết sản phẩm

    // Lấy danh mục lớn đầu tiên hoặc có thể lấy theo cách bạn chọn
    $defaultCategory = $largeCategories->first();

    // Thiết lập tiêu đề danh mục chọn, nếu không có chọn danh mục nhỏ thì dùng danh mục lớn
    $selectedCategoryTitle = $defaultCategory ? $defaultCategory->name : 'Tất cả sản phẩm';

    // Trả về view với kết quả tìm kiếm, danh mục lớn và danh mục mặc định
    return view('product.index', compact('products', 'largeCategories', 'defaultCategory', 'selectedCategoryTitle'));
}

}