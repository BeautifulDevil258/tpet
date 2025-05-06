<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Order;
use App\Models\Purchase;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function exportStatistic(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by', 'month');

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
        $data = $this->getReportData($startDate, $endDate, $groupBy);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Thời gian');
        $sheet->setCellValue('B1', 'Doanh thu');
        $sheet->setCellValue('C1', 'Số đơn hàng');
        
        $row = 2;
        foreach ($data['details'] as $record) {
            $sheet->setCellValue("A$row", $record['time']);
            $sheet->setCellValue("B$row", $record['revenue']);
            $sheet->setCellValue("C$row", $record['orders']);
            $row++;
        }
        
        $filePath = storage_path('app/public/report.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportPDF($startDate, $endDate, $groupBy)
    {
        $data = $this->getReportData($startDate, $endDate, $groupBy);
        $html = view('reports.report_pdf', ['data' => $data])->render();
        
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('report.pdf');
    }

    public function exportWord($startDate, $endDate, $groupBy)
    {
        $data = $this->getReportData($startDate, $endDate, $groupBy);
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Báo cáo Doanh Thu', ['bold' => true, 'size' => 14]);
        $section->addText('Thời gian: ' . ($startDate ?: 'Tất cả') . ' - ' . ($endDate ?: 'Tất cả'));
        
        $table = $section->addTable();
        $table->addRow();
        $table->addCell()->addText('Thời gian', ['bold' => true]);
        $table->addCell()->addText('Doanh thu', ['bold' => true]);
        $table->addCell()->addText('Số đơn hàng', ['bold' => true]);

        foreach ($data['details'] as $record) {
            $table->addRow();
            $table->addCell()->addText($record['time']);
            $table->addCell()->addText(number_format($record['revenue'], 0, ',', '.') . ' VND');
            $table->addCell()->addText($record['orders']);
        }

        $filePath = storage_path('app/public/report.docx');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function getReportData($startDate, $endDate, $groupBy)
    {
        if (!$startDate) {
            $startDate = '2000-01-01';
        }
        if (!$endDate) {
            $endDate = now();
        }

        $groupFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'year' => '%Y',
            default => '%Y-%m',
        };

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '$groupFormat') as time, COUNT(id) as orders, SUM(total_price) as revenue")
            ->groupBy('time')
            ->orderBy('time')
            ->get();

        $totalOrders = $orders->sum('orders');
        $totalRevenue = $orders->sum('revenue');
        $totalPurchaseCost = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('cost_price');
        $profit = $totalRevenue - $totalPurchaseCost;

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_purchase_cost' => $totalPurchaseCost,
            'profit' => $profit,
            'details' => $orders->toArray(),
        ];
    }
}
