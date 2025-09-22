<?php

namespace App\Models;

use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class General extends Model
{
    use HasFactory;
    private $logs;
    public function __construct()
    {
        parent::__construct();
        $this->logs = new Logs();
    }

    public function consultar_documento($tipo,$num){
        try {
            $tipoDocumento = $tipo;
            $valorNum = $num;

            $respuesta = [
                'mensaje' => '',
                'tipo' => 'info',
                'nombre' => '',
                'direccion' => '',
                'condicion' => ''
            ];

            $name = "";
            $direccion = "";
            $estado = "";
            $mensaje = "";
            $tipo_mensaje = "";
            if ($tipoDocumento == 4) { // RUC
                if (strlen($valorNum) == 11 && is_numeric($valorNum)) {
                    if ($valorNum == "00000000000") {
                        $respuesta['mensaje'] = 'Proveedor Extranjero';
                        $respuesta['tipo'] = 'success';
                        $respuesta['condicion'] = 'HABIDO';
                    } else {
                        $url = "https://api.migo.pe/api/v1/ruc";
                        $token = "A5UB9oaNM7VPs4NgZsPfZXu9SAzxmPI5Yyvzo5B5b5i2NQn5KruzvMXus4Ma";

                        $data = [
                            'token' => $token,
                            'ruc' => $valorNum
                        ];

                        $options = [
                            'http' => [
                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                'method'  => 'POST',
                                'content' => http_build_query($data),
                            ],
                        ];

                        $context = stream_context_create($options);
                        $result = file_get_contents($url, false, $context);

                        if ($result === FALSE) {
                            $respuesta['mensaje'] = 'Error en la consulta.';
                            $respuesta['tipo'] = 'danger';
                        } else {
                            $data = json_decode($result, true);

                            if ($data['success']) {
                                $respuesta['mensaje'] = 'Datos Encontrados';
                                $respuesta['tipo'] = 'success';

                                $name = $data['nombre_o_razon_social'];
                                $direccion  = $data['direccion'];
                                $estado = $data['condicion_de_domicilio'];
//                                $respuesta['condicion'] = $data['condicion_de_domicilio'];

                                if ($data['condicion_de_domicilio'] == "NO HABIDO") {
                                    $respuesta['mensaje'] = 'Este RUC se encuentra como NO HABIDO.';
                                    $respuesta['tipo'] = 'danger';
                                }

                            } else {
                                $respuesta['mensaje'] = $data['message'];
                                $respuesta['tipo'] = 'danger';
                            }
                        }
                    }
                } else {
                    $respuesta['mensaje'] = 'El RUC debe contener 11 dígitos.';
                    $respuesta['tipo'] = 'danger';

                }
            } else {
                if (strlen($valorNum) == 8 && is_numeric($valorNum)) {
                    if ($valorNum == "00000000") {

                        $respuesta['mensaje'] = 'CLIENTE GENERAL';
                        $respuesta['tipo'] = 'success';
                        $respuesta['condicion'] = 'HABIDO';

                    } else {

                        $url = "https://api.migo.pe/api/v1/dni";
                        $token = "A5UB9oaNM7VPs4NgZsPfZXu9SAzxmPI5Yyvzo5B5b5i2NQn5KruzvMXus4Ma";

                        $data = [
                            'token' => $token,
                            'dni' => $valorNum
                        ];

                        $options = [
                            'http' => [
                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                'method'  => 'POST',
                                'content' => http_build_query($data),
                            ],
                        ];

                        $context = stream_context_create($options);
                        $result = file_get_contents($url, false, $context);

                        if ($result === FALSE) {

                            $respuesta['mensaje'] = 'Error en la consulta.';
                            $respuesta['tipo'] = 'error';

                        } else {

                            $data = json_decode($result, true);

                            if ($data['success']) {

                                $respuesta['mensaje'] = 'Datos Encontrados';
                                $respuesta['tipo'] = 'success';

                                $name = $data['nombre'];
                                $estado = 'HABIDO';

//                                $this->num_document = $data['dni'];
//                                $this->status_clienteDNI = false;
                            } else {
                                $respuesta['mensaje'] = $data['message'];
                                $respuesta['tipo'] = 'danger';
                            }
                        }
                    }
                } else {
                    $respuesta['mensaje'] = 'El DNI debe contener 8 dígitos.';
                    $respuesta['tipo'] = 'danger';
                }
            }

            $mensaje = $respuesta['mensaje'];
            $tipo_mensaje = $respuesta['tipo'];

        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            $message = "Ocurrió un error al consulta del numero de documento. Por favor, inténtelo nuevamente.";
        }
        return array("result" => array("name" => $name,'direccion'=>$direccion,'estado'=>$estado,'mensaje'=>$mensaje,'tipo'=>$tipo_mensaje));
    }
    public function save_files($archivo, $rutaImg)
    {
        try {
            if ($archivo) {
                // Obtén el nombre original del archivo
                $originalName = $archivo->getClientOriginalName();
                // Obtén la fecha y hora actuales en el formato deseado
                $timestamp = now()->format('Ymd_His');
                // Combina la fecha y hora con el nombre original del archivo
                $newFileName = $timestamp . '_' . $originalName;
                // Especifica la subcarpeta dentro de 'uploads' (por ejemplo, 'uploads/documentos')
                $subcarpeta = $rutaImg;
                // Verifica si la subcarpeta existe, si no, la crea
                if (!Storage::disk('public_uploads')->exists($subcarpeta)) {
                    Storage::disk('public_uploads')->makeDirectory($subcarpeta);
                }
                // Guarda el archivo directamente en la subcarpeta con el nuevo nombre
                $path = $archivo->storeAs($subcarpeta, $newFileName, 'public_uploads');
                // Guarda la ruta en la base de datos
                return 'uploads/'.$path;

            } else {
                return [];
            }
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
            return [];
        }
    }
    public function obtenerNombreFecha($fecha, $formatoDado, $formatoRetorno, $idioma = 'es', $incluirAnio = true)
    {
        try {
            // Configuración del idioma para Carbon
            Carbon::setLocale($idioma);

            // Convertir la fecha a una instancia de Carbon
            if ($formatoDado === 'DateTime') {
                $fechaCarbon = Carbon::parse($fecha);
            } elseif ($formatoDado === 'Date') {
                $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha);
            } else {
                throw new \InvalidArgumentException('Formato de fecha no soportado');
            }

            // Preparar el formato de salida
            if ($formatoRetorno === 'Date') {
                $formatoSalida = $incluirAnio ? 'd M Y' : 'd M';
            } elseif ($formatoRetorno === 'DateTime') {
                $formatoSalida = 'd M Y, h:i a';
            } else {
                throw new \InvalidArgumentException('Formato de retorno no soportado');
            }

            // Formatear la fecha
            $fechaFormateada = $fechaCarbon->translatedFormat($formatoSalida);

        }catch (\Exception $e) {
            $this->logs->insertarLog($e);
            $fechaFormateada = "";
        }
        return $fechaFormateada;

    }

    public function formatoDecimal($valor){
        try {
            // Verificar si el valor es numérico y mayor que 0
            if (!is_numeric($valor)) {
                \Log::error('Valor no numérico:', ['valor' => $valor]);
                return 0;
            }

            // Convertir el valor a cadena y eliminar cualquier carácter no numérico (excepto el punto decimal)
            $valorStr = preg_replace('/[^0-9.]/', '', (string)$valor);

            // Verificar si el valor es mayor que 0
            if ($valorStr <= 0) {
                \Log::error('Valor no positivo:', ['valor' => $valorStr]);
                return 0;
            }

            // Truncar el número a dos decimales sin redondear usando bcdiv
            $formattedValue = bcdiv($valorStr, '1', 2);

            // Si el valor truncado no tiene decimales, mostramos sin decimales
            if ($formattedValue == floor($formattedValue)) {
                return number_format($formattedValue, 0, '.', ',');
            }

            // Si tiene decimales, mostramos dos decimales
            return number_format($formattedValue, 2, '.', ',');
        } catch (\Exception $e) {
            // En caso de error, insertamos el log y retornamos 0
            $this->logs->insertarLog($e);
            return 0;
        }
    }

    public function imprimir_ticket_os_pdf_($id_despacho, $tipo)
    {
        try {
            $despacho = Despacho::with('despacho_detalle')->findOrFail($id_despacho);

            $guia = DB::table('despachos_detalle')
                ->join('guias', 'despachos_detalle.id_guia', '=', 'guias.id_guia')
                ->join('clientes', 'guias.id_cliente', '=', 'clientes.id_cliente')
                ->where('despachos_detalle.id_despacho', $despacho->id_despacho)
                ->first();

            if ($guia) {
                $cliente_razon_social = $guia->cliente_razon_social ?? 'No disponible';
                $cliente_email = $guia->cliente_email ?? 'No disponible';
                $cliente_telefono = $guia->cliente_telefono ?? 'No disponible';
                $cliente_direccion = $guia->cliente_direccion ?? 'No disponible';

                $fecha_entrega = Carbon::parse($guia->guia_fecha_emision)->format('d/m/Y');
            }

            $pdf = new Fpdf();
            $pdf->SetMargins(20, 15, 20);
            $pdf->AddPage();

            $marco = public_path('uploads/imse/marco_v1.png');
            if (file_exists($marco)) {
                $pdf->Image($marco, 0, 0, 210, 40);
            }

            // Logo
            $logo = public_path('uploads/imse/logo_imse.png');
            if (file_exists($logo)) {
                $pdf->Image($logo, 140, null, 50, 25);
            }
            $pdf->Ln(5);

            // Encabezado
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'IMSE CONTRATISTAS GENERALES E.I.R.L.', 0, 1, 'C');
            $pdf->Cell(0, 8, 'ORDEN DE SERVICIO', 0, 1, 'C');
            $pdf->Ln(10);

            // Marcos de info
            $y_rect = $pdf->GetY();
            $pdf->RoundedRect(20, $y_rect, 85, 40, 2, 'D');
            $pdf->RoundedRect(110, $y_rect, 85, 40, 2, 'D');

            // Cliente
            $y_cliente = $y_rect + 5;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, 'Nombre:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_razon_social ?? 'No disponible'), 0, 1, 'L');

            $y_cliente += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, 'Email:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_email ?? 'No disponible'), 0, 1, 'L');

            $y_cliente += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, utf8_decode('Teléfono:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_telefono ?? 'No disponible'), 0, 1, 'L');

            $y_cliente += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, utf8_decode('Dirección:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_direccion ?? 'No disponible'), 0, 1, 'L');

            // Orden de servicio
            $y_orden = $y_rect + 5;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(115, $y_orden);
            $pdf->Cell(35, 8, utf8_decode('Número de Orden:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, 'OS-' . $despacho->id_despacho, 0, 1, 'L');

            $y_orden += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(115, $y_orden);
            $pdf->Cell(35, 8, 'Fecha de Ingreso:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, $fecha_entrega ?? '-', 0, 1, 'L');

            $y_orden += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(115, $y_orden);
            $pdf->Cell(35, 8, 'Fecha de Entrega:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, Carbon::parse($despacho->despacho_fecha)->format('d/m/Y'), 0, 1, 'L');

            // Cursor debajo de los rectángulos
            $pdf->SetY($y_rect + 40 + 10);

            // =========================================
            // Descripción del servicio (tal cual usas)
            // =========================================
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->Cell(107, 10, utf8_decode('Descripción'), 1, 0, 'C', 1);
            $pdf->Cell(47, 10, 'Precio', 1, 1, 'C', 1);
            $pdf->SetWidths([20,107,47]);

            $pdf->SetFont('Arial', '', 9);
            $i = 1;
            foreach ($despacho->despacho_detalle as $detalle) {
                $guiaRow = DB::table('guias')->where('id_guia', $detalle->id_guia)->first();
                $pdf->Row([
                    $i,
                    utf8_decode($guiaRow->guia_trabajo_realizar ?? ''),
                    'S/. 0.00',
                ]);
                // $i++; // si más adelante quieres incrementar, lo haces; por ahora lo dejo como lo tenías
            }

            $pdf->Ln(6);

            // guías incluidas en este despacho
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(174, 8, utf8_decode('Guías incluidas en este despacho'), 0, 1, 'L',);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(245,245,245);
            $pdf->Cell(20, 8, utf8_decode('N°'), 1, 0, 'C', 1);
            $pdf->Cell(154, 8, utf8_decode('Guía (Serie - Correlativo)'), 1, 1, 'C', 1);

            $pdf->SetFont('Arial', '', 9);
            $n = 1;
            foreach ($despacho->despacho_detalle as $detalle) {
                $g = DB::table('guias')
                    ->select('guia_serie','guia_correlativo')
                    ->where('id_guia', $detalle->id_guia)
                    ->first();

                $codigo = $g ? ($g->guia_serie . '-' . $g->guia_correlativo) : '-';
                $pdf->Cell(20, 8, (string)$n, 1, 0, 'C');
                $pdf->Cell(154, 8, utf8_decode($codigo), 1, 1, 'C');
                $n++;
            }

            $pdf->Ln(6);

            // NUEVO: Materiales por guía
            // (gr.id_guia -> r.recurso_nombre, tr.tipo_recurso_concepto, gr.guia_recurso_cantidad)
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(174, 8, utf8_decode('Materiales por guía'), 0, 1, 'L');

            foreach ($despacho->despacho_detalle as $detalle) {
                $g = DB::table('guias')
                    ->select('guia_serie','guia_correlativo')
                    ->where('id_guia', $detalle->id_guia)
                    ->first();
                $codigo = $g ? ($g->guia_serie . '-' . $g->guia_correlativo) : '-';

                // Subtítulo de la guía
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 7, utf8_decode('Guía: ' . $codigo), 0, 1, 'L');

                // Header materiales
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(245,245,245);
                $pdf->Cell(20, 8, utf8_decode('Cant.'), 1, 0, 'C', 1);
                $pdf->Cell(50, 8, utf8_decode('Tipo'), 1, 0, 'C', 1);
                $pdf->Cell(104, 8, utf8_decode('Recurso'), 1, 1, 'C', 1);
                $pdf->SetWidths([20,50,104]);

                // materiales de la guía
                $materiales = DB::table('guias_recursos as gr')
                    ->join('recursos as r', 'r.id_recurso', '=', 'gr.id_recurso')
                    ->leftJoin('tipos_recursos as tr', 'tr.id_tipo_recurso', '=', 'r.id_tipo_recurso')
                    ->select(
                        'gr.guia_recurso_cantidad',
                        'r.recurso_nombre',
                        'tr.tipo_recurso_concepto'
                    )
                    ->where('gr.id_guia', $detalle->id_guia)
                    ->get();

                $pdf->SetFont('Arial', '', 9);
                if ($materiales->count() === 0) {
                    $pdf->Cell(190, 8, utf8_decode('Sin materiales registrados.'), 1, 1, 'C');
                } else {
                    foreach ($materiales as $m) {
                        $pdf->Row([
                            (string)($m->guia_recurso_cantidad ?? 0),
                            utf8_decode($m->tipo_recurso_concepto ?? '-'),
                            utf8_decode($m->recurso_nombre ?? '-'),
                        ]);
                    }
                }

                $pdf->Ln(4);
            }

            if ($tipo === 1) {
                $pdf->Output();
                exit;
            } else {
                $path = "comprobantes_despachos/despacho_{$despacho->id_despacho}.pdf";
                $pdf->Output('F', public_path($path));
                return $path;
            }
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
        }
    }

    public function imprimir_ticket_os_pdf($id_despacho, $tipo)
    {
        try {
            $despacho = Despacho::with('despacho_detalle')->findOrFail($id_despacho);

            $guia = DB::table('despachos_detalle')
                ->join('guias', 'despachos_detalle.id_guia', '=', 'guias.id_guia')
                ->join('clientes', 'guias.id_cliente', '=', 'clientes.id_cliente')
                ->where('despachos_detalle.id_despacho', $despacho->id_despacho)
                ->first();

            if ($guia) {
                $cliente_razon_social = $guia->cliente_razon_social ?? 'No disponible';
                $cliente_email = $guia->cliente_email ?? 'No disponible';
                $cliente_telefono = $guia->cliente_telefono ?? 'No disponible';
                $cliente_direccion = $guia->cliente_direccion ?? 'No disponible';
                $fecha_entrega = Carbon::parse($guia->guia_fecha_emision)->format('d/m/Y');
            }

            $pdf = new Fpdf();
            $pdf->SetMargins(20, 15, 20);
            $pdf->AddPage();

            $marco = public_path('uploads/imse/marco_v1.png');
            if (file_exists($marco)) {
                $pdf->Image($marco, 0, 0, 210, 40);
            }

            // Logo
            $logo = public_path('uploads/imse/logo_imse.png');
            if (file_exists($logo)) {
                $pdf->Image($logo, 140, null, 50, 25);
            }
            $pdf->Ln(5);

            // Encabezado
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'IMSE CONTRATISTAS GENERALES E.I.R.L.', 0, 1, 'C');
            $pdf->Cell(0, 8, 'ORDEN DE SERVICIO', 0, 1, 'C');
            $pdf->Ln(10);

            // ====== Marcos de info (dinámicos con wrap) ======
            $y_top = $pdf->GetY();

            // helper para escribir "Etiqueta : Valor (multilínea)" y devolver el nuevo Y
            $writeLV = function($pdf, $x, $y, $label, $value, $labelW, $valueW, $lineH = 6) {
                $pdf->SetXY($x, $y);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell($labelW, $lineH, utf8_decode($label), 0, 0, 'L');

                // valor multilínea
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetXY($x + $labelW, $y);
                $pdf->MultiCell($valueW, $lineH, utf8_decode($value ?? 'No disponible'), 0, 'L');

                return $pdf->GetY(); // nuevo Y tras escribir el valor
            };

            // geometría de los cuadros
            $x_left_box = 20;  $w_box = 85;  $x_right_box = 110;
            $inner_pad = 5;   $lineH = 6;

            // coordenadas internas
            $x_left_in = $x_left_box + $inner_pad; // 25
            $x_right_in = $x_right_box + $inner_pad; // 115

            // anchos etiqueta/valor (izquierda)
            $labelW_L = 22;
            $valueW_L = ($x_left_box + $w_box - $inner_pad) - ($x_left_in + $labelW_L); // 100 - (25+22) = 53

            // anchos etiqueta/valor (derecha)
            $labelW_R = 35;
            $valueW_R = ($x_right_box + $w_box - $inner_pad) - ($x_right_in + $labelW_R); // 190 - (115+35) = 40

            // Y inicial dentro de cada cuadro
            $y_left  = $y_top + $inner_pad;
            $y_right = $y_top + $inner_pad;

            // ----- Bloque IZQUIERDO: Cliente -----
            $y_left = $writeLV($pdf, $x_left_in,  $y_left,  'Nombre:', $cliente_razon_social ?? 'No disponible', $labelW_L, $valueW_L, $lineH);
            $y_left = $writeLV($pdf, $x_left_in,  $y_left,  'Email:', $cliente_email ?? 'No disponible', $labelW_L, $valueW_L, $lineH);
            $y_left = $writeLV($pdf, $x_left_in,  $y_left,  'Teléfono:', $cliente_telefono ?? 'No disponible', $labelW_L, $valueW_L, $lineH);
            $y_left = $writeLV($pdf, $x_left_in,  $y_left,  'Dirección:', $cliente_direccion ?? 'No disponible', $labelW_L, $valueW_L, $lineH);

            // ----- Bloque DERECHO: Orden de servicio -----
            $y_right = $writeLV($pdf, $x_right_in, $y_right, 'Número de Orden:', $despacho->despacho_nr_orden, $labelW_R, $valueW_R, $lineH);
            $y_right = $writeLV($pdf, $x_right_in, $y_right, 'Fecha de Ingreso:', $fecha_entrega ?? '-', $labelW_R, $valueW_R, $lineH);
            $y_right = $writeLV($pdf, $x_right_in, $y_right, 'Fecha de Entrega:', Carbon::parse($despacho->despacho_fecha)->format('d/m/Y'), $labelW_R, $valueW_R, $lineH);

            // Altura necesaria según el lado que más creció
            $y_bottom = max($y_left, $y_right);
            $rect_h   = ($y_bottom - $y_top) + $inner_pad; // colchón inferior

            // Dibujar marcos con altura exacta
            $pdf->RoundedRect($x_left_box,  $y_top, $w_box, $rect_h, 2, 'D');
            $pdf->RoundedRect($x_right_box, $y_top, $w_box, $rect_h, 2, 'D');

            // Continuar debajo de ambos recuadros
            $pdf->SetY($y_top + $rect_h + 10);

            // =========================================
            // Descripción del servicio (tal cual usas)
            // =========================================
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->Cell(107, 10, utf8_decode('Descripción'), 1, 0, 'C', 1);
            $pdf->Cell(47, 10, 'Precio', 1, 1, 'C', 1);
            $pdf->SetWidths([20,107,47]);

            $pdf->SetFont('Arial', '', 9);
            $i = 1;
            foreach ($despacho->despacho_detalle as $detalle) {
                $guiaRow = DB::table('guias')->where('id_guia', $detalle->id_guia)->first();
                $pdf->Row([
                    $i,
                    utf8_decode($guiaRow->guia_trabajo_realizar ?? ''),
                    'S/. 0.00',
                ]);
                // $i++;
            }

            $pdf->Ln(6);

            // =========================================
            // Guías incluidas en este despacho
            // =========================================
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(174, 8, utf8_decode('Guías incluidas en este despacho'), 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(245,245,245);
            $pdf->Cell(20, 8, utf8_decode('N°'), 1, 0, 'C', 1);
            $pdf->Cell(154, 8, utf8_decode('Guía (Serie - Correlativo)'), 1, 1, 'C', 1);

            $pdf->SetFont('Arial', '', 9);
            $n = 1;
            foreach ($despacho->despacho_detalle as $detalle) {
                $g = DB::table('guias')
                    ->select('guia_serie','guia_correlativo')
                    ->where('id_guia', $detalle->id_guia)
                    ->first();

                $codigo = $g ? ($g->guia_serie . '-' . $g->guia_correlativo) : '-';
                $pdf->Cell(20, 8, (string)$n, 1, 0, 'C');
                $pdf->Cell(154, 8, utf8_decode($codigo), 1, 1, 'C');
                $n++;
            }

            $pdf->Ln(6);

            // =========================================
            // Materiales por guía
            // =========================================
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(174, 8, utf8_decode('Recursos por guía'), 0, 1, 'L');

            foreach ($despacho->despacho_detalle as $detalle) {
                $g = DB::table('guias')
                    ->select('guia_serie','guia_correlativo')
                    ->where('id_guia', $detalle->id_guia)
                    ->first();
                $codigo = $g ? ($g->guia_serie . '-' . $g->guia_correlativo) : '-';

                // Subtítulo de la guía
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 7, utf8_decode('Guía: ' . $codigo), 0, 1, 'L');

                // Header materiales
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(245,245,245);
                $pdf->Cell(20, 8, utf8_decode('Cant.'), 1, 0, 'C', 1);
                $pdf->Cell(50, 8, utf8_decode('Tipo'), 1, 0, 'C', 1);
                $pdf->Cell(104, 8, utf8_decode('Recurso'), 1, 1, 'C', 1);
                $pdf->SetWidths([20,50,104]);

                // materiales de la guía
                $materiales = DB::table('guias_recursos as gr')
                    ->join('recursos as r', 'r.id_recurso', '=', 'gr.id_recurso')
                    ->leftJoin('tipos_recursos as tr', 'tr.id_tipo_recurso', '=', 'r.id_tipo_recurso')
                    ->select(
                        'gr.guia_recurso_cantidad',
                        'r.recurso_nombre',
                        'tr.tipo_recurso_concepto'
                    )
                    ->where('gr.id_guia', $detalle->id_guia)
                    ->get();

                $pdf->SetFont('Arial', '', 9);
                if ($materiales->count() === 0) {
                    $pdf->Cell(190, 8, utf8_decode('Sin materiales registrados.'), 1, 1, 'C');
                } else {
                    foreach ($materiales as $m) {
                        $pdf->Row([
                            (string)($m->guia_recurso_cantidad ?? 0),
                            utf8_decode($m->tipo_recurso_concepto ?? '-'),
                            utf8_decode($m->recurso_nombre ?? '-'),
                        ]);
                    }
                }

                $pdf->Ln(4);
            }

            // Salida
            if ($tipo === 1) {
                $pdf->Output();
                exit;
            } else {
                $path = "comprobantes_despachos/despacho_{$despacho->id_despacho}.pdf";
                $pdf->Output('F', public_path($path));
                return $path;
            }
        } catch (\Exception $e) {
            $this->logs->insertarLog($e);
        }
    }
}
