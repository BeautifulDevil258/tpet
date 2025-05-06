@extends('layouts.app')

@section('content')
<style>
body {
    background: linear-gradient(135deg, #fceabb, #f8b500);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.wheel-container {
    position: relative;
    width: 320px;
    height: 320px;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border-radius: 50%;
}

.wheel {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 10px solid #ffc107;
    position: relative;
    transition: transform 4s ease-out;
    box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.4), 0 0 15px rgba(0, 0, 0, 0.3);
}

.pointer {
    position: absolute;
    top: 5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    border-bottom: 30px solid red;
    z-index: 10;
}

.slice-label {
    position: absolute;
    width: 50%;
    height: 50%;
    top: 50%;
    left: 50%;
    transform-origin: 0% 0%;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    pointer-events: none;
    font-size: clamp(10px, 2vw, 14px);
    font-weight: bold;
    color: #fff;
    word-wrap: break-word;
    max-width: 80px;
    padding: 5px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
}

.spin-btn {
    margin-top: 40px;
    padding: 12px 40px;
    font-size: 18px;
    font-weight: bold;
    background-color: #e74c3c;
    border: none;
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    transition: background 0.3s ease;
    margin-bottom: 10px;    
}

.spin-btn:hover {
    background-color: #c0392b;
}

h2 {
    font-weight: bold;
    color: #d84315;
    text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.8);
}
</style>

<div class="container text-center">
    <div class="mb-4">
        <a href="{{ route('voucher.spin.form') }}" class="btn btn-success me-2">🎡 Vòng quay Voucher</a>
        <a href="{{ route('game.index') }}" class="btn btn-info">🎮 Game</a>
    </div>
    <h2 class="mb-3">🎁 Vòng quay nhận voucher</h2>

    <div class="wheel-container">
        <div class="pointer"></div>

        @php
        $colors = ['#ffeb3b', '#f44336', '#4caf50', '#2196f3', '#9c27b0', '#ff9800', '#3f51b5', '#e91e63'];
        $count = is_array($spinOptions) ? count($spinOptions) : 0;
        $degPerItem = $count > 0 ? 360 / $count : 0;

        $gradientParts = [];
        foreach ($spinOptions as $index => $item) {
        $start = $index * $degPerItem;
        $end = $start + $degPerItem;
        $color = $colors[$index % count($colors)];
        $gradientParts[] = "$color {$start}deg {$end}deg";
        }
        $conicGradient = implode(', ', $gradientParts);
        @endphp

        <div class="wheel" id="wheel" style="background: conic-gradient({{ $conicGradient }});">
            @foreach ($spinOptions as $index => $item)
            @php
            $rotation = $index * $degPerItem + $degPerItem / 2;
            @endphp
            <div class="slice-label" style="transform: rotate({{ $rotation }}deg) translate(-50%, -130%);">
                {{ $item['label'] }}
            </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('voucher.spin') }}" method="POST" id="spinForm">
        @csrf
        <input type="hidden" name="result_label" id="result_label">
        <input type="hidden" name="result_type" id="result_type">
        <input type="hidden" name="result_id" id="result_id">
        <button type="button" class="spin-btn" onclick="spinWheel()">🎯 Quay ngay</button>
    </form>
</div>

<script>
const spinOptions = @json($spinOptions);
let spinning = false;

function spinWheel() {
    if (spinning) return;
    spinning = true;

    const wheel = document.getElementById('wheel');
    const selectedIndex = Math.floor(Math.random() * spinOptions.length);
    const selected = spinOptions[selectedIndex];

    const degPerItem = 360 / spinOptions.length;
    const fullSpins = 6;
    const rotateDeg = 360 * fullSpins + (360 - (selectedIndex * degPerItem + degPerItem / 2));

    wheel.style.transform = `rotate(${rotateDeg}deg)`;

    setTimeout(() => {
        document.getElementById('result_label').value = selected.label;
        document.getElementById('result_type').value = selected.type;
        document.getElementById('result_id').value = selected.id ?? '';
        document.getElementById('spinForm').submit();
    }, 4500);
}
</script>
@endsection