<template>
    <el-dialog :title="titleDialog" :visible="showDialog" :close-on-click-modal="false" @close="close" @open="create" width="80%">
        <form autocomplete="off" @submit.prevent="submit" v-loading="loading">
            <div class="form-body">
                <div class="row">

                    <div class="col-md-4">
                        
                        <div class="form-group">
                            <label class="control-label">Filtrar por</label>
                            <el-select
                                v-model="form.search_column"
                                @change="changeClearInput"
                            >
                                <el-option
                                    v-for="(label, key) in columns"
                                    :key="key"
                                    :value="key"
                                    :label="label"
                                ></el-option>
                            </el-select>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Valor de búsqueda</label>
                            <el-input
                                placeholder="Buscar"
                                v-model="form.search_input"
                                style="width: 100%;"
                                prefix-icon="el-icon-search"
                            >
                            </el-input>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.warehouse_id}">
                            <label class="control-label">Almacén</label>
                            <el-select v-model="form.warehouse_id" filterable clearable>
                                <el-option v-for="option in warehouses" :key="option.id" :value="option.id"
                                           :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.warehouse_id"
                                   v-text="errors.warehouse_id[0]"></small>
                        </div>
                    </div> 
  
                    
                    <div class="col-md-3">
                        <label class="control-label">Fecha inicio</label>
                        <el-date-picker v-model="form.date_start" type="date"
                                        @change="changeDisabledDates"
                                        value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="true"></el-date-picker>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Fecha término</label>
                        <el-date-picker v-model="form.date_end" type="date"
                                        :picker-options="pickerOptionsDates"
                                        value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="true"></el-date-picker>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <el-checkbox class="mt-4" v-model="form.order_by_item">Ordenar por producto</el-checkbox>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <el-checkbox class="mt-4" v-model="form.order_by_timestamps">Ordenar por fecha - hora</el-checkbox>
                        </div>
                    </div>

                    <div class="col-md-12" style="margin-top:29px">
                        <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter" :loading="loading_submit" icon="el-icon-search" >Buscar</el-button>
                        <template v-if="records.length>0">

                            <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownload('pdf')" >Exportar PDF</el-button>

                            <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportar Excel</el-button>

                        </template>
                    </div>

                </div>
                <div class="row mt-3" v-if="records.length > 0">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th >#</th>
                                        <th >Producto</th>
                                        <th class="text-center">Fecha y hora transacción</th>
                                        <th class="text-center">Motivo de traslado</th>
                                        <th class="text-center">Stock sistema</th>
                                        <th class="text-center">Entrada</th>
                                        <th class="text-center">Salida</th>
                                        <th class="text-center">Stock real</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in records" :key="index">
                                        <td> {{ customIndex(index) }} </td>
                                        <td> {{ row.item_description }} </td>
                                        <td class="text-center"> {{ row.date_time }} </td>
                                        <td class="text-center"> {{ row.description }} </td>
                                        <td class="text-center"> {{ row.system_stock }} </td>
                                        <td class="text-center"> {{ row.input }} </td>
                                        <td class="text-center"> {{ row.output }} </td>
                                        <td class="text-center"> {{ row.real_stock }} </td>
                                    </tr>
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

            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cerrar</el-button>
            </div>
        </form>
 
    </el-dialog>

</template>

<script> 

import moment from 'moment'
import queryString from 'query-string'

export default {
    props: ['showDialog'],
    data() {
        return {
            loading: false,
            loading_search: false,
            loading_submit: false,
            titleDialog: null,
            resource: 'reports/inventory-movements',
            errors: {},
            form: {},
            pagination: {},
            items: [],
            warehouses: [],
            records: [],
            inventory_transactions: [],
            pickerOptionsDates: {
                disabledDate: (time) => {
                    time = moment(time).format('YYYY-MM-DD')
                    return this.form.date_start > time
                }
            },
            columns: [],
        }
    },
    created() {
        this.initForm()
        this.initTables()
    },
    methods: {  
        changeClearInput() 
        {
            this.form.search_input = null
        },
        clickDownload(type) {
            let query = queryString.stringify({
                ...this.form
            });
            window.open(`/${this.resource}/stock/format-stock-fit/${type}/?${query}`, '_blank');
        },
        initForm() {

            this.errors = {}

            this.form = {
                id: null,
                warehouse_id: null,
                date_start:null,
                date_end:null,
                order_by_timestamps: false,
                order_by_item: false,
                search_column: 'description',
                search_input: null,
            }

        }, 
        changeDisabledDates() {
            if (this.form.date_end < this.form.date_start) {
                this.form.date_end = this.form.date_start
            }
        },
        async initTables() {

            await this.$http.get(`/${this.resource}/filter-stock-fit`)
                .then(response => {
                    this.warehouses = response.data.warehouses
                    this.columns = response.data.columns
                })

            await this.searchRemoteItems('')
            
        },
        async create() {

            this.loading = true;
            this.titleDialog = 'Reporte ajuste de stock'
            this.initForm();
            this.loading = false;

        },
        async searchRemoteItems(search) {

            this.loading_search = true

            this.items = []

            await this.$http.post(`/inventory/search_items`, {'search': search})
                .then(response => {
                    this.items = response.data.items
                })
                .then(()=>{
                    this.loading_search = false
                })
        }, 
        customIndex(index) {
            return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
        },
        async getRecordsByFilter(){

            this.loading_submit = await true
            await this.getRecords()
            this.loading_submit = await false

        },
        getRecords() {
            this.records =  []
            this.errors = {}

            return this.$http.get(`/${this.resource}/stock/records?${this.getQueryParameters()}`).then((response) => {
                    this.records = response.data.data
                    this.pagination = response.data.meta
                    this.pagination.per_page = parseInt(response.data.meta.per_page)
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        console.log(error)
                    }
                })
                .then(() => {
                    this.loading_submit = false
                })


        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page,
                limit: this.limit,
                ...this.form
            })
        },
        close() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
    }
}
</script>
