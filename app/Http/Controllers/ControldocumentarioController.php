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
        $info_recurso = $this->guia->obtener_informacion_recurso($id_guia);

        // Crear el objeto PDF
        $pdf = new Fpdf();
        $pdf->AddPage();





        // Logo - posición ajustada
        $logoPath = public_path('assets/img/icons/logo_imse.png'); // Corregir nombre si es necesario
        $pdf->Image($logoPath, 15, 15, 40); // x=15, y=15, ancho=35

        // Información de la empresa (lado izquierdo, debajo del logo)
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(70, 20); // Posición debajo del logo

        // Datos de contacto en líneas individuales
        $pdf->Cell(0, 4, utf8_decode('Calle 22 de enero Nro. 110'), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(0, 4, utf8_decode('Espinar - Cusco - Perú'), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(0, 4, utf8_decode('993 928 222'), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(0, 4, utf8_decode('Antonia.Sumiri@gmail.com'), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(0, 4, utf8_decode('info@intexa.com.pe'), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(0, 4, utf8_decode('www.intexa.com.pe'), 0, 1, 'L');

        // Cuadro principal del RUC y correlativo (lado derecho)
        // Fondo verde claro para el cuadro principal
        $pdf->SetFillColor(255, 255, 255); // Verde
        $pdf->SetTextColor(0, 0, 0); // Texto blanco
        $pdf->SetFont('Arial', 'B', 12);

        // Posición del cuadro principal
        $cuadroX = 135;
        $cuadroY = 15;
        $cuadroAncho = 60;
        $cuadroAlto = 29;

        // Dibujar el cuadro principal con borde
        $pdf->SetDrawColor(0, 0, 0); // Borde negro
        $pdf->Rect($cuadroX, $cuadroY, $cuadroAncho, $cuadroAlto, 'DF');

        // RUC en la parte superior del cuadro
        $pdf->SetXY($cuadroX + 2, $cuadroY + 2);
        $pdf->Cell($cuadroAncho - 4, 8, 'R.U.C. 10468640070', 0, 1, 'C');

        // Línea separadora dentro del cuadro
        $pdf->SetDrawColor(0, 0, 0); // Línea blanca
        $pdf->Line($cuadroX + 0, $cuadroY + 11, $cuadroX + $cuadroAncho - 0, $cuadroY + 11);

        // Texto "GUÍA DE REMISIÓN-REMITENTE"
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY($cuadroX + 2, $cuadroY + 13);
        $pdf->Cell($cuadroAncho - 4, 6, utf8_decode('GUÍA DE REMISIÓN'), 0, 1, 'C');

        // Línea separadora dentro del cuadro
        $pdf->SetDrawColor(0, 0, 0); // Línea blanca
        $pdf->Line($cuadroX + 0, $cuadroY + 20, $cuadroX + $cuadroAncho - 0, $cuadroY + 20);

        // Correlativo en rojo
        $pdf->SetTextColor(255, 0, 0); // Rojo
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY($cuadroX + 2, $cuadroY + 21);
        $serie = str_pad($guia->guia_serie, 2);
        $correlativo = str_pad($guia->guia_correlativo, 6);
        $pdf->Cell($cuadroAncho - 4, 8, utf8_decode( $serie . ' - ' . $correlativo), 0, 1, 'C');

        // Restablecer colores para el resto del documento
        $pdf->SetTextColor(0, 0, 0); // Negro
        $pdf->SetFont('Arial', '', 10);







        // Fechas en una sola fila (debajo del cuadro principal)
        $fechaY = $cuadroY + $cuadroAlto + 1;

        // Preparar las fechas
        $fechaEmision = date('d / m / Y', strtotime($guia->guia_fecha_emision));
        $fechaTraslado = date('d / m / Y', strtotime($guia->guia_fecha_emision));

        // Mostrar las fechas en una sola línea
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(15, $fechaY + 6);
        $textoFechas = 'Fecha de Emision: ' . $fechaEmision . '                    Fecha de Traslado: ' . $fechaTraslado;
        $pdf->Cell(0, 8, utf8_decode($textoFechas), 0, 1, 'L');







        // Cuadros de direcciones (debajo de las fechas)
        $direccionY = $fechaY + 15;
        $anchoCuadro = 90;
        $altoCuadro = 30;
        $espacioEntreCuadros = 5;

        // Cuadro DIRECCIÓN DE PARTIDA (izquierda)
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0); // Texto blanco
        $pdf->SetFont('Arial', 'B', 10);

        // Encabezado DIRECCIÓN DE PARTIDA
        $pdf->SetXY(15, $direccionY);
        $pdf->Cell($anchoCuadro, 6, utf8_decode('DIRECCIÓN DE PARTIDA'), 1, 0, 'C', true);

        // Contenido del cuadro de partida
        $pdf->SetFillColor(255, 255, 255); // Fondo blanco para el contenido
        $pdf->SetTextColor(0, 0, 0); // Texto negro
        $pdf->SetFont('Arial', '', 8);

        // Dirección de partida - área de texto más alta para direcciones largas
        $pdf->SetXY(15, $direccionY + 6);
        $direccionPartida = 'AVENIDA DEL EJERCITO 1376';

        // Si el texto es muy largo, usar MultiCell para que se ajuste
        $pdf->Rect(15, $direccionY + 6, $anchoCuadro, 12, 'D'); // Dibujar borde del área de texto
        $pdf->SetXY(16, $direccionY + 8); // Posición con un poco de margen
        $pdf->MultiCell($anchoCuadro - 2, 4, utf8_decode($direccionPartida), 0, 'L');

        // Distrito y Provincia (vacío)
        $pdf->SetXY(15, $direccionY + 18);
        $pdf->Cell(45, 6, utf8_decode('Distrito:'), 1, 0, 'L');
        $pdf->Cell(45, 6, utf8_decode('Provincia:'), 1, 0, 'L');

        // Cuadro DIRECCIÓN DE LLEGADA (derecha)
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0); // Texto blanco
        $pdf->SetFont('Arial', 'B', 10);

        // Encabezado DIRECCIÓN DE LLEGADA
        $cuadroLlegadaX = 15 + $anchoCuadro + $espacioEntreCuadros;
        $pdf->SetXY($cuadroLlegadaX, $direccionY);
        $pdf->Cell($anchoCuadro, 6, utf8_decode('DIRECCIÓN DE LLEGADA'), 1, 0, 'C', true);

        // Contenido del cuadro de llegada
        $pdf->SetFillColor(255, 255, 255); // Fondo blanco para el contenido
        $pdf->SetTextColor(0, 0, 0); // Texto negro
        $pdf->SetFont('Arial', '', 8);

        // Dirección de llegada (cliente_direccion o '-' si es null)
        $direccionCliente = !empty($guia->cliente_direccion) ? $guia->cliente_direccion : '-';

        // Si el texto es muy largo, usar MultiCell para que se ajuste
        $pdf->Rect($cuadroLlegadaX, $direccionY + 6, $anchoCuadro, 12, 'D'); // Dibujar borde del área de texto
        $pdf->SetXY($cuadroLlegadaX + 1, $direccionY + 8); // Posición con un poco de margen
        $pdf->MultiCell($anchoCuadro - 2, 4, utf8_decode($direccionCliente), 0, 'L');

        // Distrito y Provincia (vacío)
        $pdf->SetXY($cuadroLlegadaX, $direccionY + 18);
        $pdf->Cell(45, 6, utf8_decode('Distrito:'), 1, 0, 'L');
        $pdf->Cell(45, 6, utf8_decode('Provincia:'), 1, 0, 'L');






        // Título de la tabla
        $recursoY = $direccionY + 30;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(15, $recursoY);
        $pdf->Cell(0, 6, utf8_decode('RECURSOS'), 0, 1, 'L');

        // Encabezado de la tabla
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(15, $recursoY + 6);
        $pdf->Cell(15, 6, utf8_decode('N°'), 1, 0, 'C');
        $pdf->Cell(70, 6, utf8_decode('RECURSO'), 1, 0, 'C');
        $pdf->Cell(40, 6, utf8_decode('TIPO RECURSO'), 1, 0, 'C');
        $pdf->Cell(30, 6, utf8_decode('MEDIDA'), 1, 0, 'C');
        $pdf->Cell(30, 6, utf8_decode('CANTIDAD'), 1, 1, 'C');

        // Datos de los recursos
        $pdf->SetFont('Arial', '', 8);
        $contador = 1;

        foreach ($info_recurso as $recurso) {
            $pdf->SetX(15);
            $pdf->Cell(15, 6, $contador, 1, 0, 'C');
            $pdf->Cell(70, 6, utf8_decode($recurso->recurso_nombre), 1, 0, 'L');
            $pdf->Cell(40, 6, utf8_decode($recurso->tipo_recurso_concepto), 1, 0, 'L');
            $pdf->Cell(30, 6, utf8_decode($recurso->medida_nombre), 1, 0, 'L');
            $pdf->Cell(30, 6, utf8_decode($recurso->guia_recurso_cantidad), 1, 1, 'C');
            $contador++;
        }





        // Cuadro de Transportista (izquierda)
        $transportistaY = $recursoY + 30;
        $anchoCuadro = 90;

        // Cuadro TRANSPORTISTA
        $pdf->SetFillColor(255, 255, 255); // Fondo blanco
        $pdf->SetTextColor(0, 0, 0); // Texto negro
        $pdf->SetFont('Arial', 'B', 10);

        // Encabezado TRANSPORTISTA
        $pdf->SetXY(15, $transportistaY);
        $pdf->Cell($anchoCuadro, 6, utf8_decode('TRANSPORTISTA'), 1, 0, 'C', true);

        // Contenido del cuadro TRANSPORTISTA
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(15, $transportistaY + 6);
        $transportistaNombre = 'ARTROSCOPICTRAUMA S.A.C.';
        $pdf->MultiCell($anchoCuadro, 6, utf8_decode('NOMBRE: ' . $transportistaNombre), 0, 'L');

        // RUC
        $pdf->SetXY(15, $transportistaY + 12);
        $transportistaRuc = '20124585214';
        $pdf->MultiCell($anchoCuadro, 6, utf8_decode('R.U.C.: ' . $transportistaRuc), 0, 'L');

        // Ajustar la altura del cuadro para que todo el contenido quepa
        $altoCuadro = $pdf->GetY() - $transportistaY + 4; // Ajusta la altura para que encaje el contenido completo

        // Dibuja el borde del cuadro de "Transportista"
        $pdf->Rect(15, $transportistaY, $anchoCuadro, $altoCuadro, 'D');

        // Cuadro MOTIVO DE TRASLADO (derecha)
        $motivoTrasladoY = $transportistaY; // Ajusta esta posición según sea necesario
        $cuadroTrasladoX = 15 + $anchoCuadro + 5; // Posición a la derecha

        // Cuadro MOTIVO DE TRASLADO
        $pdf->SetFillColor(255, 255, 255); // Fondo blanco
        $pdf->SetTextColor(0, 0, 0); // Texto negro
        $pdf->SetFont('Arial', 'B', 10);

        // Encabezado MOTIVO DE TRASLADO
        $pdf->SetXY($cuadroTrasladoX, $motivoTrasladoY);
        $pdf->Cell($anchoCuadro, 6, utf8_decode('MOTIVO DE TRASLADO'), 1, 0, 'C', true);

        // Contenido del cuadro MOTIVO DE TRASLADO
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($cuadroTrasladoX, $motivoTrasladoY + 6);
        $motivoTraslado = utf8_decode($guia->guia_trabajo_realizar); // Obtener el valor del motivo de traslado

        // Si el texto es muy largo, usar MultiCell para ajustarlo
        $pdf->Rect($cuadroTrasladoX, $motivoTrasladoY + 6, $anchoCuadro, 16, 'D'); // Dibujar borde del área de texto
        $pdf->SetXY($cuadroTrasladoX + 1, $motivoTrasladoY + 8); // Posición con margen
        $pdf->MultiCell($anchoCuadro - 2, 4, $motivoTraslado, 0, 'L');









        // Guardar el archivo PDF
        $nombre_pdf = 'guia_'.$guia->guia_serie.'-'.$guia->guia_correlativo.'.pdf';
        $pdf->Output('I', utf8_decode($nombre_pdf));
        exit();
    }



}
