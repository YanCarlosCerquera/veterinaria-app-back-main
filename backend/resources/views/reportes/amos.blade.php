<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Amos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h2,
        p {
            text-align: center;
        }

        h2 {
            color: #4CAF50;
            /* Verde veterinaria */
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            font-size: 14px;
            margin-bottom: 30px;
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
    <h2>Reporte de Amos Asociados</h2>
    <div class="info-veterinario">
        <p><strong>Veterinario</strong> <br> {{ $veterinario->first_name }} {{ $veterinario->last_name }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nombre - Apelido</th>
                <th>Email</th>
                <th>Tipo de Identidad</th>
                <th>Número de Identidad</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($amos as $amo)
            <tr>
                <td>{{ $amo->first_name }} {{ $amo->second_name }} {{ $amo->last_name }}
                    {{ $amo->second_last_name }}</td>
                <td>{{ $amo->email }}</td>
                <td>{{ $amo->tipo_identidad }}</td>
                <td>{{ $amo->numero_identidad }}</td>
                <td>{{ $amo->telefono }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
