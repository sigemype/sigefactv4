<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div v-if="typeUser == 'admin'" class="right-wrapper pull-right">
                <button type="button" class="btn btn-success btn-sm  mt-2 mr-2" @click.prevent="clickImport()"><i class="fa fa-upload"></i> Imp. Ajuste de stock</button>
                <button type="button" class="btn btn-success btn-sm  mt-2 mr-2" @click.prevent="clickReportStock()"><i class="fa fa-file-excel"></i> Reporte Aj. stock</button>
                <button type="button" class="btn btn-success btn-sm  mt-2 mr-2" @click.prevent="clickReport()"><i class="fa fa-file-excel"></i> Reporte</button>

                
                <div class="btn-group flex-wrap">
                    <button
                        aria-expanded="false"
                        class="btn btn-custom btn-sm mt-2 mr-2 dropdown-toggle"
                        data-toggle="dropdown"
                        type="button"
                    >
                        <i class="fa fa-upload"></i> Importar
                        <span class="caret"></span>
                    </button>
                    <div
                        class="dropdown-menu"
                        role="menu"
                        style="position: absolute;will-change: transform;top: 0px;left: 0px;transform: translate3d(0px, 42px, 0px);" x-placement="bottom-start">
                        <a class="dropdown-item text-1" href="#" @click.prevent="clickImportSpecialAttributes('item-lots-group')">Lotes</a>
                        <a class="dropdown-item text-1" href="#" @click.prevent="clickImportSpecialAttributes('item-lots')">Series</a>
                    </div>
                </div>

                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate('input')"><i class="fa fa-plus-circle"></i> Ingreso</button>
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickOutput()"><i class="fa fa-minus-circle"></i> Salida</button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource" ref="datatable">
                    <tr slot="heading">
                        <th>
                            <el-dropdown>
                                <span class="el-dropdown-link">
                                    <el-button>
                                        <i class="fa fa-ellipsis-v"></i>
                                    </el-button>
                                </span>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item @click.native="onChecktAll">Seleccionar todo</el-dropdown-item>
                                    <el-dropdown-item @click.native="onUnCheckAll">Deseleccionar todo</el-dropdown-item>
                                    <el-dropdown-item @click.native="onOpenModalMoveGlobal">Trasladar</el-dropdown-item>
                                    <el-dropdown-item @click.native="onOpenModalStockGlobal">Ajustar stock</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th class="text-right">Stock</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }" :key="index">
                        <td>
                            <el-switch v-model="row.selected" @click="onChangeSelectedStatus(row)"></el-switch>
                        </td>
                        <!-- <td>{{ index }}</td> -->
                        <td>{{ row.item_fulldescription }}</td>
                        <td>{{ row.warehouse_description }}</td>
                        <td class="text-right">{{ row.stock }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickMove(row.id)">Trasladar</button>
                            <button v-if="typeUser == 'admin'" type="button" class="btn waves-effect waves-light btn-xs btn-warning"
                                    @click.prevent="clickRemove(row.id)">Remover</button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-warning"
                                    @click.prevent="clickStock(row.id)">
                                    Ajuste
                                    <el-tooltip class="item"
                                                    content="Ajuste: stock del sistema no cuadre con el stock real"
                                                    effect="dark"
                                                    placement="top">
                                            <i class="fa fa-info-circle"></i>
                                    </el-tooltip>
                            </button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <inventories-form
                            :showDialog.sync="showDialog"
                            :type="typeTransaction"
                                ></inventories-form>

            <inventories-form-output
                            :showDialog.sync="showDialogOutput"
                            ></inventories-form-output>

            <inventories-move :showDialog.sync="showDialogMove"
                              :recordId="recordId"></inventories-move>
            <inventories-remove :showDialog.sync="showDialogRemove"
                                :recordId="recordId"></inventories-remove>
            <MoveGlobal :products="selectedItems" :show.sync="showHideModalMoveGlobal"></MoveGlobal>

            <movement-report
                            :showDialog.sync="showDialogMovementReport"
                                ></movement-report>

            <inventories-stock :showDialog.sync="showDialogStock"
                              :recordId="recordId"></inventories-stock>
                            
            <StockGlobal :products="selectedItems" :show.sync="showHideStockMoveGlobal"></StockGlobal>

            <stock-import :showDialog.sync="showImportDialog"></stock-import>

            <stock-report
                            :showDialog.sync="showDialogStockReport"
                                ></stock-report>


            <import-special-attributes 
                :showDialog.sync="showDialogSpecialAttributes"
                :special-attribute-type="special_attribute_type"
                ></import-special-attributes>

        </div>
    </div>
</template>

<script>

    import InventoriesForm from './form.vue'
    import InventoriesFormOutput from './form_output.vue'

    import InventoriesMove from './move.vue'
    import InventoriesRemove from './remove.vue'
    import DataTable from '@components/DataTable.vue'
    import MoveGlobal from './MoveGlobal.vue';
    import MovementReport from './reports/movement_report.vue';

    import InventoriesStock from './stock.vue'

    import StockGlobal from './StockGlobal.vue';

    import StockImport from './import.vue'

    import StockReport from './reports/stock_report.vue';
    import ImportSpecialAttributes from './partials/import_special_attributes.vue'


    export default {
        props: ['type', 'typeUser'],
        components: {DataTable, InventoriesForm, InventoriesMove, InventoriesRemove, InventoriesFormOutput, MoveGlobal, MovementReport, InventoriesStock, StockGlobal, StockImport, StockReport, ImportSpecialAttributes},
        data() {
            return {
                showHideModalMoveGlobal: false,
                selectedItems: [],
                title: null,
                showDialog: false,
                showDialogMove: false,
                showDialogRemove: false,
                showDialogOutput: false,
                resource: 'inventory',
                recordId: null,
                typeTransaction:null,
                showDialogMovementReport:false,
                showDialogStock: false,
                showHideStockMoveGlobal: false,
                showImportDialog: false,
                showDialogStockReport:false,
                showDialogSpecialAttributes:false,
                special_attribute_type: null,
            }
        },
        created() {
            this.title = 'Inventario'
        },
        methods: {
            clickImportSpecialAttributes(type)
            {
                this.showDialogSpecialAttributes = true
                this.special_attribute_type = type
            },
            clickReport(){
                this.showDialogMovementReport = true
            },
            async onOpenModalMoveGlobal() {
                const itemsSelecteds = await this.$refs.datatable.records.filter(p => p.selected);
                if (itemsSelecteds.length > 0) {
                    this.selectedItems = itemsSelecteds;
                    this.showHideModalMoveGlobal = true;
                } else {
                    this.$message({
                        message: 'Selecciona uno o más productos.',
                        type: 'warning'
                    });
                }
            },
            async onChangeSelectedStatus(row) {
                this.$refs.datatable.records = await this.$refs.datatable.records.map(r => {
                    if (r.id === row.id) {
                        r.selected = row.selected ? false : true;
                    }
                    return r;
                });
                this.$forceUpdate();
            },
            onChecktAll() {
                this.$refs.datatable.records = this.$refs.datatable.records.map(r => {
                    r.selected = true;
                    return r;
                });
            },
            onUnCheckAll() {
                this.$refs.datatable.records = this.$refs.datatable.records.map(r => {
                    r.selected = false;
                    return r;
                });
            },
            clickMove(recordId) {
                this.recordId = recordId
                this.showDialogMove = true
            },
            clickCreate(type) {
                this.recordId = null
                this.typeTransaction = type
                this.showDialog = true
            },
            clickRemove(recordId) {
                this.recordId = recordId
                this.showDialogRemove = true
            },
            clickOutput() {
                this.recordId = null
                this.showDialogOutput = true
            },
            clickStock(recordId) {
                this.recordId = recordId
                this.showDialogStock = true
            },
            async onOpenModalStockGlobal() {
                const itemsSelecteds = await this.$refs.datatable.records.filter(p => p.selected);
                if (itemsSelecteds.length > 0) {
                    this.selectedItems = itemsSelecteds;
                    this.showHideStockMoveGlobal = true;
                } else {
                    this.$message({
                        message: 'Selecciona uno o más productos.',
                        type: 'warning'
                    });
                }
            },
            clickImport(){
                this.showImportDialog = true
            },
            clickReportStock(){
                this.showDialogStockReport = true
            },

        }
    }
</script>
