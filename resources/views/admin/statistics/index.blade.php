@extends('layouts.adminapp')
@section('content')

<style>
/* Thêm kiểu cho các biểu đồ */
#revenueChart,
#ordersChart {
    width: 100% !important;
    max-width: 600px;
    /* Giảm chiều rộng */
    margin: auto;
}

/* Biểu đồ top sản phẩm bán chạy (donut) */
#topProductsChart {
    width: 100% !important;
    max-width: 350px;
    /* Giảm kích thước biểu đồ donut */
    margin: auto;
}

/* Thêm kiểu cho các thẻ card */
.card-body {
    padding: 35px;
}

.font-weight-bold {
    font-weight: 700;
}

.list-group-item {
    background-color: #fff;
    border: 1px solid #ddd;
    font-size: 16px;
}

.list-group-item:hover {
    background-color: #f1f1f1;
}

.badge-primary {
    background-color: #007bff;
}

/* Cải thiện kiểu dáng các label */
label {
    font-size: 16px;
}

/* Tạo khoảng cách tốt cho form */
.form-control {
    border-radius: 6px;
    box-shadow: none;
}

/* Tăng kích thước các nút */
.form-control,
.btn-danger {
    font-size: 16px;
}

.text-dark {
    color: #333;
}
</style>

<div class="container mt-5">
<form action="{{ route('export.statistic') }}" method="GET">
    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
    <input type="hidden" name="group_by" value="{{ request('group_by', 'month') }}">

    <div class="text-end">
        <button type="submit" name="format" value="xlsx" class="btn btn-success">
            Xuất Excel <i class="fas fa-file-excel"></i>
        </button>

        <button type="submit" name="format" value="pdf" class="btn btn-danger">
            Xuất PDF <i class="fas fa-file-pdf"></i>
        </button>

        <button type="submit" name="format" value="docx" class="btn btn-primary">
            Xuất Word <i class="fas fa-file-word"></i>
        </button>
    </div>
</form>

    <!-- Tiêu đề -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="font-weight-bold text-primary">Thống Kê Đơn Hàng và Doanh Thu</h1>
        </div>
    </div>

    <!-- Bộ lọc -->
    <form method="GET" action="{{ route('statistics.index') }}" id="filterForm">
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="start_date">Ngày bắt đầu:</label>
                <input type="text" name="start_date" id="start_date"
                       class="form-control" value="{{ request('start_date') }}"
                       placeholder="Chọn ngày">
            </div>
            <div class="col-md-4">
                <label for="end_date">Ngày kết thúc:</label>
                <input type="text" name="end_date" id="end_date"
                       class="form-control" value="{{ request('end_date') }}"
                       placeholder="Chọn ngày">
            </div>
            <div class="col-md-4">
                <label for="group_by">Loại biểu đồ:</label>
                <select name="group_by" id="group_by" class="form-control">
                    <option value="month"
                            {{ request('group_by') == 'month' || request('group_by') == '' ? 'selected' : '' }}>Theo Tháng
                    </option>
                    <option value="year" {{ request('group_by') == 'year' ? 'selected' : '' }}>Theo Năm</option>
                </select>
            </div>
        </div>
        <div class="d-flex">
            <button type="button" class="btn btn-secondary mb-3 ms-auto" id="resetButton">Xóa Bộ Lọc</button>
        </div>
    </form>

    <!-- Tổng doanh thu và tổng đơn hàng -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Tổng Doanh Thu</h5>
                    <h3>{{ number_format($totalRevenue, 0, ',', '.') }} VND</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Tổng Đơn Hàng</h5>
                    <h3>{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Tổng Chi Phí Nhập Hàng</h5>
                    <h3>{{ number_format($totalImportCost, 0, ',', '.') }} VND</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Lợi nhuận</h5>
                    <h3>{{ number_format($totalProfit, 0, ',', '.') }} VND</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu và đơn hàng (Cùng 1 hàng) -->
    <div class="row">
        <!-- Biểu đồ Doanh Thu -->
        <div class="col-md-6">
            <canvas id="revenueChart" width="600" height="300"></canvas>
        </div>
        <!-- Biểu đồ Đơn Hàng -->
        <div class="col-md-6">
            <canvas id="ordersChart" width="600" height="300"></canvas>
        </div>
    </div>

    <!-- Biểu đồ Top sản phẩm bán chạy -->
    <div class="row mt-5">
        <div class="col-md-12 text-center">
            <h3>Top 5 Sản Phẩm Bán Chạy</h3>
            <canvas id="topProductsChart" width="350" height="350"></canvas>
        </div>
    </div>
    <!-- Danh sách top 5 sản phẩm bán chạy -->
    <div class="mt-5">
        <h4 class="text-center font-weight-bold text-dark">Danh sách top 5 sản phẩm bán chạy:</h4>
        <ul class="list-group list-group-flush">
            @foreach($topProducts as $product)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $product->product_name }}</span>
                <span class="badge badge-primary">{{ $product->total_quantity }} sản phẩm</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Gửi form tự động khi thay đổi bộ lọc
document.querySelectorAll('.form-control').forEach(item => {
    item.addEventListener('change', () => {
        document.getElementById('filterForm').submit(); // Gửi form khi có sự thay đổi
    });
});

// Xử lý nút xóa bộ lọc
document.getElementById('resetButton').addEventListener('click', () => {
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('group_by').value = 'month';
    document.getElementById('filterForm').submit();
});

// Khởi tạo Flatpickr cho các trường ngày
flatpickr("#start_date", {
    dateFormat: "d/m/Y", // Định dạng ngày/tháng/năm
    locale: "vi" // Tùy chọn ngôn ngữ Tiếng Việt
});

flatpickr("#end_date", {
    dateFormat: "d/m/Y", // Định dạng ngày/tháng/năm
    locale: "vi" // Tùy chọn ngôn ngữ Tiếng Việt
});

// Biểu đồ doanh thu
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($dates) !!}, // Tháng/năm
        datasets: [{
            label: 'Doanh Thu',
            data: {!! json_encode($revenues) !!},
            backgroundColor: '#4e73df',
            borderColor: '#4e73df',
            borderWidth: 1,
            barThickness: 15, // Thay đổi độ dày của cột
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Doanh thu (VND)'
                },
                grid: {
                    display: false, // Ẩn các đường lưới dọc
                },
            },
            x: {
                title: {
                    display: true,
                    text: 'Tháng/Năm'
                },
                grid: {
                    display: false, // Ẩn các đường lưới ngang
                },
            }
        }
    }
});

// Biểu đồ đơn hàng
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
const ordersChart = new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($dates) !!}, // Tháng/năm
        datasets: [{
            label: 'Số Đơn Hàng',
            data: {!! json_encode($orderCounts) !!},
            backgroundColor: '#36A2EB',
            borderColor: '#36A2EB',
            borderWidth: 1,
            barThickness: 15, // Thay đổi độ dày của cột
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Số Đơn Hàng'
                },
                grid: {
                    display: false, // Ẩn các đường lưới dọc
                },
            },
            x: {
                title: {
                    display: true,
                    text: 'Tháng/Năm'
                },
                grid: {
                    display: false, // Ẩn các đường lưới ngang
                },
            }
        }
    }
});

// Biểu đồ top sản phẩm bán chạy (donut)
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
const topProductsData = {
    labels: {!! json_encode($topProducts->pluck('product_name')) !!}, // Tên sản phẩm
    datasets: [{
        data: {!! json_encode($topProducts->pluck('total_quantity')) !!}, // Số lượng bán của mỗi sản phẩm
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#FF5733', '#D0F2A0'],
        hoverBackgroundColor: ['#FF4560', '#36A2F5', '#FF9F40', '#FF784C', '#C3F9A9'],
    }]
};

const topProductsChart = new Chart(topProductsCtx, {
    type: 'doughnut',
    data: topProductsData,
    options: {
        responsive: true,
        cutoutPercentage: 60,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw + ' sản phẩm';
                    }
                }
            }
        }
    }
});
</script>
@endsection
