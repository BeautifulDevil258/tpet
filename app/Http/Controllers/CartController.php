<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {

        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem giỏ hàng');
        }

        // Lấy giỏ hàng của người dùng đã đăng nhập hoặc tạo mới nếu không có
        $cart = auth()->user()->cart;

        if (! $cart) {
            $cart = auth()->user()->cart()->create(); // Tạo giỏ hàng trống
        }

        // Tải các sản phẩm trong giỏ hàng
        $cart->load('items.product');
        // Trả về view với giỏ hàng
        return view('cart.index', compact('cart'));
    }
    public function addToCart(Request $request, $id)
    {
        // Kiểm tra xem người dùng có đăng nhập không
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng');
        }
        // Lấy sản phẩm
        $product  = Product::findOrFail($id);
        $quantity = $request->input('quantity', 1);

        // Kiểm tra nếu người dùng đã có giỏ hàng
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->price = $product->price * $cartItem->quantity;
            // Cập nhật số lượng nếu sản phẩm đã có trong giỏ hàng
            $cartItem->quantity += $quantity; // quantity luôn được tăn
            $cartItem->save();
        } else {
            // Thêm sản phẩm mới vào giỏ hàng
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'price'      => $product->price * $quantity,
            ]);
        }
        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
    }
    public function update(Request $request, $cartItemId)
    {
        // Tìm sản phẩm trong giỏ
        $cartItem = CartItem::findOrFail($cartItemId);
    
        // Lấy sản phẩm
        $product = $cartItem->product;
    
        // Kiểm tra nếu số lượng yêu cầu lớn hơn số lượng có sẵn
        if ($request->quantity > $product->quantity) {
            return redirect()->route('cart.index')->with('error', 'Số lượng sản phẩm yêu cầu vượt quá số lượng có sẵn trong kho.');
        }
    
        // Cập nhật số lượng
        $cartItem->quantity = $request->quantity;
    
        // Cập nhật giá tạm tính (price)
        $cartItem->price = $product->price * $cartItem->quantity;
    
        // Lưu lại thông tin giỏ hàng
        $cartItem->save();
    
        // Tính lại tổng tiền giỏ hàng
        $cart = $cartItem->cart;
        $totalPrice = $cart->totalPrice();
    
        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được cập nhật!');
    }
    

    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);

        // Xóa món hàng khỏi giỏ hàng
        $cartItem->delete();

        // Tính toán lại tổng giá trị giỏ hàng
        $cart       = $cartItem->cart;
        $totalPrice = $cart->totalPrice();

        // Chuyển hướng trở lại giỏ hàng với thông báo thành công
        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng.');
    }
}