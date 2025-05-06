<?php
namespace App\Http\Controllers;

use App\Models\ImportHistory;
use App\Models\LargeCategory;
use App\Models\Product;
use App\Models\SmallCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    // Hiển thị tất cả sản phẩm
    public function index()
    {
        $products        = Product::paginate(10); // Thay vì get()
        $smallCategories = SmallCategory::all();
        return view('admin.products.index', compact('products', 'smallCategories')); // Truyền dữ liệu vào view
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
            'name'              => 'required|string|max:255',
            'price'             => 'required|numeric',
            'import_price'      => 'required|numeric', // Giá nhập
            'quantity'          => 'required|integer',
            'description'       => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'small_category_id' => 'required|exists:small_categories,id',
        ]);

        // Xử lý hình ảnh
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        } else {
            $imageName = null;
        }

        // Tạo sản phẩm mới
        $product = Product::create([
            'name'              => $request->name,
            'price'             => $request->price,
            'import_price'      => $request->import_price, 
            'quantity'          => $request->quantity,
            'description'       => $request->description,
            'image'             => $imageName,
            'small_category_id' => $request->small_category_id,
        ]);

        ImportHistory::create([
            'product_id'   => $product->id,
            'import_price' => $request->import_price,
            'quantity'     => $product->quantity,
            'total_cost'   => $request->import_price * $request->quantity,
            'import_date'  => now(),
        ]);

        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function uploadImage(Request $request)
    {
        // Kiểm tra ảnh có hợp lệ không
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5000',
        ]);

        // Lưu ảnh vào thư mục public/uploads/images
        $image     = $request->file('image');
        $imagePath = $image->store('uploads/images', 'public');

        // Trả về đường dẫn ảnh
        return response()->json([
            'location' => asset('storage/' . $imagePath),
        ]);
    }
    // Sửa sản phẩm
    public function edit($id)
    {
        // Tìm sản phẩm theo ID
        $product = Product::find($id);

        // Kiểm tra nếu không tìm thấy sản phẩm
        if (! $product) {
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
            'name'              => 'required|string|max:255',
            'price'             => 'required|numeric',
            'import_price'      => 'required|numeric', 
            'quantity'          => 'required|integer',
            'description'       => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'small_category_id' => 'required|exists:small_categories,id',
        ]);

        // Tìm sản phẩm
        $product = Product::find($id);

        if (! $product) {
            return redirect()->route('products.index')->with('error', 'Sản phẩm không tồn tại!');
        }

        // Kiểm tra nếu giá nhập hoặc số lượng thay đổi thì ghi vào lịch sử nhập hàng
        if ($product->import_price != $request->import_price || $product->quantity != $request->quantity) {
            ImportHistory::create([
                'product_id'   => $product->id,
                'import_price' => $request->import_price,
                'quantity'     => $request->quantity - $product->quantity, // Chênh lệch số lượng nhập
                'total_cost'   => $request->import_price * ($request->quantity - $product->quantity),
                'import_date'  => now(),
            ]);
        }

        // Xử lý hình ảnh
        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        } else {
            $imageName = $product->image;
        }

        // Cập nhật sản phẩm
        $product->update([
            'name'              => $request->name,
            'price'             => $request->price,
            'import_price'      => $request->import_price,
            'quantity'          => $request->quantity,
            'description'       => $request->description,
            'image'             => $imageName,
            'small_category_id' => $request->small_category_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }
    public function destroy($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return redirect()->route('products.index')->with('error', 'Sản phẩm không tồn tại!');
        }

        // Xóa ảnh nếu có
        if ($product->image && file_exists(public_path('images/' . $product->image))) {
            unlink(public_path('images/' . $product->image));
        }

        // Xóa sản phẩm (Giữ lại lịch sử nhập)
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            if ($file->getClientOriginalExtension() === 'xlsx' || $file->getClientOriginalExtension() === 'xls') {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
    
                foreach ($rows as $row) {
                    // Bỏ qua dòng tiêu đề
                    if ($row[0] !== 'ID' && $row[1] !== 'Tên Sản Phẩm') {
                        $categoryName = $row[5];
                        $categoryId = \App\Models\SmallCategory::where('name', $categoryName)->value('id');
    
                        if (!$categoryId) {
                            return back()->with('error', 'Danh mục không hợp lệ: ' . $categoryName);
                        }
    
                        // Tạo sản phẩm và lưu lại instance
                        $product = Product::create([
                            'name'              => $row[1],
                            'price'             => $row[2],
                            'quantity'          => $row[3],
                            'import_price'      => $row[4],
                            'small_category_id' => $categoryId,
                            'image'             => $row[6],
                        ]);
    
                        // Lưu lịch sử nhập kho
                        ImportHistory::create([
                            'product_id'   => $product->id,
                            'import_price' => $row[4],
                            'quantity'     => $row[3],
                            'total_cost'   => $row[4] * $row[3], 
                            'import_date'  => now(),
                        ]);
                    }
                }
    
                return redirect()->route('products.index')->with('success', 'Nhập sản phẩm thành công!');
            } else {
                return back()->with('error', 'Chỉ hỗ trợ file Excel (.xlsx hoặc .xls)!');
            }
        }
    
        return back()->with('error', 'Không có file nào được chọn!');
    }
    
    public function showProducts(Request $request)
    {
        // Lấy giá sản phẩm nhỏ nhất và lớn nhất
        $minPrice = Product::min('price');
        $maxPrice = Product::max('price');

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
        if (! $defaultCategory) {
            $defaultCategory = $largeCategories->first();
        }

        // Thiết lập tiêu đề danh mục mặc định
        $selectedCategoryTitle = $defaultCategory->name;

        // Nếu có lọc theo danh mục nhỏ, lấy danh mục nhỏ đó và sản phẩm của nó
        $smallCategoryId = $request->get('small_category_id');
        if ($smallCategoryId) {
            $smallCategory = SmallCategory::find($smallCategoryId);

            // Kiểm tra nếu danh mục nhỏ không tồn tại
            if (! $smallCategory) {
                return redirect()->route('products.index')->with('error', 'Danh mục nhỏ không tồn tại!');
            }

            // Thiết lập tiêu đề danh mục theo danh mục nhỏ đã chọn
            $selectedCategoryTitle = $smallCategory->name;

            // Lọc sản phẩm theo danh mục nhỏ đã chọn
            $productsQuery = $smallCategory->products();

        } else {
            // Nếu không có lọc theo danh mục nhỏ, lấy tất cả sản phẩm của danh mục lớn
            $productsQuery = Product::whereIn('small_category_id', $defaultCategory->smallCategories->pluck('id'));
        }

        // Lọc theo giá (nếu có)
        $minPriceInput = $request->get('min_price', 0);
        $maxPriceInput = $request->get('max_price', 10000000);
        $productsQuery->whereBetween('price', [$minPriceInput, $maxPriceInput]);

        // Xử lý sắp xếp theo các trường hợp khác nhau
        if ($request->get('sort_by') == 'name_asc') {
            $productsQuery->orderBy('name', 'asc');
        } elseif ($request->get('sort_by') == 'name_desc') {
            $productsQuery->orderBy('name', 'desc');
        } elseif ($request->get('sort_by') == 'price_asc') {
            $productsQuery->orderBy('price', 'asc');
        } elseif ($request->get('sort_by') == 'price_desc') {
            $productsQuery->orderBy('price', 'desc');
        } elseif ($request->get('sort_by') == 'latest') {
            $productsQuery->orderBy('created_at', 'desc');
        } elseif ($request->get('sort_by') == 'oldest') {
            $productsQuery->orderBy('created_at', 'asc');
        }

        // Phân trang sản phẩm
        $perPage = $request->get('per_page', 12);
        if (! in_array($perPage, [9, 18, 36])) {
            $perPage = 9; // Giá trị mặc định nếu người dùng nhập không hợp lệ
        }

        $products = $productsQuery->paginate($perPage);
        // Trả dữ liệu về view
        return view('product.index', compact('largeCategories', 'products', 'defaultCategory', 'selectedCategoryTitle', 'minPrice', 'maxPrice'));
    }
    public function search(Request $request)
{
    $query = trim($request->input('query') ?? $request->input('queries'));

    // Nếu không nhập từ khóa tìm kiếm, trả về trang hiện tại kèm thông báo lỗi
    if (empty($query)) {
        return back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm!');
    }
    if (Auth::guard('admin')->check()) {
        // Admin đăng nhập, tìm kiếm sản phẩm trên trang quản lý
        $smallCategories = SmallCategory::all();

        $products = Product::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->paginate(10);

        return view('admin.products.index', compact('products', 'smallCategories'));
    } else {
        // Người dùng (hoặc khách) tìm kiếm sản phẩm trên trang user
        $largeCategories       = LargeCategory::with('smallCategories.products')->get();
        $defaultCategory       = $largeCategories->first();
        $selectedCategoryTitle = 'Kết quả tìm kiếm: ' . $query;

        $products = Product::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->paginate(12);

        return view('product.index', compact('products', 'largeCategories', 'defaultCategory', 'selectedCategoryTitle'));
    }
}

// Hiển thị chi tiết sản phẩm
    public function details($id)
    {
        // Lấy số lượng sản phẩm đã bán cho sản phẩm có ID cụ thể
        $totalSold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $id)                        // Lọc theo ID sản phẩm
            ->whereIn('orders.status', ['shipped', 'completed', 'rated']) // Chỉ tính đơn hàng hợp lệ
            ->sum('order_items.quantity');                                // Tính tổng số lượng

        // Lấy thông tin chi tiết sản phẩm
        $product               = Product::with('smallCategory.largeCategory', 'reviews.user', 'reviews')->find($id);
        $product->rating       = round($product->reviews()->avg('rating'), 1) ?? 0; // Tính trung bình rating và làm tròn tới 1 chữ số thập phân
        $product->review_count = $product->reviews()->count();                      // Đếm số lượng đánh giá
        $comments              = $product->reviews->pluck('comment');
        // Kiểm tra nếu sản phẩm không tồn tại
        if (! $product) {
            return redirect()->route('products.index')->with('error', 'Sản phẩm không tồn tại!');
        }

        // Truyền số lượng đã bán vào view
        return view('product.details', compact('product', 'totalSold', 'comments'));
    }
}
