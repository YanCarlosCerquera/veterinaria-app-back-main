<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Historia;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class HistoriasController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mascotas_id' => 'required|exists:mascotas,id',
            'veterinarios_id' => 'required|exists:veterinarios,id',
            'fecha_consulta' => 'required|date',
            'diagnostico' => 'required|string',
            'tratamiento' => 'required|string',


        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $historia = Historia::create([
            'mascotas_id' => $request->mascotas_id,
            'veterinarios_id' => $request->veterinarios_id,
            'fecha_consulta' => $request->fecha_consulta,
            'diagnostico' => $request->diagnostico,
            'tratamiento' => $request->tratamiento,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Historia registrada correctamente',
            'data' => $historia,
        ]);
    }
    public function index()
    {
        $historias = Historia::with(['mascota', 'veterinario'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Historias obtenidas correctamente',
            'data' => $historias,
        ], 200);
    }


    public function show($id)
    {
        $historia = Historia::find($id);

        if (!$historia) {

            return response()->json(['error' => 'Historia no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $historia,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'mascotas_id' => 'required|exists:mascotas,id',
            'veterinarios_id' => 'required|exists:veterinarios,id',
            'fecha_consulta' => 'required|date',
            'diagnostico' => 'required|string',
            'tratamiento' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $historia = Historia::find($id);

        if (!$historia) {
            return response()->json(['error' => 'Historia no encontrada'], 404);
        }

        $historia->mascotas_id = $request->mascotas_id;
        $historia->veterinarios_id = $request->veterinarios_id;
        $historia->fecha_consulta = $request->fecha_consulta;
        $historia->diagnostico = $request->diagnostico;
        $historia->tratamiento = $request->tratamiento;

        $historia->save();

        return response()->json([
            'success' => true,
            'message' => 'Historia actualizada correctamente',
            'data' => $historia,
        ], 200);
    }


    public function destroy($id)
    {

        $historia = Historia::find($id);

        if (!$historia) {
            return response()->json(['error' => 'Historia no encontrada'], 404);
        }

        $historia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Historia eliminada correctamente',
        ], 200);
    }


    public function generarPdfHistorias()
    {
        $veterinario = Auth::user();
        $historias = $veterinario->historias;
        $pdf = PDF::loadView('reportes.historias', compact('historias', 'veterinario'));
        return $pdf->stream('reporte_historias.pdf');
    }

    public function generarExcelHistorias()
    {
        $veterinario = Auth::user();
        $historias = $veterinario->historias()->with('mascota')->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Reporte de Historias Clínicas');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('4CAF50');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', 'Veterinario: ' . $veterinario->first_name . ' ' . $veterinario->last_name);
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $header = ["Nombre de la Mascota", "Fecha de Consulta", "Diagnóstico", "Tratamiento"];
        $sheet->fromArray($header, null, 'A3');
        $sheet->getStyle('A3:D3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A3:D3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
        $sheet->getStyle('A3:D3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Agregar los datos de las historias clínicas
        $row = 4;
        foreach ($historias as $historia) {
            $sheet->setCellValue('A' . $row, $historia->mascota->nombre);
            $sheet->setCellValue('B' . $row, $historia->fecha_consulta);
            $sheet->setCellValue('C' . $row, $historia->diagnostico);
            $sheet->setCellValue('D' . $row, $historia->tratamiento);


            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F9F9F9');
            }

            $row++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_historias_clinicas_' . date('Y_m_d') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelFile = ob_get_contents();
        ob_end_clean();

        return response($excelFile, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }


    public function countHistorias()
    {
        $veterinario = Auth::user();
        $count = $veterinario->historias()->count();
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
