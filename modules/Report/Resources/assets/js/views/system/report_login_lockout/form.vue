<template>
    <el-dialog width="85%" :title="titleDialog" :visible="showDialog" @close="close" @open="create" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <div class="form-body">
            <div class="row">
                <div class="col-md-12">
                    
                    <div v-loading="loading_submit">
                        <div class="row ">
                            <div class="col-md-12 col-lg-12 col-xl-12 ">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2">
                                        <div class="d-flex">
                                            <div style="width:100px">
                                                Filtrar por:
                                            </div>
                                            <el-select
                                                v-model="search.column"
                                                placeholder="Select"
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
                                    <div class="col-lg-4 col-md-4 col-sm-12 pb-2">
                                        <template
                                            v-if="
                                                search.column === 'date_of_issue' ||
                                                    search.column === 'date_of_due' ||
                                                    search.column === 'date_of_payment' ||
                                                    search.column === 'date' ||
                                                    search.column === 'delivery_date'
                                            "
                                        >
                                            <el-date-picker
                                                v-model="search.value"
                                                type="date"
                                                style="width: 100%;"
                                                placeholder="Buscar"
                                                value-format="yyyy-MM-dd"
                                                @change="getRecords"
                                            >
                                            </el-date-picker>
                                        </template>
                                        <template v-else>
                                            <el-input
                                                placeholder="Buscar"
                                                v-model="search.value"
                                                style="width: 100%;"
                                                prefix-icon="el-icon-search"
                                                @input="getRecords"
                                            >
                                            </el-input>
                                        </template>
                                    </div>
                                    <!-- <div class="col-lg-4 col-md-4 col-sm-12 pb-2" v-if="records.length > 0">
                                        <el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>
                                    </div> -->

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">

                                        <thead>
                                            <tr slot="heading" style="width: 100%;">
                                                <th>#</th>
                                                <th class="text-center">Fecha y Hora</th>
                                                <th>Correo</th>
                                                <th>Transacción</th>
                                                <th class="text-center">Ip</th>
                                                <th class="text-center">Ubicación</th>
                                                <th>Dispositivo</th>
                                                <th>Tipo dispositivo</th>
                                                <th>Plataforma</th>
                                                <th>Navegador</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in records">
                                                <td>{{ customIndex(index) }}</td>
                                                <td class="text-center">{{ row.date_time }}</td>
                                                <td>{{ row.request_email }}</td>
                                                <td class="text-justify">{{ row.system_activity_log_type_description }}</td>
                                                <td class="text-center">{{ row.ip }}</td>
                                                
                                                <td class="text-center">

                                                    <template v-if="row.location">
                                                        <el-popover
                                                            placement="right"
                                                            width="300"
                                                            trigger="click">
                                                            <div>
                                                                <p><b>País: </b> {{ row.location.country_code }} - {{ row.location.country_name }}</p>
                                                                <p><b>Región: </b> {{ row.location.region_code }} - {{ row.location.region_name }}</p>
                                                                <p><b>Ciudad: </b> {{ row.location.city_name }}</p>
                                                                <p><b>Zona horaria: </b> {{ row.location.timezone }}</p>
                                                            </div>
                                                            <el-button slot="reference"> <i class="fa fa-eye"></i></el-button>
                                                        </el-popover>
                                                    </template>

                                                </td>

                                                <td>{{ row.device_name }}</td>
                                                <td>{{ row.device_type_description }}</td>
                                                <td>{{ row.platform_description }}</td>
                                                <td>{{ row.browser_description }}</td>
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
                        </div>
                    </div>

                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-right">
                    <el-button @click.prevent="close()">Cerrar</el-button>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>
    import queryString from "query-string";

    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'reports/report-login-lockout',
                errors: {},
                form: {},
                showTable: false,
                search: {
                    column: null,
                    value: null,
                    record_id: null,
                    system_activity_log_type_id: 'login_lockout'
                },
                columns: [],
                records: [],
                pagination: {},
            }
        },
        created() {
            this.$eventHub.$on("reloadData", () => {
                this.getRecords();
            });
        },
        async mounted() {
        },
        methods: { 
            clickDownload(type) 
            {
                const query = queryString.stringify({
                    ...this.search
                })

                window.open(`/${this.resource}/report/${type}/?${query}`, '_blank');
            },
            customIndex(index) {
                return (
                    this.pagination.per_page * (this.pagination.current_page - 1) +
                    index +
                    1
                );
            },
            getRecords() {
                this.loading_submit = true;
                this.search.record_id = this.recordId
                return this.$http
                    .get(`/${this.resource}/records?${this.getQueryParameters()}`)
                    .then(response => {
                        this.records = response.data.data;
                        this.pagination = response.data.meta;
                        this.pagination.per_page = parseInt(
                            response.data.meta.per_page
                        );
                    })
                    .catch(error => {})
                    .then(() => {
                        this.loading_submit = false;
                    });
            },
            getQueryParameters() {
                
                return queryString.stringify({
                    page: this.pagination.current_page,
                    limit: this.limit,
                    ...this.search
                });
            },
            changeClearInput() {
                this.search.value = "";
                this.getRecords();
            },
            async create() {
                this.records = []
                this.titleDialog = 'Cuentas bloquedas'
                await this.$http
                    .get(`/${this.resource}/columns`)
                    .then(response => {
                        this.columns = response.data;
                        this.search.column = _.head(Object.keys(this.columns));
                    });
                await this.getRecords();
            },
            close() {
                this.showTable = false
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>
