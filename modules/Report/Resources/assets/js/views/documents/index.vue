<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Consulta de Documentos</h3>
            <div class="data-table-visible-columns" style="top: 10px;">
                <el-dropdown :hide-on-click="false">
                    <el-button type="primary">
                        Mostrar/Ocultar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="(column, index) in columns" :key="index">
                            <el-checkbox @change="getColumnsToShow(1)" v-model="column.visible">{{ column.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :applyCustomer="true" :resource="resource" :visibleColumns="columns" :colspanFootSales="numberColums">
                        <tr slot="heading">
                            <th  class="">#</th>
                            <th v-if="columns.user_seller.visible" class="">Usuario/Vendedor</th>
                            <th class="">Tipo Documento</th>
                            <th class="">Serie</th>
                            <th class="">Numero</th>
                            <!-- <th class="">Comprobante</th> -->
                            <th class="">Fecha emisión</th>
                            <th class="">Fecha vencimiento</th>
                            <th v-if="columns.guides.visible" class="text-right">Guia</th>
                            <th v-if="columns.options.visible" class="text-right">Opciones</th>
                            <th v-if="columns.doc_affect.visible">Doc. Afectado</th>
                            <th v-if="columns.quote.visible">Cotización</th>
                            <th v-if="columns.case.visible">Caso</th>
                            <th v-if="columns.district.visible" class="text-right">Distrito</th>
                            <th v-if="columns.department.visible" class="text-right">Departamento</th>
                            <th v-if="columns.province.visible" class="text-right">Provincia</th>
                            <th v-if="columns.client_direction.visible" class="text-right">Direc. del cliente</th>
                            <th>Cliente</th>
                            <th v-if="columns.ruc.visible" class="text-right">Ruc</th>
                            <th v-if="columns.items.visible">Productos</th>
                            <th>Estado</th>
                            <th v-if="columns.currency_type_id.visible">Moneda</th>
                            <th class="text-center" v-if="columns.web_platforms.visible">Plataforma</th>
                            <th v-if="columns.purchase_order.visible">Orden de compra</th>
                            <th v-if="columns.note_sale.visible" class="text-right">Nota de venta</th>
                            <th v-if="columns.date_note.visible" class="text-right">Fecha N.Venta</th>
                            <th v-if="columns.payment_form.visible" class="text-right">Forma de pago</th>
                            <th v-if="columns.payment_method.visible" class="text-right">Metodo de pago</th>
                            <th v-if="columns.total_charge.visible">Total Cargos</th>
                            <th v-if="columns.total_exonerated.visible">Total Exonerado</th>
                            <th v-if="columns.total_unaffected.visible">Total Inafecto</th>
                            <th v-if="columns.total_free.visible">Total Gratuito</th>
                            <th v-if="columns.total_taxed.visible">Total Gravado</th>

                            <th v-if="columns.total_igv.visible" class="">Total IGV</th>
                            <th class="" v-if="columns.total_isc.visible">Total ISC</th>
                            <th v-if="columns.total.visible" class="">Total</th>
                            
                            <template v-if="configuration.enabled_sales_agents">
                                <th>Agente</th>
                                <th>Datos de referencia</th>
                            </template>
                            <th>Placa</th>
                            
                        </tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td v-if="columns.user_seller.visible" >{{ row.user_name }}</td>
                            <td>{{ row.document_type_description }}</td>
                            <td>{{ row.serie }}</td>
                            <td>{{ row.number }}</td>
                            <td>{{ row.date_of_issue }}</td>
                            <td>{{ row.date_of_due }}</td>
                            <td v-if="columns.guides.visible" class="text-center">
                                <span v-for="(item, i) in row.guides" :key="i">
                                    {{ item.number }} <br>
                                </span>
                            </td>
                            <td v-if="columns.options.visible" class="text-center">
                                <button class="btn waves-effect waves-light btn-xs btn-info m-1__2" type="button"
                                        @click.prevent="clickOptions(row.id)">Opciones
                                </button>
                            </td>
                            <td v-if="columns.doc_affect.visible" >{{ row.affected_document }}</td>
                            <td v-if="columns.quote.visible" >{{ row.quotation_number_full }}</td>
                            <td v-if="columns.case.visible" >{{ row.sale_opportunity_number_full }}</td>

                            <td  v-if="columns.district.visible">
                                {{row.district}}
                            </td>
                            <td  v-if="columns.department.visible">
                                {{row.department}}
                            </td>
                            <td  v-if="columns.province.visible">
                                {{row.province}}
                            </td>
                            <td  v-if="columns.client_direction.visible">
                                {{row.client_direction}}
                            </td>

                            <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>

                            <td  v-if="columns.ruc.visible">
                                {{row.ruc}}
                            </td>

                            <td v-if="columns.items.visible"  class="text-center">
                                <button
                                    class="btn waves-effect waves-light btn-xs btn-primary"
                                    type="button"
                                    @click.prevent="clickViewProducts(row.items)"
                                >
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                            <td>{{ row.state_type_description }}</td>

                            <td v-if="columns.currency_type_id.visible" >{{ row.currency_type_id }}</td>

                            <td  v-if="columns.web_platforms.visible">
                                <template v-for="(platform,i) in row.web_platforms" v-if="row.web_platforms !== undefined">
                                    <label class="d-block"  :key="i">{{platform.name}}</label>
                                </template>
                            </td>
                    
                            <td v-if="columns.purchase_order.visible">{{ row.purchase_order }}</td>

                            <td  v-if="columns.note_sale.visible">
                                {{row.note_sale}}
                            </td>
                            <td  v-if="columns.date_note.visible">
                                {{row.date_note}}
                            </td>
                            <td  v-if="columns.payment_form.visible">
                                {{row.payment_form}}
                            </td>
                            <td  v-if="columns.payment_method.visible">
                                {{row.payment_method}}
                            </td>

                            <td v-if="columns.total_charge.visible">
                                {{
                                    (row.document_type_id == '07') ? ((row.total_charge == 0) ? '0.00' : '-' + row.total_charge) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_charge)
                                }}
                            </td>

                            <td v-if="columns.total_exonerated.visible">{{
                                    (row.document_type_id == '07') ? ((row.total_exonerated == 0) ? '0.00' : '-' + row.total_exonerated) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_exonerated)
                                }}
                            </td>

                            <td v-if="columns.total_unaffected.visible">{{
                                    (row.document_type_id == '07') ? ((row.total_unaffected == 0) ? '0.00' : '-' + row.total_unaffected) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_unaffected)
                                }}
                            </td>
                            <td v-if="columns.total_free.visible">{{
                                    (row.document_type_id == '07') ? ((row.total_free == 0) ? '0.00' : '-' + row.total_free) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_free)
                                }}
                            </td>
                            <td v-if="columns.total_taxed.visible">{{
                                    (row.document_type_id == '07') ? ((row.total_taxed == 0) ? '0.00' : '-' + row.total_taxed) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_taxed)
                                }}
                            </td>
                            <td v-if="columns.total_igv.visible">{{
                                    (row.document_type_id == '07') ? ((row.total_igv == 0) ? '0.00' : '-' + row.total_igv) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_igv)
                                }}
                            </td>
                            <td v-if="columns.total_isc.visible">
                                {{
                                    (row.document_type_id == '07') ? ((row.total_isc == 0) ? '0.00' : '-' + row.total_isc) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total_isc)
                                }}
                            </td>
                            <td v-if="columns.total.visible">{{
                                    (row.document_type_id == '07') ? ((row.total == 0) ? '0.00' : '-' + row.total) : ((row.document_type_id != '07' && (row.state_type_id == '11' || row.state_type_id == '09')) ? '0.00' : row.total)
                                }}
                            </td>


                            <template v-if="configuration.enabled_sales_agents">
                                <td>{{ row.agent_name }}</td>
                                <td>{{ row.reference_data }}</td>
                            </template>

                            <td>{{ row.plate_number }}</td>

                            <!-- <td>{{ (row.document_type_id == '07') ? -row.total_unaffected : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_unaffected) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total_free : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_free) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total_taxed : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_taxed) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total_igv : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total_igv) }}</td>
                            <td>{{ (row.document_type_id == '07') ? -row.total : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total) }}</td>  -->

                        </tr>

                    </data-table>


                </div>
        </div>
        <document-options :showDialog.sync="showDialogOptions"
                          :recordId="recordId"
                          :showClose="true"
                          :configuration="configuration"
        ></document-options>
        <product-sale :records="recordsItems" :showDialog.sync="showDialogProducts">

        </product-sale>
    </div>
</template>

<style>

.el-dropdown-menu {
  overflow-y: auto;
  max-height: 300px;
}

</style>

<script>

    import DataTable from '../../components/DataTableReportsDocuments.vue'
    import DocumentOptions from '../../../../../../../resources/js/views/tenant/documents/partials/options'
    import ProductSale from './partials/product_sale.vue'
    

    export default {
        props: ['configuration'],
        components: {DataTable,DocumentOptions, ProductSale},
        data() {
            return {
                showDialogOptions: false,
                recordId: null,
                resource: 'reports/sales',
                form: {},
                columns: {
                    guides: {
                        title: 'Guias',
                        visible: false
                    },
                    options: {
                        title: 'Opciones',
                        visible: false
                    },
                    web_platforms: {
                        title: 'Plataformas web',
                        visible: false
                    },
                    total_isc: {
                        title: 'Total ISC',
                        visible: false
                    },
                    total_charge: {
                        title: 'Total Cargos',
                        visible: false
                    },
                    district: {
                        title: 'Distrito',
                        visible: false
                    },
                    department: {
                        title: 'Departamento',
                        visible: false
                    },
                    province: {
                        title: 'Provincia',
                        visible: false
                    },
                    client_direction: {
                        title: 'Direccion del cliente',
                        visible: false
                    },
                    ruc: {
                        title: 'Ruc',
                        visible: false
                    },
                    note_sale: {
                        title: 'Nota de venta',
                        visible: false
                    },
                    date_note: {
                        title: 'Fecha de N.Venta',
                        visible: false
                    },
                    payment_form: {
                        title: 'Forma de pago',
                        visible: false
                    },
                    payment_method: {
                        title: 'Metodo de pago',
                        visible: false
                    },
                    purchase_order: {
                        title: 'Orden de compra',
                        visible: true
                    },
                    total_exonerated: {
                        title: 'Total Exonerado',
                        visible: true
                    },
                    total_unaffected: {
                        title: 'Total Inafecto',
                        visible: true
                    },
                    total_free: {
                        title: 'Total Gratuito',
                        visible: true
                    },
                    total_taxed: {
                        title: 'Total Gravado',
                        visible: true
                    },
                    total_igv: {
                        title: 'Total IGV',
                        visible: true
                    },
                    total_isc: {
                        title: 'Total ISC',
                        visible: true
                    },
                    total: {
                        title: 'Total',
                        visible: true
                    },
                    user_seller: {
                        title: 'Usuario/Vendedor',
                        visible: true
                    },
                    doc_affect: {
                        title: 'Doc. Afectado',
                        visible: true
                    },
                    quote: {
                        title: 'Cotizacion',
                        visible: true
                    },
                    case: {
                        title: 'Caso',
                        visible: true
                    },
                    items: {
                        title: 'Productos',
                        visible: true
                    },
                    currency_type_id: {
                        title: 'Moneda',
                        visible: true
                    }

                },
                showDialogProducts: false,
                recordsItems:[],
                numberColums:7,

            }
        },
        created() {
            this.getColumnsToShow();
        },
        methods: {
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },
            clickViewProducts(items = []) {
                this.recordsItems = items;
                this.showDialogProducts = true;
            },
            getColumnsToShow(updated){

            this.$http.post('/validate_columns',{
                columns : this.columns,
                report : 'documents_report_index', // Nombre del reporte.
                updated : (updated !== undefined),
            })
                .then((response)=>{
                    if(updated === undefined){
                        let currentCols = response.data.columns;
                        if(currentCols !== undefined) {
                            this.columns = currentCols
                            this.getNumberColumns()
                        }
                    }
                })
                .catch((error)=>{
                    console.error(error)
                })
            },
            getNumberColumns(){
                let numColumns=0;
                let arrayColumns = Object.values(this.columns)
                //console.log(Array.isArray(this.columns))
                arrayColumns.filter(function(num){
                     switch (num.title) {
                        case 'Total':
                        case 'Total IGV':
                        case 'Total Gratuito':
                        case 'Total Gravado':
                        case 'Total Exonerado':
                        case 'Total Inafecto':
                            return numColumns=this.numberColums;
                            break;
                        default:
                            if (num) {
                                if(num.visible){
                                    numColumns=numColumns+1;
                                    return this.numberColums+numColumns
                                }
                            }
                            break;
                     }
                })
            }

        }
    }
</script>
