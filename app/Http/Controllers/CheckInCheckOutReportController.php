<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class CheckInCheckOutReportController extends Controller
{
    public function index()
    {
        $employees = Admin::with('checkInCheckOutLogs')->get();

        // Tính toán dữ liệu báo cáo cho mỗi nhân viên
        $reportData = $employees->map(function ($employee) {
            $report = $this->generateReportForEmployee($employee);

            return [
                'employee_id'             => $report['employee_id'],
                'name'                    => $report['name'],
                'total_days'              => $report['total_days'],
                'late_days'               => $report['late_days'],
                'total_late_hours'        => $report['total_late_hours'], // Hiển thị giờ
                'early_leave_days'        => $report['early_leave_days'],
                'total_early_leave_hours' => $report['total_early_leave_hours'], // Hiển thị giờ
                'total_work_hours'        => $report['total_work_hours'],
                'missed_check_ins'        => $report['missed_check_ins'],
            ];
        });

        return view('admin.reports.checkin_checkout', compact('reportData'));
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx'); // Mặc định là Excel

        switch ($format) {
            case 'xlsx':
                return $this->exportExcel();
            case 'pdf':
                return $this->exportPdf();
            case 'docx':
                return $this->exportWord();
            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }
    private function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Add column headers
        $sheet->setCellValue('A1', 'Mã Nhân Viên');
        $sheet->setCellValue('B1', 'Họ và Tên');
        $sheet->setCellValue('C1', 'Tổng Số Ngày Làm Việc');
        $sheet->setCellValue('D1', 'Số Ngày Đi Muộn');
        $sheet->setCellValue('E1', 'Tổng Số Giờ Đi Muộn');
        $sheet->setCellValue('F1', 'Số Ngày Về Sớm');
        $sheet->setCellValue('G1', 'Tổng Số Giờ Về Sớm');
        $sheet->setCellValue('H1', 'Tổng Số Giờ Làm Việc');
        $sheet->setCellValue('I1', 'Số Lần Quên Check-in/Check-out');

        // Apply basic styling to header row
        $headerStyle = [
            'font'      => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Fetch employee data
        $employees = Admin::with('checkInCheckOutLogs')->get();
        $row       = 2; // Start from row 2

        foreach ($employees as $employee) {
            // Generate report for each employee
            $report = $this->generateReportForEmployee($employee);

            // Set cell values for each row
            $sheet->setCellValue('A' . $row, $report['employee_id']);
            $sheet->setCellValue('B' . $row, $report['name']);
            $sheet->setCellValue('C' . $row, $report['total_days']);
            $sheet->setCellValue('D' . $row, $report['late_days']);
            $sheet->setCellValue('E' . $row, $report['total_late_hours']);
            $sheet->setCellValue('F' . $row, $report['early_leave_days']);
            $sheet->setCellValue('G' . $row, $report['total_early_leave_hours']);
            $sheet->setCellValue('H' . $row, $report['total_work_hours']);
            $sheet->setCellValue('I' . $row, $report['missed_check_ins']);

            // Adjust column widths for better appearance
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);

            $row++;
        }

        // Create writer object
        $writer  = new Xlsx($spreadsheet);
        $content = $writer->save('php://output');

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="bao_cao_check_in_check_out.xlsx"',
        ]);
    }
    public function exportPdf()
    {
        $employees = Admin::with('checkInCheckOutLogs')->get();

        // Khởi tạo đối tượng DomPDF
        $dompdf = new Dompdf();

        // Tạo nội dung HTML cho báo cáo với CSS
        $html = '
        <style>
            body {
                font-family: "DejaVu Sans", sans-serif;
                margin: 10px;
                font-size: 8px;
            }
            h1 {
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
                word-wrap: break-word;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>';

        $html .= '<h1>Báo cáo Check-in/Check-out Nhân Viên</h1>';
        $html .= '<table>';
        $html .= '<thead><tr><th>Mã nhân viên</th><th>Họ và tên</th><th>Tổng ngày làm việc</th><th>Số lần đi muộn</th><th>Tổng số giờ đi muộn</th><th>Số lần về sớm</th><th>Tổng số giờ về sớm</th><th>Tổng số giờ làm việc</th><th>Số lần quên check-in/check-out</th></tr></thead>';
        $html .= '<tbody>';

        // Sử dụng dữ liệu báo cáo đã tính toán từ controller
        foreach ($employees as $employee) {
            $report = $this->generateReportForEmployee($employee);
            $html .= "<tr>
                        <td>{$report['employee_id']}</td>
                        <td>{$report['name']}</td>
                        <td>{$report['total_days']}</td>
                        <td>{$report['late_days']}</td>
                        <td>{$report['total_late_hours']} phút</td>
                        <td>{$report['early_leave_days']}</td>
                        <td>{$report['total_early_leave_hours']} phút</td>
                        <td>{$report['total_work_hours']} giờ</td>
                        <td>{$report['missed_check_ins']}</td>
                      </tr>";
        }

        $html .= '</tbody></table>';

        // Load nội dung HTML vào DomPDF
        $dompdf->loadHtml($html);

        // Tạo PDF
        $dompdf->render();

        // Lưu PDF vào output (Trả về cho người dùng)
        return $dompdf->stream('check_in_check_out_report.pdf', [
            'Attachment' => 1, // Thiết lập để tải file về thay vì xem trực tiếp
        ]);
    }

    public function exportWord()
    {
        $employees = Admin::with('checkInCheckOutLogs')->get();

        // Khởi tạo đối tượng PhpWord
        $phpWord = new PhpWord();

        // Thêm trang mới
        $section = $phpWord->addSection();

        // Thêm tiêu đề
        $section->addTitle('Báo cáo Check-in/Check-out Nhân Viên', 1);

        // Tạo bảng
        $table = $section->addTable();

        // Thêm hàng tiêu đề bảng
        $table->addRow();
        $table->addCell(2000)->addText('Mã nhân viên');
        $table->addCell(2000)->addText('Họ và tên');
        $table->addCell(2000)->addText('Tổng ngày làm việc');
        $table->addCell(2000)->addText('Số lần đi muộn');
        $table->addCell(2000)->addText('Tổng số giờ đi muộn');
        $table->addCell(2000)->addText('Số lần về sớm');
        $table->addCell(2000)->addText('Tổng số giờ về sớm');
        $table->addCell(2000)->addText('Tổng số giờ làm việc');
        $table->addCell(2000)->addText('Số lần quên check-in/check-out');

        // Thêm dữ liệu vào bảng
        foreach ($employees as $employee) {
            $report = $this->generateReportForEmployee($employee);
            $table->addRow();
            $table->addCell(2000)->addText($report['employee_id']);
            $table->addCell(2000)->addText($report['name']);
            $table->addCell(2000)->addText($report['total_days']);
            $table->addCell(2000)->addText($report['late_days']);
            $table->addCell(2000)->addText($report['total_late_hours'] . ' phút');
            $table->addCell(2000)->addText($report['early_leave_days']);
            $table->addCell(2000)->addText($report['total_early_leave_hours'] . ' phút');
            $table->addCell(2000)->addText($report['total_work_hours'] . ' giờ');
            $table->addCell(2000)->addText($report['missed_check_ins']);
        }

        // Định dạng viền bảng cho từng cell
        foreach ($table->getRows() as $row) {
            foreach ($row->getCells() as $cell) {
                $cell->getStyle()->setBorderColor('000000'); // Màu viền (đen)
                $cell->getStyle()->setBorderSize(6);         // Kích thước viền
            }
        }

        // Lưu file Word vào thư mục tạm
        $fileName  = 'check_in_check_out_report.docx';
        $tempFile  = storage_path('app/public/' . $fileName);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        // Kiểm tra nếu file đã được lưu thành công
        try {
            $objWriter->save($tempFile);
        } catch (\Exception $e) {
            // In lỗi nếu có sự cố
            dd($e->getMessage());
        }

        // Trả về file cho người dùng tải về
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    private function generateReportForEmployee($employee)
    {
        $standardCheckIn  = Carbon::parse('08:00:00');
        $standardCheckOut = Carbon::parse('17:00:00');

        $totalDays              = $employee->checkInCheckOutLogs->count();
        $lateDays               = 0;
        $earlyLeaveDays         = 0;
        $totalLateMinutes       = 0;
        $totalEarlyLeaveMinutes = 0;
        $totalWorkHours         = 0;
        $missedCheckIns         = 0;

        foreach ($employee->checkInCheckOutLogs as $log) {
            if (! $log->check_in_time || ! $log->check_out_time) {
                $missedCheckIns++;
                continue;
            }

            $checkIn  = Carbon::parse($log->check_in_time);
            $checkOut = Carbon::parse($log->check_out_time);

            // Tính toán đi muộn, lấy giá trị tuyệt đối của thời gian muộn
            if ($checkIn->gt($standardCheckIn)) {
                $lateDays++;
                $totalLateMinutes += abs($checkIn->diffInMinutes($standardCheckIn)); // Lấy giá trị tuyệt đối
            }

            // Tính toán về sớm, lấy giá trị tuyệt đối của thời gian về sớm
            if ($checkOut->lt($standardCheckOut)) {
                $earlyLeaveDays++;
                $totalEarlyLeaveMinutes += abs($standardCheckOut->diffInMinutes($checkOut)); // Lấy giá trị tuyệt đối
            }

            $totalWorkHours += $log->work_hours;
            $totalWorkHours = round($totalWorkHours, 3);
        }

        // Chuyển đổi phút sang giờ
        $totalLateHours       = round($totalLateMinutes / 60, 2);
        $totalEarlyLeaveHours = round($totalEarlyLeaveMinutes / 60, 2);

        return [
            'employee_id'             => $employee->id,
            'name'                    => $employee->name,
            'total_days'              => $totalDays,
            'late_days'               => $lateDays,
            'total_late_hours'        => $totalLateHours, // Sử dụng giờ thay vì phút
            'early_leave_days'        => $earlyLeaveDays,
            'total_early_leave_hours' => $totalEarlyLeaveHours, // Sử dụng giờ thay vì phút
            'total_work_hours'        => $totalWorkHours,
            'missed_check_ins'        => $missedCheckIns,
        ];
    }
}
