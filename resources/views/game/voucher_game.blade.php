@extends('layouts.app')

@section('content')
<div class="container text-center my-3">
    <div class="mb-3">
        <a href="{{ route('voucher.spin.form') }}" class="btn btn-success me-2">🎡 Vòng quay Voucher</a>
        <a href="{{ route('game.index') }}" class="btn btn-info">🎮 Game</a>
    </div>

    <h1 class="mb-3 fw-bold">🎁 Trò chơi hứng Voucher</h1>

    <div class="canvas-wrapper position-relative mx-auto shadow rounded" style="width: 600px; height: 350px;">
        <div class="canvas-background"></div>
        <canvas id="gameCanvas" width="600" height="350" style="position:absolute; top:0; left:0; z-index:1;"></canvas>
    </div>

    <h3 class="mt-3 fw-semibold">Điểm: <span id="scoreDisplay" class="text-success">0</span></h3>

    <div class="mt-4 d-flex justify-content-center gap-3">
        <button id="startGameBtn" class="btn btn-primary btn-lg">▶️ Bắt đầu</button>
        <button id="pauseGameBtn" class="btn btn-warning btn-lg" style="display: none;">⏸️ Tạm dừng</button>
        <button id="stopGameBtn" class="btn btn-danger btn-lg" style="display: none;">🛑 Dừng chơi</button>
    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toastMessage" class="toast show" role="alert" style="display: none;">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle"></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastBody"></div>
        </div>
    </div>
</div>

<style>
body {
    background: linear-gradient(135deg, #fceabb, #f8b500);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.canvas-wrapper {
    border: 3px solid #ccc;
    border-radius: 16px;
    overflow: hidden;
    background: #f8f9fa;
}

.canvas-background {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: url('{{ asset('images/nen.jpg') }}');
    background-size: cover;
    background-position: center;
    opacity: 0.2;
    z-index: 0;
}

#toastMessage {
    background-color: #28a745;
    color: white;
}
</style>

<script>
let canvas = document.getElementById("gameCanvas");
let ctx = canvas.getContext("2d");

let player = {
    x: 280,
    y: 300,
    width: 40,
    height: 40
};
let objects = [];
let floatingTexts = [];
let score = 0;
let isDoublePoints = false;
let doublePointsTimer = null;

let images = {
    player: new Image(),
    voucher: new Image(),
    bomb: new Image(),
    double: new Image()
};
images.player.src = "{{ asset('images/cho.jpg') }}";
images.voucher.src = "{{ asset('images/vp.jpg') }}";
images.bomb.src = "{{ asset('images/bom.jpg') }}";
images.double.src = "{{ asset('images/x2.png') }}";

let running = false;
let paused = false;
let spawnInterval = 1500; // Thời gian giữa các lần rơi vật phẩm
let gameTime = 0; // Thời gian chơi tính bằng giây
let spawnTimer = null;

let baseSpeed = 2; // Tốc độ rơi ban đầu
const maxObjects = 12; // Giới hạn số lượng vật phẩm có thể rơi trong một lúc

function drawPlayer() {
    if (images.player.complete) {
        ctx.drawImage(images.player, player.x, player.y, player.width, player.height);
    }
}

function drawObjects() {
    objects.forEach((obj, index) => {
        if (obj.image.complete) {
            ctx.drawImage(obj.image, obj.x, obj.y, obj.width, obj.height);
        }
        obj.y += obj.speed;

        if (obj.y > canvas.height) objects.splice(index, 1);

        if (obj.y + obj.height >= player.y && obj.x >= player.x && obj.x <= player.x + player.width) {
            let text = "";
            if (obj.type === "voucher") {
                let points = isDoublePoints ? 20 : 10;
                score += points;
                text = `+${points}`;
            } else if (obj.type === "bomb") {
                score -= 15;
                if (score < 0) score = 0;
                text = "-15";
            } else if (obj.type === "double") {
                isDoublePoints = true;
                clearTimeout(doublePointsTimer);
                doublePointsTimer = setTimeout(() => isDoublePoints = false, 5000);
                text = "x2!";
            }

            floatingTexts.push({
                text,
                x: obj.x,
                y: obj.y,
                opacity: 1
            });
            objects.splice(index, 1);
            updateScore();
            checkMilestone();
        }
    });
}

function drawFloatingTexts() {
    floatingTexts.forEach((t, index) => {
        ctx.fillStyle = `rgba(255, 255, 255, ${t.opacity})`;
        ctx.font = "18px Arial";
        ctx.fillText(t.text, t.x, t.y);
        t.y -= 1;
        t.opacity -= 0.02;
        if (t.opacity <= 0) floatingTexts.splice(index, 1);
    });
}

function updateScore() {
    document.getElementById("scoreDisplay").innerText = score;
}

function spawnObject() {
    if (objects.length >= maxObjects) return; // Không rơi thêm nếu đã quá nhiều

    let numObjects = Math.min(Math.floor(gameTime / 10) + 1, 5); // Tối đa 5 vật phẩm/lần rơi

    for (let i = 0; i < numObjects; i++) {
        let x = Math.random() * (canvas.width - 30);
        let rand = Math.random();
        let type = rand < 0.3 ? "voucher" : (rand < 0.85 ? "bomb" : "double");

        objects.push({
            x,
            y: 0,
            width: 30,
            height: 30,
            speed: Math.min(baseSpeed + gameTime * 0.1, 10), // Tăng tốc nhưng tối đa là 10
            type,
            image: images[type]
        });
    }

    // Vẫn giữ thời gian cố định giữa các lần rơi vật phẩm
    spawnTimer = setTimeout(spawnObject, spawnInterval);
}

function updateGame() {
    if (!running || paused) return requestAnimationFrame(updateGame);

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawPlayer();
    drawObjects();
    drawFloatingTexts();
    requestAnimationFrame(updateGame);
}

document.addEventListener("keydown", event => {
    if (!running || paused) return;
    if (event.key === "ArrowLeft" && player.x > 0) player.x -= 20;
    if (event.key === "ArrowRight" && player.x < canvas.width - player.width) player.x += 20;
});

document.getElementById("startGameBtn").addEventListener("click", () => {
    running = true;
    paused = false;
    score = 0;
    gameTime = 0;
    objects = [];
    floatingTexts = [];
    updateScore();

    document.getElementById("pauseGameBtn").style.display = "inline-block";
    document.getElementById("stopGameBtn").style.display = "inline-block";
    document.getElementById("startGameBtn").style.display = "none";

    spawnObject(); // Bắt đầu việc rơi vật phẩm ngay lập tức

    setInterval(() => {
        if (running && !paused) gameTime++;
    }, 1000);

    updateGame();
});

document.getElementById("pauseGameBtn").addEventListener("click", () => {
    paused = !paused;
    document.getElementById("pauseGameBtn").innerText = paused ? "▶️ Tiếp tục" : "⏸️ Tạm dừng";
    if (!paused) updateGame();
});

document.getElementById("stopGameBtn").addEventListener("click", () => {
    running = false;
    paused = false;
    clearTimeout(spawnTimer);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    objects = [];
    floatingTexts = [];
    score = 0;
    updateScore();

    document.getElementById("pauseGameBtn").style.display = "none";
    document.getElementById("stopGameBtn").style.display = "none";
    document.getElementById("startGameBtn").style.display = "inline-block";
});

function showToast(title, message) {
    let toast = document.getElementById("toastMessage");
    document.getElementById("toastTitle").innerText = title;
    document.getElementById("toastBody").innerText = message;
    toast.style.display = "block";
    new bootstrap.Toast(toast).show();

    setTimeout(() => toast.style.display = "none", 3000);
}

function checkMilestone() {
    let vouchers = @json($vouchers);
    let eligibleVoucher = null;

    for (let v of vouchers) {
        if (score >= v.min_score) {
            eligibleVoucher = v;
        }
    }

    if (eligibleVoucher) {
        fetch("{{ route('game.claim') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    voucher_id: eligibleVoucher.id
                })
            })
            .then(res => res.json())
            .then(data => showToast("🎉 Thành công!", data.message))
            .catch(() => showToast("❌ Lỗi", "Có lỗi xảy ra!"));
    }
}
</script>
@endsection
