document.addEventListener('DOMContentLoaded', function () {
    const profileInput = document.getElementById('profile_picture');
    const previewAvatar = document.getElementById('preview-avatar');
    const saveButton = document.getElementById('save-button');

    profileInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            // Hiển thị ảnh mới khi chọn file
            reader.onload = function (e) {
                previewAvatar.src = e.target.result;
            };

            reader.readAsDataURL(file);

            // Hiện nút Lưu
            saveButton.style.display = 'block';
        } else {
            // Nếu không có file, ẩn nút Lưu và đặt lại ảnh
            saveButton.style.display = 'none';
            previewAvatar.src = "{{ Auth::user()->profile_picture ? asset('storage/public/avatars/' . basename(Auth::user()->profile_picture)) : '/images/avt.jpg' }}";
        }
    });
});
// Ẩn thông báo sau 3 giây
setTimeout(function() {
    document.getElementById('flash-message').classList.add('d-none');
}, 3000); // 3000ms = 3 giây
