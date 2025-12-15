<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Tag - {{ $animal->tag_id }}</title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: sans-serif;
            text-align: center;
        }
        .tag-container {
            border: 2px solid #000;
            width: 300px;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
        }
        .tag-id {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="tag-container">
        <div class="tag-id">{{ $animal->tag_id }}</div>
        <div>
            {!! $qrCode !!}
        </div>
        <p>{{ $animal->breed->name }} - {{ $animal->gender }}</p>
    </div>

    <button class="no-print" onclick="window.print()">Print Tag</button>
</body>
</html>
