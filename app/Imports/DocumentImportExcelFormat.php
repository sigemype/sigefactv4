<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DocumentImportExcelFormat implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
        $registered = 0;
        unset($rows[0]);

        $records = [];
        foreach ($rows as $row) {
            $create_date = Carbon::instance(Date::excelToDateTimeObject(trim($row[2])));
            $date_of_issue = Carbon::parse($create_date)->format('Y-m-d');

            $records[] = [
                'folio' => $row[0],
                'purchase_order' => $row[1],
                'date_of_issue' => $date_of_issue,
                'customer_number' => trim($row[3]),
                'customer_name' => trim($row[4]),
                'customer_address' => trim($row[5]),
                'customer_email' => trim($row[6]),
                'plate_number' => trim($row[7]),
                'currency_type_id' => (func_str_to_upper_utf8($row[8]) === 'S') ? 'PEN' : null,
                'item_name' => trim($row[9]),
                'unit_type_id' => trim($row[10]),
                'quantity' => trim($row[11]),
                'unit_price' => trim($row[12]),
                'subtotal' => trim($row[13]),
                'total_igv' => trim($row[14]),
            ];
        }

        $data = [];
        $records = collect($records)->groupBy('folio');
        $total_records = count($records);
        foreach ($records as $index => $group) {
            $operation_type_id = '0101';
            $document_type_id = request()->input('document_type_id');
            $series = request()->input('series');
            $number = '#';
            $date_of_issue = $group[0]['date_of_issue'];
            $currency_type_id = $group[0]['currency_type_id'];
            $purchase_order = $group[0]['purchase_order'];
            $folio = $group[0]['folio'];

            //Customer
            $customer_number = $group[0]['customer_number'];
            if (strlen($customer_number) === 11 || strlen($customer_number) === 8) {
                $customer_identity_document_type_id = (strlen($customer_number) == 11) ? '6' : '1';
            } else {
                $customer_identity_document_type_id = '0';
                $customer_number = '00000000';
            }

            $customer_name = $group[0]['customer_name'];
            $customer_address = $group[0]['customer_address'];
            $customer_email = $group[0]['customer_email'];

            $items = [];
            $sum_total_igv = 0;
            $sum_subtotal = 0;
            $sum_total = 0;
            foreach ($group as $row) {
                $unit_type = $row['unit_type_id'];
                if ($unit_type === 'GLNS') {
                    $unit_type_id = 'GLL';
                } elseif ($unit_type === 'GLN') {
                    $unit_type_id = 'GLL';
                } elseif ($unit_type === 'LT') {
                    $unit_type_id = 'LTR';
                } else {
                    $unit_type_id = 'NIU';
                }

                $quantity = floatval($row['quantity']);
                $unit_price = floatval($row['unit_price']);
                $subtotal = floatval($row['subtotal']);
                $total_igv = floatval($row['total_igv']);
                $total = $subtotal + $total_igv;

                $sum_total_igv += $total_igv;
                $sum_subtotal += $subtotal;
                $sum_total += $total;

                $others = [];
                if ($row['plate_number'] !== '' && !is_null($row['plate_number'])) {
                    $others[] = [
                        "codigo" => "5010",
                        "descripcion" => "Número de Placa",
                        "valor" => $row['plate_number'],
                        "fecha_inicio" => "",
                        "fecha_fin" => "",
                        "duracion" => ""
                    ];
                }
                $items[] = [
                    "codigo_interno" => Str::slug($row['item_name']),
                    "descripcion" => $row['item_name'],
                    "codigo_producto_sunat" => '',
                    "unidad_de_medida" => $unit_type_id,
                    "cantidad" => $quantity,
                    "valor_unitario" => $unit_price - ($total_igv / $quantity),
                    "codigo_tipo_precio" => "01",
                    "precio_unitario" => $unit_price,
                    "codigo_tipo_afectacion_igv" => "10",
                    "total_base_igv" => $subtotal,
                    "porcentaje_igv" => "18",
                    "total_igv" => $total_igv,
                    "total_impuestos" => $total_igv,
                    "total_valor_item" => $subtotal,
                    "total_item" => $total,
                    "datos_adicionales" => $others
                ];
            }

            $json = array(
                "serie_documento" => $series,
                "numero_documento" => $number,
                "fecha_de_emision" => $date_of_issue,
                "hora_de_emision" => "11:00:00",
                "codigo_tipo_operacion" => $operation_type_id,
                "codigo_tipo_documento" => $document_type_id,
                "codigo_tipo_moneda" => $currency_type_id,
                "fecha_de_vencimiento" => $date_of_issue,
                "numero_orden_de_compra" => $purchase_order,
                "folio" => $folio,
                "totales" => [
                    "total_exportacion" => 0.00,
                    "total_operaciones_gravadas" => $sum_subtotal,
                    "total_operaciones_inafectas" => 0.00,
                    "total_operaciones_exoneradas" => 0.00,
                    "total_operaciones_gratuitas" => 0.00,
                    "total_igv" => $sum_total_igv,
                    "total_impuestos" => $sum_total_igv,
                    "total_valor" => $sum_subtotal,
                    "total_venta" => $sum_total
                ],
                "datos_del_emisor" => [
                    "codigo_del_domicilio_fiscal" => "0000"
                ],
                "datos_del_cliente_o_receptor" => [
                    "codigo_tipo_documento_identidad" => $customer_identity_document_type_id,
                    "numero_documento" => $customer_number,
                    "apellidos_y_nombres_o_razon_social" => $customer_name,
                    "codigo_pais" => "PE",
                    "ubigeo" => "010101",
                    "direccion" => $customer_address,
                    "correo_electronico" => $customer_email,
                    "telefono" => ""
                ],
                "items" => $items,
                "acciones" => [
                    'enviar_email' => ($customer_email !== '')
                ]
            );

            $url = url('/api/documents');
            $token = auth()->user()->api_token;

            try {
                $client = new \GuzzleHttp\Client();
                $res = $client->post($url, [
                    'verify' => false,
                    'headers' => [
                        'Content-Type' => 'Application/json',
                        'Authorization' => 'Bearer ' . $token
                    ],
                    'json' => $json
                ]);
                $response = json_decode($res->getBody()->getContents(), true);
                if($response['success']) {
                    $registered += 1;
                    $message = 'Registrado';
                } else {
                    $message = $response['message'];
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
            }

            $data[] = [
                'folio' => $index,
                'message' => $message
            ];

        }
        $this->data = compact('total_records', 'registered', 'data');

    }

    public function getData()
    {
        return $this->data;
    }
}
