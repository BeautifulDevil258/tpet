<?php

namespace App\Http\Controllers;

use App\Models\Product;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ChatBotController extends Controller
{
    public function respond(Request $request)
{
    $query = mb_strtolower($request->input('query')); // Chuyển câu hỏi thành chữ thường để dễ xử lý

    // Xử lý các câu hỏi theo kịch bản
    if (strpos($query, 'chào') !== false || strpos($query, 'bạn là ai') !== false) {
        return $this->welcomeResponse($request);
    }

    if (strpos($query, 'sản phẩm giảm giá') !== false || strpos($query, 'khuyến mãi') !== false) {
        return $this->promotionInfo($request);
    }

    if (strpos($query, 'sản phẩm đặc biệt') !== false) {
        return $this->introduceProducts($request);
    }

    if (strpos($query, 'bảo hành') !== false) {
        return $this->warrantyInfo($request);
    }

    if (strpos($query, 'giao hàng') !== false) {
        return $this->shippingInfo($request);
    }

    // Tìm sản phẩm nếu không tìm thấy kịch bản nào khác
    $keywords = $this->extractKeywords($query);
    $products = Product::where(function ($q) use ($keywords) {
        foreach ($keywords as $keyword) {
            $q->orWhere('name', 'like', '%' . $keyword . '%')
              ->orWhere('description', 'like', '%' . $keyword . '%')
              ->orWhere('price', 'like', '%' . $keyword . '%');
        }
    })->get();

    // Trả lời nếu không tìm thấy sản phẩm
    if ($products->isEmpty()) {
        return response()->json([
            'response' => 'Xin lỗi, tôi không thể tìm thấy sản phẩm nào với từ khóa: "' . implode(' ', $keywords) . '".'
        ]);
    }

    // Trả về danh sách sản phẩm nếu tìm thấy
    $response = 'Tôi tìm thấy một số sản phẩm sau:<br>';
    foreach ($products as $product) {
        $response .= $product->name . ' - Giá: ' . number_format($product->price) . ' VND. '
                     . 'Xem chi tiết: <a href="' . url('/product/details/' . $product->id) . '" target="_blank" style="color: blue;">' 
                     . url('/product/' . $product->id) . '</a><br>';
    }

    return response()->json([
        'response' => $response
    ]);
}

    // Hàm trích xuất từ khóa từ câu truy vấn
    private function extractKeywords($query)
    {
        // Loại bỏ những từ không quan trọng (stop words)
        $stopWords = ['tôi', 'muốn', 'mua', 'cần', 'sản phẩm', 'là', 'với', 'giá', 'ở', 'đây', 'có'];
        $words = preg_split('/\s+/', mb_strtolower($query));
        $keywords = array_diff($words, $stopWords);

        return $keywords;
    }

    // Hàm để xử lý kịch bản chào mừng
    public function welcomeResponse(Request $request)
    {
        return response()->json([
            'response' => 'Chào bạn! Tôi là chatbot hỗ trợ của cửa hàng. Bạn có thể hỏi tôi về các sản phẩm, giá cả hoặc giúp bạn tìm kiếm thứ bạn cần. Có gì tôi có thể giúp bạn?'
        ]);
    }

    // Giới thiệu các sản phẩm đặc biệt
    public function introduceProducts(Request $request)
    {
        $response = 'Chúng tôi hiện đang có các sản phẩm đặc biệt với nhiều ưu đãi hấp dẫn. Bạn có muốn xem chi tiết không?';
        
        // Trả lại sản phẩm nổi bật
        $products = Product::where('featured', true)->take(5)->get();
        if ($products->isEmpty()) {
            $response .= ' Hiện tại không có sản phẩm nổi bật.';
        } else {
            $response .= ' Đây là một số sản phẩm nổi bật của chúng tôi: <br>';
            foreach ($products as $product) {
                $response .= $product->name . ' - Giá: ' . number_format($product->price) . ' VND. '
                             . 'Xem chi tiết: <a href="' . url('/product/' . $product->id) . '" target="_blank" style="color: blue;">' 
                             . url('/product/' . $product->id) . '</a><br>';
            }
        }

        return response()->json([
            'response' => $response
        ]);
    }

    // Hàm giới thiệu chương trình khuyến mãi
    public function promotionInfo(Request $request)
    {
        return response()->json([
            'response' => 'Hiện tại chúng tôi có chương trình giảm giá lên đến 50% cho nhiều sản phẩm. Bạn muốn xem sản phẩm được giảm giá không?'
        ]);
    }

    // Giải đáp các câu hỏi về chính sách bảo hành
    public function warrantyInfo(Request $request)
    {
        return response()->json([
            'response' => 'Sản phẩm của chúng tôi có bảo hành 12 tháng. Nếu bạn gặp vấn đề với sản phẩm, vui lòng liên hệ với chúng tôi để được hỗ trợ.'
        ]);
    }

    // Trả lời câu hỏi về giao hàng
    public function shippingInfo(Request $request)
    {
        return response()->json([
            'response' => 'Chúng tôi cung cấp dịch vụ giao hàng miễn phí trong 3-5 ngày làm việc. Bạn có cần giúp đỡ về việc giao hàng không?'
        ]);
    }
}
