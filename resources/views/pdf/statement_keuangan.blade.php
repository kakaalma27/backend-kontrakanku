<!DOCTYPE html>
<html>
<head>
    <title>Statement Keuangan Kontrakan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Statement Keuangan Kontrakan</h1>
    <h2>Bulan: {{ $month }}</h2>
    <h3>Target Keuangan: {{ $target_keuangan }}</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Uang Masuk</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $index => $transaction)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ number_format($transaction->price, 2, ',', '.') }}</td> <!-- Format the price -->
                    <td>{{ $transaction->created_at->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>