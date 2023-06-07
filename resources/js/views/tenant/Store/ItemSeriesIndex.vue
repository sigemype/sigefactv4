<template>
    <el-dialog :title="title"
               width="40%"
               :visible="showDialog"
               @open="handleOpen"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false"
               append-to-body>
        <div class="row" v-if="item" v-loading="loading">
            <div class="col-md-6 text-right">
                <h5>Cant. Pedida: {{ quantity }}</h5>
            </div>
            <div class="col-md-6 text-right">
                <h5 v-bind:class="{ 'text-danger': (toAttend < 0) }">Por Atender: {{ toAttend }}</h5>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-6 ">
                <template v-if="search_series_by_barcode">
                    <el-input placeholder="Buscar serie ..."
                              v-model="form.input"
                              style="width: 100%;"
                              prefix-icon="el-icon-search"
                              @change="searchSeriesBarcode">
                    </el-input>
                </template>
                <template v-else>
                    <el-input placeholder="Buscar serie ..."
                              v-model="form.input"
                              style="width: 100%;"
                              prefix-icon="el-icon-search"
                              @input="getRecords">
                    </el-input>
                </template>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-6 ">
                <el-switch v-model="search_series_by_barcode" active-text="Buscar por código de barras"
                           @change="changeSearchSeriesBarcode"></el-switch>
            </div>
            <div class="col-md-12">
                <div class="table-responsive mt-3">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 60px">#</th>
                            <th class="text-center" style="width: 120px">Seleccionar</th>
                            <th>Serie</th>
                            <th class="text-center" style="width: 100px">Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td class="text-center">
                                <el-checkbox v-model="row.has_sale"
                                             @change="changeHasSale(row, index)"></el-checkbox>
                            </td>
                            <td>{{ row.series }}</td>
                            <td>{{ row.date }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div>
                        <el-pagination
                            @current-change="getRecords"
                            layout="total, prev, pager, next"
                            :total="pagination.total"
                            :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page"
                        >
                        </el-pagination>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <el-button @click.prevent="onSubmit" :disabled="toAttend < 0">Cerrar</el-button>
            </div>
        </div>
    </el-dialog>
</template>

<script>
import queryString from "query-string";

export default {
    name: 'StoreItemSeriesIndex',
    props: ['showDialog', 'item', 'documentId'],
    data() {
        return {
            title: 'Series',
            resource: 'store',
            loading: false,
            errors: {},
            form: {},
            records: [],
            search_series_by_barcode: false,
            lots: [],
            pagination: {},
        }
    },
    computed: {
        quantity() {
            return (this.item) ? parseFloat(this.item.quantity) : 0;
        },
        toAttend() {
            return this.quantity - this.lots.length
        }
    },
    methods: {
        initForm() {
            this.form = {
                input: null,
                item_id: this.item.item_id,
            }
        },
        async handleOpen() {
            await this.initForm();
            this.lots = this.item.item.lots;
            await this.getRecords();
        },
        changeSearchSeriesBarcode() {
            this.cleanInput()
        },
        cleanInput() {
            this.form.input = null
        },
        async searchSeriesBarcode() {
            await this.getRecords()
        },
        changeHasSale(row, index) {
            let lots_current = this.lots;
            this.lots = [];
            _.forEach(this.records, r => {
                if (r.has_sale) {
                    this.lots.push(r)
                }
            });

            this.lots = this.lots.concat(lots_current)
        },
        getRecords() {
            this.loading = true
            return this.$http.post(`/${this.resource}/get_item_series?${this.getQueryParameters()}`, this.form)
                .then((response) => {
                    this.records = response.data.data;
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(
                        response.data.meta.per_page
                    );
                    this.checkedLot()
                })
                .catch(error => {
                    console.log(error);
                })
                .then(() => {
                    this.loading = false
                })
        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page
            });
        },
        checkedLot() {
            if(this.documentId) {
                _.forEach(this.lots, row => {
                    this.records.push(row);
                });
            } else {
                _.forEach(this.records, row => {
                    if (_.find(this.lots, {id: row.id})) {
                        row.has_sale = true;
                    }
                });
                console.log('aqui')
            }

        },
        verifyLot(row) {
            return _.find(this.lots, {id: row.id});
        },
        onSubmit() {
            this.$emit('success', this.lots);
            this.closeDialog();
        },
        closeDialog() {
            this.$emit('update:showDialog', false)
        },
    }
}
</script>
