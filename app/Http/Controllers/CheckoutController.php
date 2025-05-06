<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function checkout()
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán');
        }
        // Lấy giỏ hàng của người dùng đã đăng nhập
        $cart = auth()->user()->cart;

        // Kiểm tra nếu giỏ hàng trống
        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống, không thể thanh toán');
        }

        $totalPrice     = $cart->totalPrice();
        $user           = auth()->user();
        $addresses      = $user->addresses;
        $currentAddress = $user->addresses()->where('is_default', true)->first();
        if (! $currentAddress) {
            $currentAddress = $user->addresses->first();
        }
        $user     = Auth::user();             // Lấy người dùng hiện tại
        $vouchers = $user->vouchers()->get(); // Lấy tất cả voucher của người dùng dưới dạng collection

        // Kiểm tra nếu vouchers là một collection và không rỗng
        if ($vouchers && $vouchers->isEmpty()) {
                                   // Nếu không có voucher, trả về một thông báo
            $vouchers = collect(); // Khởi tạo collection rỗng
        }

        return view('checkout.index', compact('cart', 'totalPrice', 'addresses', 'user', 'currentAddress', 'vouchers'));
    }

    protected function generateOrderCode()
    {
        do {
            // Tạo mã đơn hàng ngẫu nhiên (bao gồm chữ và số)
            $orderCode = strtoupper(Str::random(5)) . time();

            // Kiểm tra xem mã đơn hàng đã tồn tại trong cơ sở dữ liệu chưa
            $existingOrder = Order::where('order_code', $orderCode)->first();
        } while ($existingOrder); // Nếu mã đã tồn tại, tiếp tục tạo mã mới

        return $orderCode;
    }
    public function applyVoucher(Request $request)
    {
        $voucherCode = $request->input('voucher_code');
        $voucher = Voucher::where('code', $voucherCode)->first();
    
        if (!$voucher) {
            return response()->json(['error' => 'Voucher không hợp lệ.'], 400);
        }
    
        if ($voucher->quantity <= 0) {
            return redirect()->back()->with('error', 'Voucher này đã hết lượt sử dụng.');
        }
    
        $cart = auth()->user()->cart;
    
        if (!$cart) {
            return redirect()->back()->with('error', 'Giỏ hàng không tồn tại.');
        }
    
        $cartTotal = $cart->totalPrice();
    
        // Tính giá sau giảm
        $discountedPrice = $cartTotal - ($cartTotal * $voucher->discount / 100);
    
        // Trừ số lượng voucher
        $voucher->decrement('quantity');
    
        // Lưu thông tin vào session
        session()->put('applied_voucher', [
            'code' => $voucher->code,
            'discount' => $voucher->discount,
        ]);
        session()->put('totalPrice', $discountedPrice);
    
        return redirect()->route('checkout.index')->with('success', 'Voucher đã được áp dụng thành công!');
    }    

    public function processPayment(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán');
        }

        $cart = auth()->user()->cart;

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống, không thể thanh toán');
        }

        // Validate payment method
        $validated = $request->validate([
            'payment_method' => 'required|string',
        ]);

        $user    = auth()->user();
        $address = $user->addresses()->where('is_default', true)->first();

        if (! $address) {
            return redirect()->route('checkout.index')->with('error', 'Bạn chưa có địa chỉ mặc định!');
        }

        DB::beginTransaction();
        try {
            // Tạo mã đơn hàng ngẫu nhiên và lưu vào cơ sở dữ liệu
            $orderCode = $this->generateOrderCode();
            Log::info('Generated Order Code: ' . $orderCode);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'recipient_name'   => $address->name,
                'status'           => 'pending',                                    // Trạng thái chờ thanh toán
                'total_price'      => session('totalPrice') ?? $cart->totalPrice(), // Sử dụng tổng tiền từ session nếu có, nếu không thì lấy tổng tiền từ giỏ hàng
                'shipping_address' => $address->detail . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city,
                'payment_method'   => $request->payment_method,
                'order_code'       => $orderCode,
            ]);

            Log::info('Order Created: ' . json_encode($order)); // Kiểm tra đơn hàng đã được tạo

            // Lưu các sản phẩm vào đơn hàng
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $cartItem->price,
                ]);

                // Trừ số lượng sản phẩm trong kho
                $product = $cartItem->product;
                $product->decrement('quantity', $cartItem->quantity);
            }
            // Nếu người dùng có chọn voucher
            if ($request->has('voucher_id')) {
                $voucher = \App\Models\Voucher::find($request->voucher_id);

                // Kiểm tra voucher tồn tại và còn số lượng
                if ($voucher && $voucher->quantity > 0) {
                    // Giảm số lượng voucher
                    $voucher->decrement('quantity');

                    // Gắn voucher vào đơn hàng nếu bạn có cột `voucher_id`
                    $order->update([
                        'voucher_id' => $voucher->id,
                    ]);
                } else {
                    DB::rollBack();
                    return redirect()->route('checkout.index')->with('error', 'Voucher không hợp lệ hoặc đã hết lượt sử dụng!');
                }
            }
            // Lưu ID đơn hàng vào session để sử dụng trong bước VNPAY
            session(['order_id' => $order->id]);

            // Xóa giỏ hàng sau khi thanh toán
            $cart->items()->delete();
            $cart->delete();

            // Xóa tổng tiền trong session sau khi thanh toán xong
            session()->forget('totalPrice');
            if ($request->payment_method == 'cod') {
                DB::commit();
                session()->flash('order', $order);
                return redirect()->route('checkout.success')->with('success', 'Đặt hàng thành công!');
            } elseif ($request->payment_method == 'vnpay') {
                // Chuyển hướng đến VNPAY
                $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnp_TmnCode    = "DS0MK0MC";
                $vnp_HashSecret = "J9LL08FH8K3ELFC6FPABPQE8CEMAQULS";
                $vnp_TxnRef     = $order->id;
                $vnp_OrderInfo  = "Thanh toán đơn hàng #$order->id";
                $vnp_Amount     = $order->total_price * 100; // Chuyển đổi ra đơn vị tiền tệ
                $vnp_Locale     = "vn";
                $vnp_ReturnUrl  = route('checkout.vnpay_return');

                // Tạo dữ liệu gửi đi
                $inputData = [
                    "vnp_Version"    => "2.1.0",
                    "vnp_TmnCode"    => $vnp_TmnCode,
                    "vnp_Amount"     => $vnp_Amount,
                    "vnp_Command"    => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode"   => "VND",
                    "vnp_IpAddr"     => $request->ip(),
                    "vnp_Locale"     => $vnp_Locale,
                    "vnp_OrderInfo"  => $vnp_OrderInfo,
                    "vnp_OrderType"  => "billpayment",
                    "vnp_ReturnUrl"  => $vnp_ReturnUrl,
                    "vnp_TxnRef"     => $vnp_TxnRef,
                ];

                // Sắp xếp và tạo chuỗi query
                ksort($inputData);
                $query = http_build_query($inputData);

                // Tạo chữ ký an toàn
                $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
                session(['vnp_secure_hash' => $secureHash]);

                // Thêm chữ ký vào query
                $query .= '&vnp_SecureHash=' . $secureHash;

                $vnp_Url .= "?" . $query;
                DB::commit();
                return redirect($vnp_Url);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'Có lỗi xảy ra!');
        }
    }
    public function vnpayReturn(Request $request)
    {
        // Kiểm tra mã phản hồi từ VNPAY
        if ($request->vnp_ResponseCode == '00') {
            $orderId = session('order_id');                  // Lấy order_id từ session
            Log::info('Order ID from session: ' . $orderId); // Kiểm tra order_id từ session

            if (! $orderId) {
                return redirect()->route('checkout.index')->with('error', 'Không tìm thấy thông tin đơn hàng.');
            }

            $order = Order::find($orderId); // Tìm đơn hàng trong cơ sở dữ liệu

            Log::info('Order details: ' . json_encode($order)); // Kiểm tra thông tin đơn hàng

            if ($order) {
                // Kiểm tra thêm về txnRef từ VNPAY để xác thực đơn hàng
                if ($request->vnp_TxnRef != $order->id) {
                    return redirect()->route('checkout.index')->with('error', 'Mã đơn hàng không khớp.');
                }

                // Cập nhật trạng thái đơn hàng thành 'successful'
                $order->status = 'pending';
                $order->save();

                // Có thể thêm các hành động khác như gửi email xác nhận, v.v.
                session()->flash('order', $order);
                return redirect()->route('checkout.success')->with('success', 'Thanh toán thành công!');
            } else {
                return redirect()->route('checkout.index')->with('error', 'Không tìm thấy thông tin đơn hàng!');
            }
        } else {
                                            // Nếu mã phản hồi không phải '00', thanh toán thất bại hoặc bị hủy
            $orderId = session('order_id'); // Lấy order_id từ session
            Log::info('Failed order ID from session: ' . $orderId);

            if (! $orderId) {
                return redirect()->route('checkout.index')->with('error', 'Không tìm thấy thông tin đơn hàng.');
            }

            $order = Order::find($orderId); // Tìm đơn hàng trong cơ sở dữ liệu

            if ($order) {
                                           // Cập nhật trạng thái đơn hàng thành 'failed' hoặc 'pending'
                $order->status = 'failed'; // Hoặc 'pending' nếu muốn để người dùng có thể thanh toán lại
                $order->save();

                // Cung cấp tùy chọn thanh toán lại
                session()->flash('order', $order);
                return redirect()->route('orders.index')->with('error', 'Thanh toán thất bại hoặc đã bị hủy. Bạn có thể thử lại!');
            } else {
                return redirect()->route('checkout.index')->with('error', 'Không tìm thấy thông tin đơn hàng!');
            }
        }
    }
    public function retryPayment($orderId)
    {
        // Find the order by ID
        $order = Order::find($orderId);

        // Check if the order exists and is in 'failed' status
        if (! $order) {
            return redirect()->route('checkout.index')->with('error', 'Không tìm thấy thông tin đơn hàng.');
        }

        if ($order->status !== 'Chưa thanh toán') {
            return redirect()->route('checkout.index')->with('error', 'Đơn hàng không phải là đơn hàng thất bại.');
        }

        // Prepare data for VNPAY payment
        $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode    = "DS0MK0MC";
        $vnp_HashSecret = "J9LL08FH8K3ELFC6FPABPQE8CEMAQULS";
        $vnp_TxnRef     = $order->id;
        $vnp_OrderInfo  = "Thanh toán đơn hàng #$order->id";
        $vnp_Amount     = $order->total_price * 100; // Convert to VND
        $vnp_Locale     = "vn";
        $vnp_ReturnUrl  = route('checkout.vnpay_return');

        // Create data to send
        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => request()->ip(),
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $vnp_TxnRef,
        ];

        // Sort and create query string
        ksort($inputData);
        $query = http_build_query($inputData);

        // Generate secure hash
        $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        session(['vnp_secure_hash' => $secureHash]);

        // Add secure hash to query
        $query .= '&vnp_SecureHash=' . $secureHash;

        $vnp_Url .= "?" . $query;

        // Redirect to VNPAY for re-payment
        return redirect($vnp_Url);
    }

    public function success()
    {
        // Kiểm tra xem có thông tin đơn hàng trong session không
        if (session('order')) {
            $order = session('order');
        } else {
            // Nếu không có thông tin đơn hàng trong session, bạn có thể điều hướng lại đến trang khác hoặc thông báo lỗi
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng!');
        }

        // Trả về view success với dữ liệu đơn hàng
        return view('checkout.success', compact('order'));
    }
    public function updateAddress(Request $request)
    {
        // Lấy ID địa chỉ đã chọn từ form
        $addressId = $request->input('address_id');

        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Tìm địa chỉ của người dùng trong cơ sở dữ liệu
        $address = $user->addresses()->find($addressId);

        // Nếu địa chỉ không tồn tại, trả về thông báo lỗi
        if (! $address) {
            return redirect()->route('checkout.index')->with('error', 'Địa chỉ không hợp lệ!');
        }

        // Đặt tất cả các địa chỉ của người dùng thành không mặc định
        $user->addresses()->update(['is_default' => false]);

        // Đặt địa chỉ đã chọn thành mặc định
        $address->is_default = true;
        $address->save();

        // Sau khi cập nhật địa chỉ, chuyển về trang thanh toán
        return redirect()->route('checkout.index')->with('success', 'Địa chỉ đã được cập nhật!');
    }
}
