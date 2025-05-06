<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Lấy trạng thái từ query parameter
        $status = $request->query('status', 'all');
        $rate   = $request->query('rate', 'all');
        // Lấy đơn hàng theo trạng thái
        $query = Order::query()->where('user_id', auth()->id());

        // Kiểm tra trạng thái và lọc đơn hàng
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        // Kiểm tra rate và lọc đơn hàng theo rate
        if ($rate !== 'all') {
            $query->where('rate', $rate);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('orders.index', compact('orders', 'status', 'rate'));
    }

    public function show($orderId)
    {
        // Tìm đơn hàng theo ID và kiểm tra quyền của người dùng
        $order = Order::findOrFail($orderId);

        // Kiểm tra quyền của người dùng (chỉ người tạo đơn hàng mới có thể xem)
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('orders.index')->with('error', 'Đơn hàng không hợp lệ.');
        }

        // Trả về view với dữ liệu đơn hàng
        return view('orders.show', compact('order'));
    }
    public function cancel(Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Kiểm tra trạng thái của đơn hàng
        if ($order->status !== 'Chờ lấy hàng') {
            return redirect()->route('orders.index')->with('error', 'Chỉ có thể hủy đơn hàng khi trạng thái là "Chờ lấy hàng".');
        }

        // Cập nhật trạng thái đơn hàng thành 'cancelled'
        $order->status = 'canceled';
        $order->save();

        // Hoàn lại số lượng sản phẩm vào kho (nếu cần)
        foreach ($order->orderItems as $item) {
            $item->product->increment('quantity', $item->quantity);
        }

        // Trả về trang danh sách đơn hàng với thông báo thành công
        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy thành công.');
    }
    public function reOrder(Order $order)
    {
        // Kiểm tra quyền của người dùng
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('orders.index')->with('error', 'Đơn hàng không hợp lệ.');
        }

        // Lấy thông tin sản phẩm từ đơn hàng cũ
        $orderItems = $order->orderItems;

        // Kiểm tra xem người dùng có giỏ hàng không
        $cart = auth()->user()->cart;
        if (! $cart) {
            // Nếu không có giỏ hàng, tạo giỏ hàng mới
            $cart          = new Cart();
            $cart->user_id = auth()->id();
            $cart->save();
        }

        // Thêm sản phẩm vào giỏ hàng
        foreach ($orderItems as $orderItem) {
            $existingItem = $cart->items()->where('product_id', $orderItem->product_id)->first();

            if ($existingItem) {
                // Nếu sản phẩm đã có trong giỏ hàng, tăng số lượng
                $existingItem->quantity += $orderItem->quantity;
                $existingItem->save();
            } else {
                // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
                $cart->items()->create([
                    'product_id' => $orderItem->product_id,
                    'quantity'   => $orderItem->quantity,
                    'price'      => $orderItem->price,
                ]);
            }
        }

        // Tính tổng tiền của giỏ hàng
        $totalPrice = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Lấy thông tin địa chỉ
        $currentAddress = auth()->user()->addresses()->where('is_default', true)->first();
        $addresses      = auth()->user()->addresses;

        // Trả về view checkout với dữ liệu của giỏ hàng
        return view('cart.index', compact('cart', 'totalPrice', 'currentAddress', 'addresses'));
    }
    // Hiển thị danh sách đơn hàng
    public function adminIndex()
    {
        $orders = Order::with('orderItems.product')
            ->paginate(8);

        return view('admin.order.index', compact('orders'));
    }
    public function update(Request $request, $id)
    {
        $orders         = Order::findOrFail($id);
        $orders->status = $request->status;
        $orders->save();
        // Nếu đơn hàng hoàn thành, cập nhật tổng chi tiêu & rank của khách hàng
        if (in_array($orders->status, ['Đã giao', 'completed'])) {
            $this->updateCustomerRank($orders->user_id);
        }
        return redirect()->route('admin.order.index')->with('success', 'Trạng thái đơn hàng đã được cập nhật thành công.');
    }
    public function updateCustomerRank($userId)
    {
        $user = User::findOrFail($userId);

        // Tính tổng tiền của các đơn hàng đã hoàn thành
        $totalSpent = $user->orders()
            ->whereIn('status', ['Đã giao', 'completed'])
            ->sum('total_price');
        // Cập nhật rank theo mức mới
        if ($totalSpent >= 20_000_000) {
            $user->rank = 'Kim cương';
        } elseif ($totalSpent >= 10_000_000 && $totalSpent < 20_000_000) {
            $user->rank = 'Vàng';
        } elseif ($totalSpent >= 5_000_000 && $totalSpent < 10_000_000) {
            $user->rank = 'Bạc';
        } else {
            $user->rank = 'Đồng';
        }

        $user->save();
    }

    public function adminShow(Order $order)
    {
        // Lấy thông tin các mục trong đơn hàng cùng thông tin sản phẩm
        $orderItems = \DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.*', 'products.name as product_name', 'products.price as product_price')
            ->get();
    
        return view('admin.order.show', [
            'order'      => $order,
            'orderItems' => $orderItems,
        ]);
    }    
    public function search(Request $request)
    {
         // Kiểm tra đăng nhập
    if (!Auth::check() && !Auth::guard('admin')->check()) {
        return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem đơn hàng.');
    }

    // Xác định quyền (user hay admin)
    $isAdmin = Auth::guard('admin')->check();
    $userId  = $isAdmin ? $request->query('user_id') : auth()->id();

    // Lấy dữ liệu tìm kiếm
    $search   = $request->query('search');  // Tìm theo tên người nhận hoặc ID
    $status   = $request->query('status');  // Tìm theo trạng thái

    // Truy vấn đơn hàng
    $query = Order::query();

    // Nếu không phải admin, chỉ lấy đơn hàng của user
    if (!$isAdmin) {
        $query->where('user_id', $userId);
    }

    // Tìm theo ID đơn hàng hoặc tên người nhận
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'LIKE', "%$search%")   // Tìm theo ID đơn hàng
              ->orWhere('recipient_name', 'LIKE', "%$search%");  // Tìm theo tên người nhận
        });
    }

    // Tìm theo trạng thái đơn hàng
    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    // Lấy danh sách đơn hàng
    $orders = $query->orderBy('created_at', 'desc')->paginate(9);

    return view($isAdmin ? 'admin.order.index' : 'orders.index', compact('orders', 'search', 'status'));
    }
}
