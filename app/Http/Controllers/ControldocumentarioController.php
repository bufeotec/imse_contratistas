<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\Guia;
use Codedge\Fpdf\Fpdf\Fpdf;

class ControldocumentarioController extends Controller{
    private $logs;
    private $guia;
    public function __construct(){
        $this->logs = new Logs();
        $this->guia = new Guia();
    }

    public function registrardocumentos(){
        try {


            return view('controldocumentario.registrardocumentos');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

    public function sincerar_documentos(){
        try {


            return view('controldocumentario.sincerar_documentos');
        }catch (\Exception $e){
            $this->logs->insertarLog($e);
            return redirect()->route('intranet')->with('error', 'Ocurrió un error al intentar mostrar el contenido.');
        }
    }

    public function generar_pdf(Request $request){
        // Obtener el parámetro desde la ruta o query
        $encoded = $request->route('id_guia') ?? $request->input('id_guia');
        if (!$encoded) {
            abort(400, 'Falta parámetro id_guia');
        }

        // Decodificar base64 de forma segura
        $id_guia = base64_decode($encoded, true);
        if ($id_guia === false) {
            abort(400, 'id_guia inválido');
        }

        // Traer solo los campos necesarios
        $guia = $this->guia->obtener_guia_por_id($id_guia);
        if (!$guia) {
            abort(404, 'Guía no encontrada');
        }

        // Generar PDF con solo serie y correlativo
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, utf8_decode('Guía de Remisión'), 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 8, utf8_decode('Serie:'));
        $pdf->Cell(0, 8, (string)$guia->guia_serie, 0, 1);

        $pdf->Cell(40, 8, utf8_decode('Correlativo:'));
        $pdf->Cell(0, 8, (string)$guia->guia_correlativo, 0, 1);

        $nombre_pdf = 'guia_'.$guia->guia_serie.'-'.$guia->guia_correlativo.'.pdf';
        $pdf->Output('I', utf8_decode($nombre_pdf));
        exit();
    }


}
