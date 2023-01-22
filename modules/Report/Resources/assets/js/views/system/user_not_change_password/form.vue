<template>
    <el-dialog width="85%" :title="titleDialog" :visible="showDialog" @close="close" @open="create" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <div class="form-body">
            <div class="row">
                <div class="col-md-12">
                    
                    <div v-loading="loading_submit">
                        <div class="row ">
                            <div class="col-md-12 col-lg-12 col-xl-12 ">
                                <div class="row">
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
                                                <th>Email</th>
                                                <th>Nombre</th>
                                                <th>Perfil</th>
                                                <th>Establecimiento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in records">
                                                
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ row.email }}</td>
                                                <td>{{ row.name }}</td>
                                                <td>{{ row.type }}</td>
                                                <td>{{ row.establishment_description }}</td>
                                            </tr>
                                        </tbody>

                                    </table>
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
                resource: 'reports/user-not-change-password',
                errors: {},
                form: {},
                showTable: false,
                search: {
                    record_id: null,
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
            getRecords() {
                this.loading_submit = true;
                this.search.record_id = this.recordId
                return this.$http
                    .get(`/${this.resource}/records?${this.getQueryParameters()}`)
                    .then(response => {
                        this.records = response.data.data;
                    })
                    .catch(error => {})
                    .then(() => {
                        this.loading_submit = false;
                    });
            },
            getQueryParameters() {
                
                return queryString.stringify({
                    ...this.search
                });
            },
            changeClearInput() {
                this.getRecords();
            },
            async create() {
                this.records = []
                this.titleDialog = 'Usuarios con contraseña desactualizada'
                
                await this.getRecords();
            },
            close() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>
