<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tham số từ request
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $groupBy   = $request->input('group_by', 'month'); // Mặc định là theo tháng
        if ($startDate && $endDate) {                                       
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay(); 
            $endDate   = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();    
        }

        // Tính tổng số đơn hàng và tổng doanh thu theo khoảng thời gian đã chọn
        $query = Order::whereIn('status', ['rated', 'completed']);

        // Nếu có mốc thời gian, thêm điều kiện lọc
        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $totalOrders  = $query->count();            
        $totalRevenue = $query->sum('total_price');

        // Nếu lọc theo tháng
        if ($groupBy == 'month') {
            // Lấy năm hiện tại nếu không có start_date
            $year = $startDate ? date('Y', strtotime($startDate)) : date('Y');

            // Tạo danh sách 12 tháng mặc định với doanh thu = 0
            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . $year] = [
                    'total_revenue' => 0,
                    'total_orders'  => 0,
                ];
            }

            // Truy vấn doanh thu theo tháng dựa trên updated_at
            $revenues = Order::selectRaw('MONTH(updated_at) as month, YEAR(updated_at) as year, SUM(total_price) as total_revenue')
                ->whereIn('status', ['rated', 'completed'])
                ->whereYear('updated_at', $year)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->groupBy(DB::raw('YEAR(updated_at)'), DB::raw('MONTH(updated_at)'))
                ->orderBy(DB::raw('MONTH(updated_at)'))
                ->get();

            // Truy vấn số lượng đơn hàng theo tháng dựa trên updated_at
            $orderCounts = Order::selectRaw('MONTH(updated_at) as month, YEAR(updated_at) as year, COUNT(*) as total_orders')
                ->whereIn('status', ['rated', 'completed'])
                ->whereYear('updated_at', $year)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->groupBy(DB::raw('YEAR(updated_at)'), DB::raw('MONTH(updated_at)'))
                ->orderBy(DB::raw('MONTH(updated_at)'))
                ->get();

            // Cập nhật doanh thu vào mảng mặc định
            foreach ($revenues as $item) {
                $monthKey                           = str_pad($item->month, 2, '0', STR_PAD_LEFT) . '-' . $item->year;
                $months[$monthKey]['total_revenue'] = $item->total_revenue;
            }

            // Cập nhật số đơn hàng vào mảng mặc định
            foreach ($orderCounts as $item) {
                $monthKey                          = str_pad($item->month, 2, '0', STR_PAD_LEFT) . '-' . $item->year;
                $months[$monthKey]['total_orders'] = $item->total_orders;
            }

            // Tạo mảng để truyền ra view
            $dates       = array_keys($months);
            $revenues    = array_column($months, 'total_revenue');
            $orderCounts = array_column($months, 'total_orders');
        }
        // Nếu lọc theo năm
        else {
            $dates = Order::selectRaw('YEAR(updated_at) as year')
                ->whereIn('status', ['rated', 'completed'])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->groupBy(DB::raw('YEAR(updated_at)'))
                ->orderBy(DB::raw('YEAR(updated_at)'))
                ->pluck('year')
                ->toArray();

            $revenues = Order::selectRaw('YEAR(updated_at) as year, SUM(total_price) as total_revenue')
                ->whereIn('status', ['rated', 'completed'])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->groupBy(DB::raw('YEAR(updated_at)'))
                ->orderBy(DB::raw('YEAR(updated_at)'))
                ->pluck('total_revenue')
                ->toArray();

            $orderCounts = Order::selectRaw('YEAR(updated_at) as year, COUNT(*) as total_orders')
                ->whereIn('status', ['rated', 'completed'])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->groupBy(DB::raw('YEAR(updated_at)'))
                ->orderBy(DB::raw('YEAR(updated_at)'))
                ->pluck('total_orders')
                ->toArray();
        }

        // Truy vấn top 5 sản phẩm bán chạy theo số lượng đã bán
        $topProductsByQuantity = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['rated', 'completed'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('orders.updated_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $topProducts = $topProductsByQuantity->map(function ($item) {
            $item->product_name = Product::find($item->product_id)->name;
            return $item;
        });
        // Tính tổng chi phí nhập hàng
        // Tính tổng chi phí nhập hàng
        $totalImportCost = DB::table('import_history')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->sum('total_cost'); // Đảm bảo 'import_price' là cột chứa chi phí nhập hàng thực tế
                             //Lợi nhuận
        $totalProfit = $totalRevenue - $totalImportCost;

        // Trả về view
        return view('admin.statistics.index', compact(
            'totalOrders',
            'totalRevenue',
            'dates',
            'revenues',
            'orderCounts',
            'topProducts',
            'totalImportCost',
            'totalProfit'
        ));
    }
    public function exports(Request $request)
    {
        $format    = $request->input('format', 'xlsx');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $groupBy   = $request->input('group_by', 'month');

        switch ($format) {
            case 'xlsx':
                return $this->exportExcel($startDate, $endDate, $groupBy);
            case 'pdf':
                return $this->exportPDF($startDate, $endDate, $groupBy);
            case 'docx':
                return $this->exportWord($startDate, $endDate, $groupBy);
            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }
    public function exportExcel($startDate, $endDate, $groupBy)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $yearTitle   = '';

        // Lấy dữ liệu thống kê
        $data = $this->getStatisticsData($startDate, $endDate, $groupBy);

        // Kiểm tra nếu groupBy là 'month' và danh sách ngày không rỗng
        if ($groupBy == 'month' && ! empty($data['dates'])) {
            $firstDate = reset($data['dates']);              // Lấy giá trị đầu tiên trong mảng
            if (preg_match('/^\d{4}-\d{2}$/', $firstDate)) { // Kiểm tra định dạng YYYY-MM
                $yearTitle = ' Năm ' . substr($firstDate, 0, 4);
            }
        }

        // Đặt tiêu đề vào ô A1
        $sheet->setCellValue('A1', 'Thống kê doanh thu' . $yearTitle);
        $sheet->setCellValue('A2', 'Ngày bắt đầu: ' . $startDate);
        $sheet->setCellValue('B2', 'Ngày kết thúc: ' . $endDate);

        // Headers
        $headers     = ['Thời gian', 'Tổng đơn hàng', 'Tổng doanh thu', 'Tổng chi phí nhập hàng', 'Lợi nhuận'];
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '4', $header);
            $columnIndex++;
        }

        // Áp dụng kiểu cho hàng tiêu đề
        $headerStyle = [
            'font'      => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders'   => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A4:E4')->applyFromArray($headerStyle);
// Căn giữa dữ liệu trong các ô (A5 đến E cuối cùng)
        // Ghi dữ liệu vào Excel
        $row             = 5;
        $totalOrderCount = 0;
        $totalRevenue    = 0;
        $totalImportCost = 0;
        $totalProfit     = 0;
        foreach ($data['dates'] as $index => $date) {
            $sheet->setCellValue("A$row", $date);
            $sheet->setCellValue("B$row", $data['orderCounts'][$index]);
            $sheet->setCellValue("C$row", number_format($data['revenues'][$index]));

            // Lấy chi phí nhập hàng cho tháng hiện tại
            $importCost = $data['importCosts'][$index] ?? 0;
            $profit     = $data['revenues'][$index] - $importCost;

            $sheet->setCellValue("D$row", number_format($importCost));
            $sheet->setCellValue("E$row", number_format($profit));

            // Cộng dồn tổng
            $totalOrderCount += $data['orderCounts'][$index];
            $totalRevenue += $data['revenues'][$index];
            $totalImportCost += $importCost;
            $totalProfit += $profit;
            $row++;
        }
        // Thêm dòng tổng ở cuối
        $sheet->setCellValue("A$row", 'Tổng');
        $sheet->getStyle("A$row")->getFont()->setBold(true);
        $sheet->setCellValue("B$row", number_format($totalOrderCount));
        $sheet->getStyle("B$row")->getFont()->setBold(true);
        $sheet->setCellValue("C$row", number_format($totalRevenue));
        $sheet->getStyle("C$row")->getFont()->setBold(true);
        $sheet->setCellValue("D$row", number_format($totalImportCost));
        $sheet->getStyle("D$row")->getFont()->setBold(true);
        $sheet->setCellValue("E$row", number_format($totalProfit));
        $sheet->getStyle("E$row")->getFont()->setBold(true);
        // Căn giữa dữ liệu trong các ô (A5 đến E cuối cùng)
        $sheet->getStyle("A5:E$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A5:E$row")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // Tự động điều chỉnh độ rộng cột
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Xuất file
        $writer   = new Xlsx($spreadsheet);
        $fileName = 'thong_ke.xlsx';
        $filePath = storage_path($fileName);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    public function exportPDF($startDate, $endDate, $groupBy)
    {
        $data = $this->getStatisticsData($startDate, $endDate, $groupBy);

        $html = '<h2>Thống kê doanh thu</h2>';
        $html .= '<p>Ngày bắt đầu: ' . $startDate . ' - Ngày kết thúc: ' . $endDate . '</p>';
        $html .= '<table border="1" cellpadding="5"><tr><th>Thời gian</th><th>Tổng đơn hàng</th><th>Tổng doanh thu</th><th>Tổng chi phí nhập hàng</th><th>Lợi nhuận</th></tr>';

        $totalOrderCount = 0;
        $totalRevenue    = 0;
        $totalImportCost = 0;
        $totalProfit     = 0;

        foreach ($data['dates'] as $index => $date) {
            $importCost = $data['importCosts'][$index] ?? 0; // Lấy chi phí nhập hàng cho tháng
            $profit     = $data['revenues'][$index] - $importCost;

            $html .= "<tr><td>{$date}</td><td>{$data['orderCounts'][$index]}</td><td>" . number_format($data['revenues'][$index]) . "</td><td>" . number_format($importCost) . "</td><td>" . number_format($profit) . "</td></tr>";

            // Cộng dồn tổng
            $totalOrderCount += $data['orderCounts'][$index];
            $totalRevenue += $data['revenues'][$index];
            $totalImportCost += $importCost;
            $totalProfit += $profit;
        }

        // Thêm dòng tổng ở cuối
        $html .= "<tr><td><strong>Tổng</strong></td><td><strong>{$totalOrderCount}</strong></td><td><strong>" . number_format($totalRevenue) . "</strong></td><td><strong>" . number_format($totalImportCost) . "</strong></td><td><strong>" . number_format($totalProfit) . "</strong></td></tr>";
        $html .= '</table>';

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            'thong_ke.pdf'
        );
    }
    public function exportWord($startDate, $endDate, $groupBy)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Tiêu đề chính
        $section->addText('Thống kê doanh thu', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addText("Ngày bắt đầu: $startDate - Ngày kết thúc: $endDate", ['size' => 12]);
        $section->addTextBreak();

        // Lấy dữ liệu thống kê
        $data = $this->getStatisticsData($startDate, $endDate, $groupBy);

        // Tạo bảng
        $tableStyle = [
            'borderSize'  => 6,
            'borderColor' => '000000',
            'cellMargin'  => 80,
            'alignment'   => 'center',
        ];
        $phpWord->addTableStyle('OrderStats', $tableStyle);

        $table = $section->addTable('OrderStats');

        // Tiêu đề cột
        $headerStyle   = ['bold' => true, 'size' => 12];
        $cellHCentered = ['alignment' => 'center'];

        $table->addRow();
        $table->addCell(3500, ['valign' => 'center'])->addText('Thời gian', $headerStyle, $cellHCentered);
        $table->addCell(2500, ['valign' => 'center'])->addText('Tổng đơn hàng', $headerStyle, $cellHCentered);
        $table->addCell(3000, ['valign' => 'center'])->addText('Tổng doanh thu', $headerStyle, $cellHCentered);
        $table->addCell(3000, ['valign' => 'center'])->addText('Tổng chi phí nhập hàng', $headerStyle, $cellHCentered);
        $table->addCell(3000, ['valign' => 'center'])->addText('Lợi nhuận', $headerStyle, $cellHCentered);

        // Biến tổng
        $totalOrderCount = 0;
        $totalRevenue    = 0;
        $totalImportCost = 0;
        $totalProfit     = 0;

        // Dữ liệu bảng
        foreach ($data['dates'] as $index => $date) {
            $table->addRow();
            $table->addCell(3500)->addText($date, ['size' => 11]);
            $table->addCell(2500)->addText(number_format($data['orderCounts'][$index]), ['size' => 11], $cellHCentered);
            $table->addCell(3000)->addText(number_format($data['revenues'][$index]) . ' VND', ['size' => 11], $cellHCentered);

            // Lấy chi phí nhập hàng cho tháng hiện tại
            $importCost = $data['importCosts'][$index] ?? 0;
            $profit     = $data['revenues'][$index] - $importCost;

            $table->addCell(3000)->addText(number_format($importCost) . ' VND', ['size' => 11], $cellHCentered);
            $table->addCell(3000)->addText(number_format($profit) . ' VND', ['size' => 11], $cellHCentered);

            // Cộng dồn tổng
            $totalOrderCount += $data['orderCounts'][$index];
            $totalRevenue += $data['revenues'][$index];
            $totalImportCost += $importCost;
            $totalProfit += $profit;
        }

        // Thêm dòng tổng ở cuối
        $table->addRow();
        $table->addCell(3500)->addText('Tổng', ['bold' => true, 'size' => 11]);
        $table->addCell(2500)->addText(number_format($totalOrderCount), ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(3000)->addText(number_format($totalRevenue) . ' VND', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(3000)->addText(number_format($totalImportCost) . ' VND', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(3000)->addText(number_format($totalProfit) . ' VND', ['bold' => true, 'size' => 11], $cellHCentered);

        // Xuất file Word
        $fileName = 'thong_ke.docx';
        $filePath = storage_path($fileName);
        $writer   = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function getStatisticsData($startDate, $endDate, $groupBy)
    {
        $query = Order::whereIn('status', ['rated', 'completed']);

        // Chuyển đổi ngày về định dạng chuẩn Y-m-d H:i:s
        if ($startDate && $endDate) {
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate);
            $endDate   = Carbon::createFromFormat('d/m/Y', $endDate);
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        if ($groupBy == 'month') {
            // Sử dụng định dạng m/Y (tháng/năm) cho ngày
            $data = $query->selectRaw('DATE_FORMAT(updated_at, "%m/%Y") as date, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
                ->groupBy(DB::raw('DATE_FORMAT(updated_at, "%m/%Y")'))
                ->orderBy(DB::raw('DATE_FORMAT(updated_at, "%m/%Y")'))
                ->get();
        } else {
            // Định dạng theo năm
            $data = $query->selectRaw('YEAR(updated_at) as date, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
                ->groupBy(DB::raw('YEAR(updated_at)'))
                ->orderBy(DB::raw('YEAR(updated_at)'))
                ->get();
        }

        // Lấy chi phí nhập hàng cho từng tháng
        $totalImportCost = DB::table('import_history')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->selectRaw('DATE_FORMAT(updated_at, "%m/%Y") as month, SUM(total_cost) as total_import_cost')
            ->groupBy(DB::raw('DATE_FORMAT(updated_at, "%m/%Y")'))
            ->orderBy(DB::raw('DATE_FORMAT(updated_at, "%m/%Y")'))
            ->get();

        // Tính doanh thu từng tháng
        $revenues = $data->pluck('total_revenue')->toArray();

        // Tạo một mảng lợi nhuận cho từng tháng
        $profits     = [];
        $importCosts = [];

        // Duyệt qua các tháng và tính lợi nhuận cho từng tháng
        foreach ($data as $index => $monthData) {
            $month   = $monthData->date;
            $revenue = $revenues[$index];

            // Tìm tổng chi phí nhập hàng cho tháng hiện tại
            $importCost = $totalImportCost->firstWhere('month', $month)->total_import_cost ?? 0;

            // Lưu chi phí nhập hàng vào mảng importCosts
            $importCosts[] = $importCost;

            // Tính lợi nhuận cho tháng này
            $profits[] = $revenue - $importCost;
        }

        return [
            'dates'       => $data->pluck('date')->toArray(),
            'orderCounts' => $data->pluck('total_orders')->toArray(),
            'revenues'    => $revenues,
            'importCosts' => $importCosts, // Lưu chi phí nhập hàng cho từng tháng
            'profits'     => $profits,     // Lợi nhuận tính được cho từng tháng
        ];
    }

}
