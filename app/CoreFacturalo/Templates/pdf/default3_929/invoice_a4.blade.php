<?php
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;use App\Models\Tenant\DocumentItem;use App\Models\Tenant\PaymentMethodType;use Illuminate\Database\Eloquent\Collection;
/**
 * @var Document $document
 * @var Company  $company
 */


$debug = [];
$company = isset($company) ? $company : new Company();
$establishment = $document->establishment;

$establishment_address = ($establishment->address !== '-') ? $establishment->address : null;
$establishment_district = ($establishment->district_id !== '-') ? $establishment->district->description : null;
$establishment_province = ($establishment->province_id !== '-') ? ' - ' . $establishment->province->description : null;
$establishment_department = ($establishment->department_id !== '-') ? ' - ' . $establishment->department->description : null;

$establishment_ubi = $establishment_district . $establishment_province . $establishment_department;
$establishment_trade_address = ($establishment->trade_address !== '-') ? 'D. Fiscal: ' . $establishment->trade_address : null;
$establishment_telephone = ($establishment->telephone !== '-') ? 'Central telefónica: ' . $establishment->telephone : null;
$establishment_aditional_information = ($establishment->aditional_information !== '-') ? $establishment->aditional_information : null;
$establishment_web_address = ($establishment->web_address !== '-') ? 'Web: ' . $establishment->web_address : null;
$establishment_email = ($establishment->email !== '-') ? 'Email: ' . $establishment->email : null;


$customer = $document->customer;
// $debug[] = $customer->address;
$invoice = $document->invoice;
$document_base = ($document->note) ? $document->note : null;

//$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
$document_number = $document->series . '-' . str_pad($document->number, 8, '0', STR_PAD_LEFT);
$accounts = \App\Models\Tenant\BankAccount::all();
if ($document_base) {
    $affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series . '-' . str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series . '-' . str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);
} else {
    $affected_document_number = null;
}
$payments = $document->payments;
$document->load('reference_guides');
$total_payment = $document->payments->sum('payment');
$balance = ($document->total - $total_payment) - $document->payments->sum('change');
//calculate items
$allowed_items = 94 - (\App\Models\Tenant\BankAccount::all()->count()) * 2;
$quantity_items = $document->items()->count();
$cycle_items = $allowed_items - ($quantity_items * 3);
$total_weight = 0;
$border_st = "border:1px solid black;";
$four_borders = "border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000";

$igv = 18;
$anchoTotal = (100 / 745);
$customer_name = $customer->name;
$customer_dni = $customer->number;
$customer_address = "";
$address_clean = true;
$address_length = 50;
$max_address_length = 150;
$blank_line = '';
for ($i = 1; $i < $address_length; $i++) {
    $blank_line .= "&nbsp;";
}
if ($customer->address) {
    $customer_address = $customer->address;
    $customer_address .= ($customer->district_id !== '-') ? ', ' . $customer->district->description : '';
    $customer_address .= ($customer->province_id !== '-') ? ', ' . $customer->province->description : '';
    $customer_address .= ($customer->department_id !== '-') ? ', ' . $customer->department->description : '';
} else {
    $address_clean = false;
    for ($j = 0; $j < 3; $j++) {
        $customer_address .= $blank_line;

    }
}
$customer_address = str_pad($customer_address, $max_address_length, ' ');

$date = $document->date_of_issue->format('d/m/Y');
$currency = $document->currency_type->description;
$gudie = "";
if ($document->guides) {
    foreach ($document->guides as $item) {
        $gudie .= $item->document_type_description . " : " . $item->number . "<br>";
    }

}


if ($document->payment_condition_id === '01') {
    $paymentCondition = PaymentMethodType::where('id', '10')->first();
} else {
    $paymentCondition = PaymentMethodType::where('id', '09')->first();
}


$sale_condition = $paymentCondition->getDescription();

/** @var Collection $items */
$items = $document->items;
$array_items = [];
function setNubmer($number, $decimal = 2, $mil = ',', $dec = '.')
{
    return number_format($number, $decimal, $mil, $dec);
}
foreach ($items as $item_obj) {
    /** @var DocumentItem $item_obj */
    $item = [];
    $it = (array)$item_obj->item;
    $item['code'] = isset($it['internal_id']) ? $it['internal_id'] : null;
    $item['description'] = isset($it['description']) ? $it['description'] : null;
    if ($item_obj->name_product_pdf && !empty($item_obj->name_product_pdf)) {
        $item['description'] = $item_obj->name_product_pdf;
    }
    $item['qty'] = $item_obj->quantity;
    $item['unit'] = isset($it['unit_type_id']) ? $it['unit_type_id'] : null;
    $item['p_unit'] = ($item_obj->unit_value);
    $total_discount_line = 0;
    $item['dscto'] = 0;
    $dsc = [];
    if ($item_obj->discounts) {
        foreach ($item_obj->discounts as $disto) {
            $dsc[] = $disto;
            $total_discount_line += $disto->amount;
            $item['dscto'] += ($disto->factor * 100);
            // <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
        }
    }

    $item_obj->dstto = $total_discount_line;
    $item['dsc'] = $dsc;
    $item['dscto1'] = (empty($total_discount_line)) ? null : $total_discount_line;
    $item['neto'] = $item['p_unit'] - ($item['p_unit'] * ($item['dscto'] / 100));
    $item['total'] = ($item['neto'] * $item['qty']);
    $item['dscto'] = (empty($item['dscto'])) ? null : setNubmer($item['dscto'], 2) . "%";
    $item['dscto1'] = (empty($total_discount_line)) ? null : setNubmer($total_discount_line, 2);

    $item['qty'] = setNubmer($item['qty'], 1);
    $item['p_unit'] = setNubmer($item['p_unit']);
    $item['neto'] = setNubmer($item['neto']);
    $item['total'] = setNubmer($item['total']);
    $array_items[] = $item;

}

$total_word = '';
$extra_info = '';
$currency_symbol = $document->currency_type->symbol;

$total_gravado = $currency_symbol . " " . setNubmer($document->total_taxed);
$total_inefacta = $currency_symbol . " " . setNubmer($document->total_unaffected);
$total_exonerada = $currency_symbol . " " . setNubmer($document->total_exonerated);
$total_gratuita = $currency_symbol . " " . setNubmer($document->total_free);
$total_descuento = $currency_symbol . " " . setNubmer($document->total_discount);
$total_igv = $currency_symbol . " " . setNubmer($document->total_igv);
$total_importe = $currency_symbol . " " . setNubmer($document->total);

$total_items = count($array_items);
$cantidad_linea = 25;

foreach (array_reverse((array)$document->legends) as $row) {
    $total_word .= ($row->code == "1000") ? $row->value . " " . $document->currency_type->description : $row->code . " " . $row->value;
    $total_word .= "<br>";
}

foreach ($document->additional_information as $information) {
    if ($information) {
        $extra_info .= $information . "<br>";
    }
}

$company_name = $company->name;
$company_number = $company->number;
$company_logo = null;
if ($company->logo) {
    $company_logo = $company->logo;
}



$array_chunk = array_chunk($array_items, $cantidad_linea);
$total_array_chunk = count($array_chunk);

?>
@for($items_in_array = 0; $items_in_array < $total_array_chunk; $items_in_array++)
    @php
        $array_to_work = $array_chunk[$items_in_array] ?? null
    @endphp

    <html>
    <head>
        {{--<title>{{ $document_number }}</title>--}}
        {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
    </head>
    <body>


    @if($document->state_type->id == '11')
        <div class="company_logo_box"
             style="position: absolute; text-align: center; top:30%;">
            <img
                src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")) }};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))) }}"
                alt="anulado"
                class=""
                style="opacity: 0.6; {{ $border_st }}">
        </div>
    @endif
    <table width="100%">
        <colgroup width="60%"></colgroup>
        <colgroup width="30%"></colgroup>
        <tr>
            <td
                align="center">
                <div class="company_logo_box">
                    <img
                        @if($company_logo!=null)
                        src="data:{{mime_content_type(public_path("storage/uploads/logos/".$company_logo)) }};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/".$company_logo))) }}"
                        @endif
                        alt="{{ $company_name }}"
                        class="company_logo"
                        style="max-height: 50px; width: 400px;">
                </div>

            </td>
            <td style="{!! $four_borders !!}"
                rowspan=2
                width="30%"
                align="center"
            >
                <h3 class="font-bold">R.U.C. N° {{  $company_number }}</h3>
                <h3 class="text-center font-bold">{{ $document->document_type->description }}</h3>
                <br>
                <h3 class="text-center font-bold">{{ $document_number }}</h3>

            </td>
        </tr>
        <tr>
            <td align="center">
                <small>
                    @if(!empty($company_name)){{ $company_name }}<br>@endif
                    @if(!empty($establishment_trade_address)){{ $establishment_trade_address }}<br>@endif
                    @if(!empty($establishment_address)){{ $establishment_address }}<br>@endif
                    @if(!empty($establishment_ubi)){{ $establishment_ubi }}<br>@endif
                    @if(!empty($establishment_aditional_information)){{ $establishment_aditional_information }}
                    <br>@endif
                    @if(!empty($establishment_telephone)){{ $establishment_telephone }}<br>@endif
                    @if(!empty($establishment_email)){{ $establishment_email }}<br>@endif
                    @if(!empty($establishment_web_address)){{ $establishment_web_address }}<br>@endif

                <!--            <h5>RUC {{ $company_number }}</h5>-->
                </small>
            </td>
        </tr>

    </table>
    <!-- Aqui Tabla de factura -->
    <!-- Datos de cliente y documento -->
    <table cellspacing="0"
           border="0"
           width="100%">
        <tr>
            <td colspan="39"
                height="17"
                align="left"><br></td>
        </tr>
        <colgroup span="36"
                  width="19"></colgroup>
        <tr>
            <td colspan=4
                height="17"
                align="left"
                valign=middle
                sdnum="8202;0;@"><b>Cliente</b></td>
            <td align="center">:</td>
            <td colspan=16
                align="left"
                valign=middle>{{ $customer_name }}</td>
            <td rowspan=4
                align="center"
                valign=middle><br></td>
            <td colspan=7
                align="left"
                valign=middle><b>F. Emisi&oacute;n</b></td>
            <td align="center">:</td>
            <td colspan=9
                align="left"
                valign=middle>{{ $date }}</td>
        </tr>
        <tr>
            <td colspan=4
                height="17"
                align="left"
                valign=middle
                sdnum="8202;0;@"><b>Doc. ID</b></td>
            <td align="center">:</td>
            <td colspan=16
                align="left"
                valign=middle>{{ $customer_dni }}</td>
            <td colspan=7
                align="left"
                valign=middle><b>Moneda</b></td>
            <td align="center">:</td>
            <td colspan=9
                align="left"
                valign=middle>{{ $currency }}</td>
        </tr>
        <tr>
            <td colspan=4
                height="17"
                align="left"
                valign=middle
                sdnum="8202;0;@"><b>Direcci&oacute;n</b></td>
            <td align="center">:</td>
            <td colspan=16
                rowspan=2
                align="left"
                valign=top>@if($address_clean == true)

                    {{ $customer_address }}<br>
                @else
                    {!! $customer_address !!}
                @endif</td>
            <td colspan=7
                align="left"
                valign=middle><b>Gu&iacute;a de remisi&oacute;n</b></td>
            <td align="center">:</td>
            <td colspan=9
                align="left"
                valign=middle>{{ $gudie }}</td>
        </tr>
        <tr>
            <td colspan=5
                rowspan=2
                height="35"
                align="center"
                valign=middle><br></td>
            <td colspan=7
                align="left"
                valign=middle><b>Condici&oacute;n de venta</b></td>
            <td align="center">:</td>
            <td colspan=9
                align="left"
                valign=middle>{{ $sale_condition }}</td>
        </tr>
        <tr>
            <td colspan=34
                align="center"
                valign=middle><b><br></b></td>
        </tr>


        <!-- Cabecera Tabla de items -->
        <?php
        // Col span de los bloques
        $span_cod = 3;
        $span_description = 15;
        $span_qty = 2;
        $span_unit = 3;
        $span_punit = 4;
        $span_dscto = 4;
        $span_neto = 4;
        $span_venta = 4;
        $span_total = 39 - ($span_cod + $span_description + $span_qty + $span_unit + $span_punit + $span_dscto + $span_neto + $span_venta);
        $err = null;
        if ($span_total != 0) {
            $err = $span_total;
        }
        ?>

        <tr>
            <td style="{{ $four_borders }}"
                colspan={{ $span_cod }}
                    height="17"
                align="center"
                valign=middle><b>Cod.</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_description }}
                    align="center"
                valign=middle><b>Descripci&oacute;n</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_qty }}
                    align="center"
                valign=middle><b>Cant.</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_unit }}
                    align="center"
                valign=middle><b>Unidad</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_punit }}
                    align="center"
                valign=middle><b>P. Unit</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_dscto }}
                    align="center"
                valign=middle><b>Dscto</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_neto }}
                    align="center"
                valign=middle><b>P. Neto</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $span_venta }}
                    align="center"
                valign=middle><b>V. Venta</b></td>
        </tr>
        <!-- Cabecera Tabla de items -->

        <!-- Tabla de items -->
        @for($i = 0; $i < $cantidad_linea; $i++)
            <?php
            $border = '';
            if ($i == 0) {
                $border = "border-top: 1px solid #000000;";
            } elseif ($i == ($cantidad_linea - 1)) {
                $border = "border-bottom: 1px solid #000000;";
            }
            $item = [
                'code' => null,
                'description' => null,
                'qty' => null,
                'unit' => null,
                'p_unit' => null,
                'dscto' => null,
                'neto' => null,
                'total' => null,
            ];
            $item_blank_line = true;

            if (isset($array_to_work[$i]) && $array_to_work[$i] != null) {
                $item = $array_to_work[$i];
                $item_blank_line = false;
                $item['description'] = substr($item['description'], 0, 50);
            }

            $border_common = $border . 'border-left: 1px solid #000000; border-right: 1px solid #000000';
            ?>
            <tr>
                <td style="{{ $border_common }}"
                    colspan={{ $span_cod }}
                        height="17"
                    align="center"
                    valign=top>{{ $item['code'] }}</td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_description }}

                        align="left"
                    valign=top>@if($item_blank_line==true)
                        {!! $item['description'] !!}
                    @else
                        {{ $item['description'] }}
                    @endif</td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_qty }}

                        align="center"
                    valign=top>{{ $item['qty'] }}</td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_unit }}
                        align="center"
                    valign=top>{{ $item['unit'] }}</td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_punit }}
                        align="center"
                    valign=top>{{ $item['p_unit'] }} </td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_dscto }}
                        align="center"
                    valign=top>{{ $item['dscto'] }}  </td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_neto }}
                        align="center"
                    valign=top>{{ $item['neto'] }}   </td>
                <td style="{{ $border_common }}"
                    colspan={{ $span_venta }}
                        align="center"
                    valign=top>{{ $item['total'] }}   </td>
            </tr>
    @endfor

    <!-- Tabla de items -->

        <!-- Cantidad en palabras -->

        <tr>
            <td colspan="39"
                height="17"
                align="left"><br></td>
        </tr>
        <tr>
            <td colspan=39
                height="17"
                align="left"
                valign=top>{!! $total_word !!}</td>
        </tr>
        <!-- Cantidad en palabras -->


        <!-- Seccion inferior -->


        <tr>
            <td colspan="39"
                height="17"
                align="left"><br></td>
        </tr>
        <tr>
            <td style="{{ $four_borders }}"
                colspan=22
                rowspan=6
                height="104"
                align="left"
                valign=top>{{ $extra_info }}</td>
            <td align="left"><br></td>

            <?php
            $sepracion1 = 8;
            ?>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 + 1 }}
                    align="left"
                valign=middle><b>Total Op. Gravada</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 }}
                    align="left"
                valign=middle>{{ $total_gravado }}</td>
        </tr>
        <tr>
            <td align="left"><br></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 + 1 }}
                    align="left"
                valign=middle><b>Total Op. Inefacta</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 }}
                    align="left"
                valign=middle>{{ $total_inefacta }}</td>
        </tr>
        <tr>
            <td align="left"><br></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 + 1 }}
                    align="left"
                valign=middle><b>Total Op. Exonerada</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 }}
                    align="left"
                valign=middle>{{ $total_exonerada }}</td>
        </tr>
        <tr>
            <td align="left"><br></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 + 1 }}
                    align="left"
                valign=middle><b>Total Op. Gratuita</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 }}
                    align="left"
                valign=middle>{{ $total_gratuita }}</td>
        </tr>
        <tr>
            <td align="left"><br></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 + 1 }}
                    align="left"
                valign=middle><b>Total IGV {{ $igv }}%</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 }}
                    align="left"
                valign=middle>{{ $total_igv }}</td>
        </tr>
        <tr>
            <td align="left"><br></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 + 1 }}
                    align="left"
                valign=middle><b>Importe Total</b></td>
            <td style="{{ $four_borders }}"
                colspan={{ $sepracion1 }}
                    align="left"
                valign=middle>{{ $total_importe }}</td>
        </tr>
        <!-- Seccion inferior -->

    </table>
    <!-- Aqui Tabla de factura -->
    @endfor


{{--    <pre>{{ var_export($debug,true) }}</pre>--}}


    </body>

    </html>
