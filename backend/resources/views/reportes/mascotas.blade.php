<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mascotas y Amos</title>
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

        .mascotas-table {
            margin-top: 30px;
        }

        .mascotas-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .mascotas-table th,
        .mascotas-table td {
            text-align: center;
        }

        .mascotas-table td {
            font-size: 12px;
            color: #555;
        }
    </style>
</head>

<body>
    <h2>Reporte de Mascotas y Amos Asociados</h2>
    <div class="info-veterinario">
        <p><strong>Veterinario</strong> <br> {{ $veterinario->first_name }} {{ $veterinario->last_name }}</p>
    </div>

    <!-- Tabla de Amos -->
    <table>
        <thead>
            <tr>
                <th>Nombre-Apellido</th>
                <th>Email</th>
                <th>Tipo de Identidad</th>
                <th>Número de Identidad</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($amos as $amo)
            <tr>
                <td>{{ $amo->first_name }} {{ $amo->second_name }} {{ $amo->last_name }} {{ $amo->second_last_name }}
                </td>
                <td>{{ $amo->email }}</td>
                <td>{{ $amo->tipo_identidad }}</td>
                <td>{{ $amo->numero_identidad }}</td>
                <td>{{ $amo->telefono }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Mascotas -->
    <div class="mascotas-table">
        <h3 class="mascotas-title">Lista de Mascotas</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre de la Mascota</th>
                    <th>Especie</th>
                    <th>Raza</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Peso</th>
                    <th>Amo de la Mascota</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($amos as $amo)
                @foreach ($amo->mascotas as $mascota)
                <tr>
                    <td>{{ $mascota->nombre }}</td>
                    <td>{{ $mascota->especie }}</td>
                    <td>{{ $mascota->raza }}</td>
                    <td>{{ $mascota->fecha_nacimiento }}</td>
                    <td>{{ $mascota->peso }} Kg</td>
                    <td>{{ $amo->first_name }} {{ $amo->last_name }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
