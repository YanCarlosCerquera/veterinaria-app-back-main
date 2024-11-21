<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MascotaController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string|max:255',
            'raza' => 'nullable|string|max:255',
            'peso' => 'nullable|numeric|min:0',
            'fecha_nacimiento' => 'required|date',
            'amo_id' => 'required|exists:amos,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $mascota = Mascota::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mascota registrada correctamente',
            'data' => $mascota,
        ]);
    }

    // Método para obtener todas las mascotas
    public function index()
    {
        $veterinario = Auth::user();

        $amos = $veterinario->amos;

        // Obtener todas las mascotas de los amos
        $mascotas = [];
        foreach ($amos as $amo) {
            $mascotas = array_merge($mascotas, $amo->mascotas->toArray());
        }


        return response()->json([
            'success' => true,
            'data' => $mascotas,
        ]);
    }

    public function show($id)
    {
        $mascota = Mascota::find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mascota,
        ]);
    }
    public function update(Request $request, $id)
    {
        $mascota = Mascota::find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|string|max:255',
            'raza' => 'sometimes|nullable|string|max:255',
            'peso' => 'sometimes|nullable|numeric|min:0',
            'amo_id' => 'sometimes|required|exists:amos,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $mascota->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada correctamente',
            'data' => $mascota,
        ]);
    }


    public function destroy($id)
    {
        $mascota = Mascota::find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }


        $mascota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mascota eliminada correctamente',
        ]);
    }
    public function generarPdfMascotas()
    {
        $veterinario = Auth::user();
        $amos = $veterinario->amos;
        $pdf = PDF::loadView('reportes.mascotas', compact('amos', 'veterinario'));
        return $pdf->stream('reporte_mascotas.pdf');
    }

    public function generarExcelMascotas()
    {
        $veterinario = Auth::user();
        $amos = $veterinario->amos()->with('mascotas')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Reporte de Mascotas y Amos Asociados');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('4CAF50');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');


        $sheet->setCellValue('A2', 'Veterinario: ' . $veterinario->first_name . ' ' . $veterinario->last_name);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');


        $header = ["Nombre - Apellido", "Email", "Tipo de Identidad", "Número de Identidad", "Teléfono", "Mascota", "Especie", "Raza", "Fecha de Nacimiento", "Peso"];
        $sheet->fromArray($header, null, 'A4');


        $sheet->getStyle('A4:J4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:J4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
        $sheet->getStyle('A4:J4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Agrega los datos de los amos y sus mascotas
        $row = 5;
        foreach ($amos as $amo) {
            $sheet->setCellValue('A' . $row, $amo->first_name . ' ' . $amo->second_name . ' ' . $amo->last_name . ' ' . $amo->second_last_name);
            $sheet->setCellValue('B' . $row, $amo->email);
            $sheet->setCellValue('C' . $row, $amo->tipo_identidad);
            $sheet->setCellValue('D' . $row, $amo->numero_identidad);
            $sheet->setCellValue('E' . $row, $amo->telefono);

            // Agregar mascotas en filas individuales
            if ($amo->mascotas->isNotEmpty()) {
                foreach ($amo->mascotas as $mascota) {
                    $sheet->setCellValue('F' . $row, $mascota->nombre);
                    $sheet->setCellValue('G' . $row, $mascota->especie);
                    $sheet->setCellValue('H' . $row, $mascota->raza);
                    $sheet->setCellValue('I' . $row, $mascota->fecha_nacimiento);
                    $sheet->setCellValue('J' . $row, $mascota->peso . ' Kg');

                    // Estilos de la fila
                    $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));

                    // Alternar color de fila
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F9F9F9');
                    }

                    $row++;
                }
            } else {

                $sheet->setCellValue('F' . $row, 'Sin mascotas');
                $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                $row++;
            }
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_mascotas_' . date('Y_m_d') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelFile = ob_get_contents();
        ob_end_clean();

        return response($excelFile, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function countMascotas()
    {
        $veterinario = Auth::user();
        // Contar las mascotas asociadas a los amos del veterinario logueado
        $count = $veterinario->amos()->with('mascotas')->get()->pluck('mascotas')->flatten()->count();
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
