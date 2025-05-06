
document.getElementById('change-address').addEventListener('click', function() {
    console.log('Thay đổi địa chỉ đã được nhấn');
    // Ẩn địa chỉ hiện tại và hiển thị danh sách địa chỉ
    document.getElementById('current-address').style.display = 'none';
    document.getElementById('address-selection').style.display = 'block';
});

document.getElementById('confirm-address').addEventListener('click', function() {
    // Lấy địa chỉ đã chọn
    const selectedAddress = document.querySelector('.select-address:checked');
    if (selectedAddress) {
        const addressId = selectedAddress.getAttribute('data-id');
        const addressText = selectedAddress.parentElement.querySelector('span').textContent;
        
        // Cập nhật địa chỉ nhận hàng
        document.getElementById('address-text').textContent = addressText;
        
        // Ẩn danh sách địa chỉ và hiển thị địa chỉ nhận hàng
        document.getElementById('address-selection').style.display = 'none';
        document.getElementById('current-address').style.display = 'block';

        // Gửi yêu cầu thay đổi địa chỉ mặc định
        fetch(`{{ url('/addresses/select') }}/${addressId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json())
        .then(data => {
            alert('Địa chỉ đã được thay đổi!');
        }).catch(error => {
            console.error('Có lỗi xảy ra:', error);
        });
    } else {
        alert('Vui lòng chọn địa chỉ!');
    }
});