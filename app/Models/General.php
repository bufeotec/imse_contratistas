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
                // Obtener los datos del cliente y asignar "No disponible" si es nulo
                $cliente_razon_social = $guia->cliente_razon_social ?? 'No disponible';
                $cliente_email = $guia->cliente_email ?? 'No disponible';
                $cliente_telefono = $guia->cliente_telefono ?? 'No disponible';
                $cliente_direccion = $guia->cliente_direccion ?? 'No disponible';

                // Para la fecha de entrega, usamos la fecha de emisión de la guía
                $fecha_entrega = Carbon::parse($guia->guia_fecha_emision)->format('d/m/Y');
            }

            // --- 2) Inicio PDF ---
            $pdf = new Fpdf();
            $pdf->SetMargins(20, 15, 20);
            $pdf->AddPage();

            // Logo (Se puede colocar un logo en la parte superior)
            $logo = public_path('uploads/imse/logo_imse.png');
            if (file_exists($logo)) {
                $pdf->Image($logo, null, null, 50, 35);
            }
            $pdf->Ln(5);

            // Título del PDF con el diseño correcto
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'IMSE CONTRATISTAS GENERALES E.I.R.L.', 0, 1, 'C');
            $pdf->Cell(0, 8, 'ORDEN DE SERVICIO', 0, 1, 'C');
            $pdf->Ln(10);

            // Datos del cliente
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 8, 'Nombre:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(70, 8, utf8_decode($cliente_razon_social), 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 8, 'Email:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(70, 8, utf8_decode($cliente_email), 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 8, utf8_decode('Teléfono:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(70, 8, utf8_decode($cliente_telefono), 0, 1, 'L');

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 8, utf8_decode('Dirección:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(70, 8, utf8_decode($cliente_direccion), 0, 1, 'L');

            $pdf->Ln(10);

            // Datos de la orden de servicio
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 8, utf8_decode('Número de Orden:'), 0, 0, 'L');
            $pdf->Cell(70, 8, 'OS-' . $despacho->id_despacho, 0, 1, 'L');
            $pdf->Cell(50, 8, 'Fecha de Ingreso:', 0, 0, 'L');
            $pdf->Cell(70, 8, $fecha_entrega, 0, 1, 'L');

            $pdf->Cell(50, 8, 'Fecha de Entrega:', 0, 0, 'L');
            $pdf->Cell(70, 8, Carbon::parse($despacho->despacho_fecha)->format('d/m/Y'), 0, 1, 'L');

            $pdf->Ln(10);

            // Descripción del servicio (con tabla)
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->Cell(102, 10, utf8_decode('Descripción'), 1, 0, 'C', 1);
            $pdf->Cell(52, 10, 'Precio', 1, 1, 'C', 1);
            $pdf->SetWidths([20,102,52]);

            // Mostrar detalles de las guías seleccionadas
            $pdf->SetFont('Arial', '', 9);
            $i = 1;
            foreach ($despacho->despacho_detalle as $detalle) {
                $guia = DB::table('guias')->where('id_guia', $detalle->id_guia)->first();
                $pdf->Row([
                    $i,
                    utf8_decode($guia->guia_trabajo_realizar),
                    'S/. 0.00',
                ]);
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

            // Título del PDF con el diseño correcto
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'IMSE CONTRATISTAS GENERALES E.I.R.L.', 0, 1, 'C');
            $pdf->Cell(0, 8, 'ORDEN DE SERVICIO', 0, 1, 'C');
            $pdf->Ln(10);

            // Dibujar rectángulos para datos del cliente y orden de servicio
            $y_rect = $pdf->GetY();
            $pdf->RoundedRect(20, $y_rect, 85, 40, 2, 'D');
            $pdf->RoundedRect(110, $y_rect, 85, 40, 2, 'D');

            // Escribir datos del cliente
            $y_cliente = $y_rect + 5;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, 'Nombre:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_razon_social), 0, 1, 'L');

            $y_cliente += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, 'Email:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_email), 0, 1, 'L');

            $y_cliente += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, utf8_decode('Teléfono:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_telefono), 0, 1, 'L');

            $y_cliente += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(25, $y_cliente);
            $pdf->Cell(20, 8, utf8_decode('Dirección:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, utf8_decode($cliente_direccion), 0, 1, 'L');

            // Escribir datos de la orden de servicio
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
            $pdf->Cell(40, 8, $fecha_entrega, 0, 1, 'L');

            $y_orden += 8;
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(115, $y_orden);
            $pdf->Cell(35, 8, 'Fecha de Entrega:', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 8, Carbon::parse($despacho->despacho_fecha)->format('d/m/Y'), 0, 1, 'L');

            // Mover el cursor debajo de los rectángulos
            $pdf->SetY($y_rect + 40 + 10);

            // Descripción del servicio (con tabla)
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->Cell(107, 10, utf8_decode('Descripción'), 1, 0, 'C', 1);
            $pdf->Cell(47, 10, 'Precio', 1, 1, 'C', 1);
            $pdf->SetWidths([20,107,47]);

            // Mostrar detalles de las guías seleccionadas
            $pdf->SetFont('Arial', '', 9);
            $i = 1;
            foreach ($despacho->despacho_detalle as $detalle) {
                $guia = DB::table('guias')->where('id_guia', $detalle->id_guia)->first();
                $pdf->Row([
                    $i,
                    utf8_decode($guia->guia_trabajo_realizar),
                    'S/. 0.00',
                ]);
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
}
