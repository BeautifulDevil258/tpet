<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hứng Voucher</title>
    <style>
        body { text-align: center; font-family: Arial, sans-serif; }
        #game-container { width: 400px; height: 500px; border: 1px solid #000; margin: auto; position: relative; overflow: hidden; }
        #basket { width: 60px; height: 40px; background: brown; position: absolute; bottom: 10px; left: 170px; }
        .voucher { width: 40px; height: 40px; background: gold; position: absolute; top: 0; left: 50%; }
    </style>
</head>
<body>

<h1>Chơi Game Hứng Voucher</h1>
<p>Di chuyển giỏ để hứng voucher và nhận mã giảm giá!</p>

<div id="game-container">
    <div id="basket"></div>
</div>

<p id="voucher-code"></p>

<script>
    const basket = document.getElementById('basket');
    const gameContainer = document.getElementById('game-container');
    let basketX = 170;

    document.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft' && basketX > 0) {
            basketX -= 20;
        } else if (event.key === 'ArrowRight' && basketX < 340) {
            basketX += 20;
        }
        basket.style.left = basketX + 'px';
    });

    function dropVoucher() {
        const voucher = document.createElement('div');
        voucher.classList.add('voucher');
        voucher.style.left = Math.random() * 360 + 'px';
        gameContainer.appendChild(voucher);

        let dropInterval = setInterval(() => {
            voucher.style.top = (parseInt(voucher.style.top || '0') + 5) + 'px';

            if (parseInt(voucher.style.top) > 460) {
                if (Math.abs(parseInt(voucher.style.left) - basketX) < 50) {
                    fetch('/api/get-voucher')
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('voucher-code').innerText = 'Bạn nhận được mã: ' + data.code;
                        })
                        .catch(() => alert('Hết voucher!'));
                }
                gameContainer.removeChild(voucher);
                clearInterval(dropInterval);
            }
        }, 50);
    }

    setInterval(dropVoucher, 2000);
</script>

</body>
</html>
