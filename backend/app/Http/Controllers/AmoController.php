<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Amo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AmoController extends Controller
{
    public function registro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:amos',
            'tipo_identidad' => 'required|string|in:C.C,Cédula de extranjería',
            'numero_identidad' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'genero' => 'required|string|in:Masculino,Femenino',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $veterinario = $request->user();

        $amo = Amo::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'last_name' => $request->last_name,
            'second_last_name' => $request->second_last_name,
            'email' => $request->email,
            'tipo_identidad' => $request->tipo_identidad,
            'numero_identidad' => $request->numero_identidad,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'genero' => $request->genero,
        ]);

        $veterinario->amos()->attach($amo->id);

        $token = $amo->createToken('Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Amo registrado correctamente',
            'data' => $amo,
            'token' => $token,
        ], 201);
    }


    public function index(): JsonResponse
    {

        $veterinario = Auth::user();

        if (!$veterinario) {
            return response()->json(['error' => 'Veterinario no autenticado'], 401);
        }

        $amos = $veterinario->amos()->withTimestamps()->get();

        return response()->json([
            'success' => true,
            'data' => $amos,
        ], 200);
    }


    // Método para obtener un Amo por su ID
    public function show($id)
    {
        $amo = Amo::find($id);

        if (!$amo) {
            return response()->json(['error' => 'Amo no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $amo,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $amo = Amo::find($id);

        if (!$amo) {
            return response()->json(['error' => 'Amo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'second_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'second_last_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:amos,email,' . $amo->id,
            'tipo_identidad' => 'sometimes|required|string|in:C.C,Cédula de extranjería',
            'numero_identidad' => 'sometimes|required|string|max:255',
            'direccion' => 'sometimes|required|string|max:255',
            'telefono' => 'sometimes|required|string|max:255',
            'genero' => 'sometimes|required|string|in:Masculino,Femenino',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('password')) {
            $amo->password = Hash::make($request->password);
        }

        $amo->fill($request->only([
            'first_name',
            'second_name',
            'last_name',
            'second_last_name',
            'email',
            'tipo_identidad',
            'numero_identidad',
            'direccion',
            'telefono',
            'genero'
        ]));
        $amo->save();

        return response()->json([
            'success' => true,
            'message' => 'Amo actualizado correctamente',
            'data' => $amo,
        ], 200);
    }

    public function destroy($id)
    {
        $amo = Amo::find($id);

        if (!$amo) {
            return response()->json(['error' => 'Amo no encontrado'], 404);
        }

        $amo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Amo eliminado correctamente',
        ], 200);
    }

    public function generarPdfAmos()
    {

        $veterinario = Auth::user();

        $amos = $veterinario->amos;

        $pdf = PDF::loadView('reportes.amos', compact('amos', 'veterinario'));

        return $pdf->stream('reporte_amos.pdf');
    }

    public function generarExcelAmos()
    {
        $veterinario = Auth::user();

        $amos = $veterinario->amos;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $headers = [
            'Primer Nombre',
            'Segundo Nombre',
            'Apellido',
            'Segundo Apellido',
            'Email',
            'Tipo de Identidad',
            'Número de Identidad',
            'Dirección',
            'Teléfono',
            'Género'
        ];

        // Agrega los encabezados a la primera fila
        $sheet->fromArray($headers, NULL, 'A1');

        // Estilos de los encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF388E3C'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // Aplicar estilos a la fila de encabezado
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Aplicar bordes a la fila de encabezado
        $sheet->getStyle('A1:J1')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('FF000000');

        // Agrega los datos de los amos
        $row = 2;
        foreach ($amos as $amo) {
            $sheet->fromArray([
                $amo->first_name,
                $amo->second_name,
                $amo->last_name,
                $amo->second_last_name,
                $amo->email,
                $amo->tipo_identidad,
                $amo->numero_identidad,
                $amo->direccion,
                $amo->telefono,
                $amo->genero,
            ], NULL, 'A' . $row++);

            // Aplicar bordes a cada fila de datos
            $sheet->getStyle("A" . ($row - 1) . ":J" . ($row - 1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FF000000');
        }

        // Aplicar bordes a todas las filas de datos
        $sheet->getStyle('A2:J' . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('FF000000');

        // Ajustar ancho de las columnas
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'reporte_amos.xlsx';
        ob_start();
        $writer->save('php://output');
        $excelFile = ob_get_contents();
        ob_end_clean();

        return response()->stream(function () use ($excelFile) {
            echo $excelFile;
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function countAmos()
    {
        $veterinario = Auth::user();
        $count = $veterinario->amos()->count();
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
