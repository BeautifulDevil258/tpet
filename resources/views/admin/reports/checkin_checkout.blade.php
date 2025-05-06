@extends('layouts.adminapp')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">Báo cáo Check-in/Check-out Nhân Viên</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Mã nhân viên</th>
                    <th>Họ và tên</th>
                    <th>Tổng ngày làm việc</th>
                    <th>Số lần đi muộn</th>
                    <th>Tổng số phút đi muộn</th>
                    <th>Số lần về sớm</th>
                    <th>Tổng số phút về sớm</th>
                    <th>Tổng số giờ làm việc</th>
                    <th>Số lần quên check-in/check-out</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData as $data)
                    <tr>
                        <td>{{ $data['employee_id'] }}</td>
                        <td>{{ $data['name'] }}</td>
                        <td>{{ $data['total_days'] }}</td>
                        <td>{{ $data['late_days'] }}</td>
                        <td>{{ $data['total_late_hours'] }} giờ</td>
                        <td>{{ $data['early_leave_days'] }}</td>
                        <td>{{ $data['total_early_leave_hours'] }} giờ</td>
                        <td>{{ $data['total_work_hours'] }} giờ</td>
                        <td>{{ $data['missed_check_ins'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form id="reportForm" class="d-flex">
            <div class="form-group mr-3">
                <label for="fileFormat" class="sr-only">Chọn định dạng báo cáo</label>
                <select id="fileFormat" class="form-control">
                    <option value="xlsx">Excel (.xlsx)</option>
                    <option value="pdf">PDF (.pdf)</option>
                    <option value="docx">Word (.docx)</option>
                </select>
            </div>
            <button type="button" id="downloadReport" class="btn btn-primary">
                <i class="fas fa-download"></i> Tải báo cáo
            </button>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
    document.getElementById('downloadReport').addEventListener('click', function() {
        var selectedFormat = document.getElementById('fileFormat').value;
        
        // Gửi request về server để lấy dữ liệu báo cáo theo định dạng đã chọn
        fetch('/report/export-check?format=' + selectedFormat)
            .then(response => response.blob())
            .then(blob => {
                // Lưu file với FileSaver.js
                var fileName = 'report.' + selectedFormat;
                saveAs(blob, fileName);
            })
            .catch(error => console.error('Error exporting report:', error));
    });
</script>
@endsection
