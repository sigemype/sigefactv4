<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
            </div>
        </div>
        <div class="card mb-0" v-loading="loading">
            <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="card-body">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.warehouse_id}">
                            <label class="control-label font-weight-bold text-info">
                                Sucursal
                            </label>
                            <el-select v-model="form.warehouse_id" filterable @change="changeWarehouse">
                                <el-option v-for="option in warehouses" :key="option.id" :value="option.id" :label="option.establishment_description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.warehouse_id" v-text="errors.warehouse_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.category_id}">
                            <label class="control-label font-weight-bold text-info">
                                Categoría
                            </label>
                            <el-select v-model="form.category_id" filterable clearable>
                                <el-option v-for="option in categories" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.category_id" v-text="errors.category_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.filter_by_variants}">
                            <el-checkbox class="mt-4" v-model="form.filter_by_variants">Filtrar por variantes </el-checkbox>
                        </div>
                    </div>

                </div>

                <div class="row mt-3" v-if="form.filter_by_variants">
                    
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.item_size_id}">
                            <label class="control-label font-weight-bold text-info">
                                Talla
                            </label>
                            <el-select v-model="form.item_size_id" filterable clearable>
                                <el-option v-for="option in item_sizes" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.item_size_id" v-text="errors.item_size_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.item_color_id}">
                            <label class="control-label font-weight-bold text-info">
                                Color
                            </label>
                            <el-select v-model="form.item_color_id" filterable clearable>
                                <el-option v-for="option in item_colors" :key="option.id" :value="option.id" :label="option.name"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.item_color_id" v-text="errors.item_color_id[0]"></small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3"  v-if="records.length > 0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                Producto
                                <el-tooltip class="item"
                                            effect="dark"
                                            content="Busca el producto por código de barras en el listado, si lo encuentra incrementará en 1 el stock escaneado"
                                            placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input
                                ref="inputSearchByBarcode"
                                placeholder="Buscar producto por código de barras"
                                v-model="input_search_barcode"
                                @change="changeInputSearch">
                            </el-input>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    
                    <div class="col-md-12">
                        <el-button :loading="loading_submit"
                                   class="submit"
                                   icon="el-icon-search"
                                   type="primary"
                                   @click.prevent="getRecords">Buscar
                        </el-button>

                        <template v-if="records.length > 0">
                            <el-button :loading="loading_review"
                                    icon="el-icon-check"
                                    class="ml-3"
                                    type="success"
                                    @click.prevent="reviewStock">Revisar stock
                            </el-button>
                        </template>
                        
                        <template v-if="records.length > 0 && init_review">
                            <el-button
                                    icon="el-icon-tickets"
                                    class="ml-3"
                                    type="danger"
                                    @click.prevent="clickDownload('pdf')"> Exportar PDF
                            </el-button>

                            <el-button
                                    class="ml-3"
                                    type="success"
                                    @click.prevent="clickDownload('xlsx')"><i class="fa fa-file-excel"></i> Exportar Excel
                            </el-button>
                        </template>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">

                        <template v-if="records.length > 0">
                            <data-tables 
                                :data="records"
                                :current-page.sync="currentPage" 
                                :table-props="tableProps"
                                :pagination-props="{ pageSizes: [20] }"
                                style="width: 100%" 
                            >
                            
                                <el-table-column
                                    width="50"
                                    prop="index"
                                    label="#">
                                </el-table-column>

                                <el-table-column prop="item_barcode"  label="Cód. Barras">
                                </el-table-column>

                                <el-table-column prop="item_description"  label="Producto">
                                </el-table-column>

                                <el-table-column prop="stock"  label="Stock sistema">
                                </el-table-column>

                                
                                <el-table-column prop="input_stock"  label="Stock escaneado">
                                    
                                    <template slot-scope="scope">  
                                        <el-input-number v-model="scope.row.input_stock" :min="0" :step="1"></el-input-number>
                                    </template>

                                </el-table-column>

                                <el-table-column prop="difference"  label="Diferencia Stock">
                                    
                                    <template slot-scope="scope">  
                                        <template v-if="scope.row.difference != null">
                                            <span :class="scope.row.difference < 0 ? 'text-danger' : ''"><b>{{ scope.row.difference }}</b></span>
                                        </template>
                                    </template>

                                </el-table-column>
                            </data-tables>
                        </template>
                         
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>

    import queryString from 'query-string'
    import { DataTables } from 'vue-data-tables'

    export default {
        components: {
            DataTables,
        },
        props: ['typeUser'],
        data() {
            return {
                title: null,
                form: {},
                resource: 'inventory-review',
                warehouses: [],
                categories: [],
                item_sizes: [],
                item_colors: [],
                records: [],
                errors: {},
                loading_submit: false,
                tableProps: {
                    border: true,
                },
                currentPage: 1,
                per_page: 20,
                loading_review: false,
                input_search_barcode: null,
                init_review: false,
                loading: false,
            }
        },
        created() {
            this.title = 'Revisión de inventario'
            this.initForm()
            this.filters()
        },
        methods: {
            async clickDownload(format)
            {
                this.loading = true

                await this.$http({
                        url: `/${this.resource}/export`,
                        method: 'POST',
                        data: {
                            records: this.records,
                            format: format,
                        },
                        responseType: 'blob'
                    })
                    .then(response => {

                        const res = response.data
                        const url = window.URL.createObjectURL(new Blob([res]))
                        const link = document.createElement('a')
                        link.href = url
                        link.setAttribute('download', 'Revision_stock_' + moment().format('HHmmss') + '.' + format)
                        document.body.appendChild(link)
                        link.click()

                    })
                    .catch(error => {
                        console.log(error)
                    })
                    .then(() => {
                        this.loading = false
                    })
            },
            findItem(barcode)
            {
                return _.find(this.records, {item_barcode: barcode})
            },
            changeInputSearch()
            {
                const item = this.findItem(this.input_search_barcode)

                if(item)
                {
                    item.input_stock = parseFloat(item.input_stock) + 1
                    this.input_search_barcode = null
                    this.$refs.inputSearchByBarcode.$el.getElementsByTagName('input')[0].focus()
                    
                    this.$notify({
                        title: '',
                        message: 'Cantidad incrementada!',
                        type: 'success',
                        duration: 700
                    })
                }
                else
                {
                    this.itemBarcodeNotFound()
                }
            },
            itemBarcodeNotFound()
            {
                this.input_search_barcode = null
                this.$message.error('No se encontró el producto.')
            },
            setFocusInInputSearch()
            {
                this.$refs.inputSearchByBarcode.$el.getElementsByTagName('input')[0].focus()
            },
            async reviewStock()
            {
                this.loading_review = true
                this.init_review = true

                await this.sleep(200)

                await this.records.forEach(row => {
                    row.difference = row.stock - parseFloat(row.input_stock)
                })

                this.loading_review = false

            },
            sleep(ms) 
            {
                return new Promise(resolve => setTimeout(resolve, ms))
            },
            getQueryParameters() 
            {
                return queryString.stringify({
                    ...this.form
                })
            },
            async getRecords()
            {
                // if(this.form.filter_by_variants)
                // {
                //     if(!this.form.item_color_id && !this.form.item_size_id) return this.$message.error('Debe seleccionar al menos un filtro de las variantes.')
                // }

                this.loading_submit = true
                await this.$http.get(`/${this.resource}/records?${this.getQueryParameters()}`)
                    .then(response => {
                        this.records = response.data.data
                    })
                    .then(()=>{
                        this.loading_submit = false
                        this.setFocusInInputSearch()
                    })
            },
            changeWarehouse()
            {
                const warehouse = _.find(this.warehouses, {id : this.form.warehouse_id})
                this.form.establishment_id = warehouse.establishment_id
            },
            initForm()
            {
                this.form = {
                    warehouse_id: null,
                    establishment_id: null,
                    category_id: null,
                    filter_by_variants: false,
                    item_color_id: null,
                    item_size_id: null,
                }
            },
            filters() 
            {
                this.$http.get(`/${this.resource}/filters`)
                    .then(response => {
                        this.warehouses = response.data.warehouses
                        this.categories = response.data.categories
                        this.item_colors = response.data.item_colors
                        this.item_sizes = response.data.item_sizes
                        
                        this.form.warehouse_id = this.warehouses.length > 0 ? this.warehouses[0].id : null
                        this.changeWarehouse()
                    })
            }
        }
    }
</script>
