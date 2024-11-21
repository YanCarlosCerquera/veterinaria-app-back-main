<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Historias Clínicas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h2 {
            color: #4CAF50;
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }

        .info-veterinario {
            text-align: center;
            font-size: 14px;
            color: #333;
            margin-bottom: 20px;
        }

        .info-veterinario strong {
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #4CAF50;
            color: #fff;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            font-size: 14px;
            font-weight: bold;
        }

        td {
            font-size: 12px;
            color: #555;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h2>Reporte de Historias Clínicas</h2>

    <div class="info-veterinario">
        <p><strong>Veterinario</strong><br>{{ $veterinario->first_name }} {{ $veterinario->last_name }}</p>
    </div>

    <!-- Tabla de Historias Clínicas -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mascota</th>
                <th>Veterinario</th>
                <th>Fecha</th>
                <th>Diagnóstico</th>
                <th>Tratamiento</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($historias as $historia)
            <tr>
                <td>{{ $historia->id }}</td>
                <td>{{ $historia->mascota->nombre }}</td>
                <td>{{ $historia->veterinario->first_name }} {{ $historia->veterinario->last_name }}</td>
                <td>{{ $historia->fecha_consulta }}</td>
                <td>{{ $historia->diagnostico }}</td>
                <td>{{ $historia->tratamiento }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
