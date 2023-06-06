<template>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12 ">
            <div class="row mt-2">


                <template v-if="isEnabledAdvancedRecordsSearch">

                    <div class="col-md-6">
                        <label class="control-label">Almacén</label>
                        <el-select v-model="form.warehouse_id"
                                    @change="changeWarehouseAdvancedSearch">
                            <el-option v-for="option in warehouses" :key="option.id" :value="option.id"
                                        :label="option.name"></el-option>
                        </el-select>
                    </div>

                    <div class="col-md-6" v-if="load_warehouses">
                        <advanced-items-search
                            @eventSetItemId="setItemId"
                            :warehouse-id="form.warehouse_id"
                            ref="advanced_items_search"
                            >
                        </advanced-items-search>
                    </div>
                </template>
                <template v-else>
                    <div class="col-md-6">
                        <label class="control-label">Almacén</label>
                        <el-select v-model="form.warehouse_id"
                                   @change="changeWarehouse">
                            <el-option v-for="option in warehouses" :key="option.id" :value="option.id"
                                       :label="option.name"></el-option>
                        </el-select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">Producto</label>
                        <el-select v-model="form.item_id"
                                   filterable clearable>
                            <el-option v-for="option in items" :key="option.id" :value="option.id"
                                       :label="option.full_description"></el-option>
                        </el-select>
                    </div>
                </template>

                <div class="col-md-3">
                    <label class="control-label">Fecha inicio</label>
                    <el-date-picker v-model="form.date_start" type="date"
                                    @change="changeDisabledDates"
                                    value-format="yyyy-MM-dd" format="dd/MM/yyyy"
                                    :clearable="true"></el-date-picker>
                </div>
                <div class="col-md-3">
                    <label class="control-label">Fecha término</label>
                    <el-date-picker v-model="form.date_end" type="date"
                                    :picker-options="pickerOptionsDates"
                                    value-format="yyyy-MM-dd" format="dd/MM/yyyy"
                                    :clearable="true"></el-date-picker>
                </div>

                <div class="col-md-6" style="margin-top:29px">
                    <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter"
                               :loading="loading_submit" icon="el-icon-search">Buscar
                    </el-button>
                    <template v-if="records.length>0">
                        <el-button class="submit" type="danger" icon="el-icon-tickets"
                                   @click.prevent="clickDownload('pdf')">Exportar PDF
                        </el-button>
                        <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i
                            class="fa fa-file-excel"></i> Exportar Excel
                        </el-button>
                    </template>
                </div>
            </div>
            <div class="row mt-1 mb-4">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <slot name="heading"></slot>
                    </thead>
                    <tbody>
                    <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                    </tbody>
                </table>
                <div>
                    <el-pagination
                        @current-change="getRecords"
                        layout="total, prev, pager, next"
                        :total="pagination.total"
                        :current-page.sync="pagination.current_page"
                        :page-size="pagination.per_page">
                    </el-pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import moment from 'moment'
import queryString from 'query-string'
import {mapState} from "vuex/dist/vuex.mjs";
import AdvancedItemsSearch from './AdvancedItemsSearch.vue'

export default {
    components: {AdvancedItemsSearch},
    props: {
        resource: String,
    },
    data() {
        return {
            loading_submit: false,
            columns: [],
            records: [],
            headers: headers_token,
            document_types: [],
            pagination: {},
            search: {},
            totals: {},
            establishment: null,
            warehouses: [],
            items: [],
            form: {},
            pickerOptionsDates: {
                disabledDate: (time) => {
                    time = moment(time).format('YYYY-MM-DD')
                    return this.form.date_start > time
                }
            },
            load_warehouses: false,
        }
    },
    created() {
        this.initForm()
        this.events()
    },
    computed: {
        ...mapState([
            'config',
        ]),
        isEnabledAdvancedRecordsSearch: function () {
            return (!_.isEmpty(this.config)) ? this.config.enabled_advanced_records_search : false
        },
    },
    async mounted() {
        if (!this.isEnabledAdvancedRecordsSearch) {
            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.warehouses = response.data.warehouses;
                });
            if (this.warehouses.length > 0) {
                const war = _.find(this.warehouses, {'id': 'all'});
                if (war) {
                    this.form.warehouse_id = 'all'
                } else {
                    this.form.warehouse_id = _.head(this.warehouses).id;
                }
                await this.changeWarehouse();
            }
        }
        else
        {
            this.getFilters()
        }
    },
    methods: {
        changeWarehouseAdvancedSearch()
        {
            this.form.item_id = null
            this.$refs.advanced_items_search.cleanItemId()
            this.$refs.advanced_items_search.initData(this.form.warehouse_id)
        },
        async getFilters()
        {
            await this.$http.get(`/${this.resource}/filter`)
                .then(response => {
                    this.warehouses = response.data.warehouses
                    this.setWarehouseId()
                })
                .then(()=>{
                    this.load_warehouses = true
                })
        },
        setWarehouseId()
        {
            if(this.warehouses.length > 0)
            {
                const all_filter_id = 'all'
                const warehouse = _.find(this.warehouses, {'id': all_filter_id})
                this.form.warehouse_id = !_.isEmpty(warehouse) ? all_filter_id : _.head(this.warehouses).id
            }
        },
        async getCurrentWarehouse()
        {
            await this.$http.get(`/general-get-current-warehouse`)
                .then(response => {
                    this.form.warehouse_id = response.data.id
                })
        },
        initForm() {
            this.form = {
                warehouse_id: 'all',
                item_id: null,
                date_start: null,
                date_end: null,
            }
        },
        setItemId(item_id) {
            this.form.item_id = item_id
        },
        events() {
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            })
        },
        async changeWarehouse() {
            this.items = [];
            this.form.item_id = null;
            await this.$http.get(`/${this.resource}/filter_by_warehouse/${this.form.warehouse_id}`)
                .then(response => {
                    this.items = response.data.items;
                });
        },
        changeDisabledDates() {
            if (this.form.date_end < this.form.date_start) {
                this.form.date_end = this.form.date_start
            }
        },
        clickDownload(type) {
            if (!this.form.item_id) {
                return this.$message.error('El producto es obligatorio')
            }
            let query = queryString.stringify({
                ...this.form
            });
            window.open(`/${this.resource}/${type}/?${query}`, '_blank');
        },
        customIndex(index) {
            return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
        },
        async getRecordsByFilter() {
            if (!this.form.item_id) {
                return this.$message.error('El producto es obligatorio')
            }
            this.loading_submit = true
            await this.getRecords();
            this.loading_submit = false
        },
        async getRecords() {
            this.$eventHub.$emit('emitItemID', this.form.item_id)
            this.records = [];
            await this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`).then((response) => {
                this.records = response.data.data
                console.log(response.data.data)
                this.pagination = response.data.meta
                this.pagination.per_page = parseInt(response.data.meta.per_page)
                this.loading_submit = false
            });
        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page,
                limit: this.limit,
                ...this.form
            })
        },
    }
}
</script>
