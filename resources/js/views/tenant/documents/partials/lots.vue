<template>
    <el-dialog :title="titleDialog"
               width="40%"
               :visible="showDialog"
               @open="create"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               append-to-body
               :show-close="false">

        <div class="form-body">
            <div class="col-md-12 text-right">
                <h5>Cant. Pedida: {{ quantity }}</h5>
                <h5 v-bind:class="{ 'text-danger': (toAttend < 0) }">Por Atender: {{ toAttend }}</h5>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xl-6 ">
                    <!--                    <template v-if="search_series_by_barcode">-->
                    <!--                        <el-input placeholder="Buscar serie ..."-->
                    <!--                                  v-model="search.input"-->
                    <!--                                  style="width: 100%;"-->
                    <!--                                  prefix-icon="el-icon-search"-->
                    <!--                                  @change="searchSeriesBarcode">-->
                    <!--                        </el-input>-->
                    <!--                    </template>-->
                    <!--                    <template v-else>-->
                    <el-input placeholder="Buscar serie ..."
                              v-model="search.input"
                              style="width: 100%;"
                              prefix-icon="el-icon-search"
                              @input="getRecords(true)">
                    </el-input>
                    <!--                    </template>-->
                </div>
                <!--                <div class="col-md-6 col-lg-6 col-xl-6 ">-->
                <!--                    <el-switch v-model="search_series_by_barcode"-->
                <!--                               active-text="Buscar por código de barras"-->
                <!--                               @change="changeSearchSeriesBarcode"></el-switch>-->
                <!--                </div>-->
                <div class="col-md-12" v-loading="loading">
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Seleccionar</th>
                                <th>Cod. Lote</th>
                                <th>Serie</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in records" :key="index">
                                <td class="text-center">
                                    {{ customIndex(index) }}
                                </td>
                                <td class="text-center">
                                    <el-checkbox v-model="row.has_sale"
                                                 @change="changeHasSale(row, index)"></el-checkbox>
                                </td>
                                <td>
                                    {{ row.lot_code }}
                                </td>
                                <td>
                                    {{ row.series }}
                                </td>
                                <td>
                                    {{ row.date }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="close">Cerrar</el-button>
            <el-button type="primary"
                       :disabled="toAttend < 0"
                       @click="submit">Guardar
            </el-button>
        </div>
    </el-dialog>
</template>

<script>

export default {
    props: ['showDialog',
        'lotsAll',
        'lots',
        'stock',
        'itemId',
        'documentItemId',
        'quantity',
        'saleNoteItemId',
        'warehouseId'
    ],
    data() {
        return {
            titleDialog: 'Series',
            resource: 'documents',
            search_series_by_barcode: false,
            loading: false,
            errors: {},
            form: {},
            lotsSelected: [],
            records: [],
            pagination: {},
            search: {
                input: null,
                item_id: null
            },
        }
    },
    computed: {
        toAttend() {
            return this.quantity - this.lotsSelected.length
        }
    },
    async mounted() {
        // await this.getRecords()
    },
    // async created() {
    //     this.$eventHub.$on('reloadLotsDataTable', () => {
    //         this.getRecords()
    //     })
    // },
    methods: {
        initForm() {
            this.search = {
                input: null,
                item_id: null,
                document_item_id: this.documentItemId,
                sale_note_item_id: this.saleNoteItemId,
                warehouse_id: this.warehouseId,
            }
        },
        async create() {
            this.initForm()
            this.lotsSelected = this.lots;
            await this.getRecords();
        },
        async getRecords() {
            this.search.item_id = this.itemId
            this.records = [];
            if(this.lotsAll) {
                this.records = this.lotsAll;
            } else {
                this.loading = true
                await this.$http.post(`/${this.resource}/item_lots`, this.search)
                    .then((response) => {
                        this.records = response.data.data
                    })
                    .catch(error => {
                    })
                    .then(() => {
                        this.loading = false
                    })
            }
            await this.checkedLot();
        },
        changeSearchSeriesBarcode() {
            this.cleanInput()
        },
        cleanInput() {
            this.search.input = null
        },
        // async searchSeriesBarcode() {
        //     await this.getRecords(true)
        //     await this.checkedSeries()
        // },
        // async checkedSeries() {
        //     if (this.search_series_by_barcode) {
        //         if (this.records.length == 1) {
        //             let lot = await _.find(this.lots, {id: this.records[0].id})
        //             if (!lot) {
        //                 this.records[0].has_sale = true
        //                 this.addLot(this.records[0])
        //             }
        //         }
        //         this.cleanInput();
        //     }
        // },
        changeHasSale(row, index) {
            let lotIndex = _.findIndex(this.lotsSelected, {id: row.id})
            if (lotIndex > -1) {
                this.lotsSelected.splice(lotIndex, 1)
                row.has_sale = false;
            } else {
                this.lotsSelected.push(row);
                row.has_sale = true;
            }
        },
        customIndex(index) {
            return index + 1;
        },
        checkedLot() {
            _.forEach(this.lotsSelected, row => {
                let lot = _.find(this.records, {id: row.id})
                console.log(row);
                if (lot) {
                    lot.has_sale = true;
                }
            });
        },
        submit() {
            if (this.toAttend < 0) {
                return this.$message.error('La cantidad de series seleccionadas no es la correcta');
            }
            this.$emit('addRowSelectLot', this.lotsSelected);
            this.close();
        },
        close() {
            this.$emit('update:showDialog', false)
        },
    }
}
</script>
