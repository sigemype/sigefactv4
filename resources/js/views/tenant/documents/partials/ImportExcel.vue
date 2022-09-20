<template>
    <el-dialog :title="title"
               :visible="showDialog"
               @close="close"
               @open="create"
               class="dialog-import">
        <form autocomplete="off" @submit.prevent="onSubmit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div :class="{ 'has-danger': errors.document_type_id }" class="form-group">
                            <label class="control-label">Tipo de documento</label>
                            <el-select v-model="form.document_type_id" @change="changeDocumentType">
                                <el-option
                                    v-for="option in document_types"
                                    :key="option.id"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.document_type_id"
                                class="form-control-feedback"
                                v-text="errors.document_type_id[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div :class="{ 'has-danger': errors.series }" class="form-group">
                            <label class="control-label">Serie</label>
                            <el-select v-model="form.series">
                                <el-option
                                    v-for="option in series"
                                    :key="option.number"
                                    :label="option.number"
                                    :value="option.number"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.series"
                                class="form-control-feedback"
                                v-text="errors.series[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="form-group text-center" :class="{'has-danger': errors.file}">
                            <el-upload
                                ref="upload"
                                :headers="headers"
                                action="/documents/import_excel_format"
                                :data="{'document_type_id': form.document_type_id, 'series': form.series}"
                                :show-file-list="true"
                                :auto-upload="false"
                                :multiple="false"
                                :on-error="errorUpload"
                                :limit="1"
                                :on-success="successUpload">
                                <el-button slot="trigger" type="primary">Seleccione un archivo (xlsx)</el-button>
                            </el-upload>
                            <small class="form-control-feedback" v-if="errors.file" v-text="errors.file[0]"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loadingSubmit">Procesar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

export default {
    props: ['showDialog'],
    data() {
        return {
            loadingSubmit: false,
            headers: headers_token,
            title: null,
            resource: 'items',
            errors: {},
            form: {},
            document_types: [],
            all_series: [],
            series: [],
        }
    },
    async created() {
        await this.initTables();
        this.initForm()
    },
    methods: {
        async initTables() {
            await this.$http.get(`/documents/import_excel_tables`)
                .then(response => {
                    this.document_types = response.data.document_types;
                    this.all_series = response.data.series;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        initForm() {
            this.errors = {}
            this.form = {
                file: null,
                document_type_id: null,
                series: null,
            }
        },
        create() {
            this.title = 'Importar Documentos'
        },
        changeDocumentType() {
            this.form.series = null;
            this.series = _.filter(this.all_series, {'document_type_id': this.form.document_type_id});
        },
        async onSubmit() {
            this.loadingSubmit = true
            await this.$refs.upload.submit()
            this.loadingSubmit = false
        },
        successUpload(response, file, fileList) {
            if (response.success) {
                this.$message.success(response.message)
                this.$eventHub.$emit('reloadData')
                this.$refs.upload.clearFiles()
                this.close()
            } else {
                this.$message({message: response.message, type: 'error'})
            }
        },
        errorUpload(response) {
            console.log(response)
        },
        close() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
    }
}
</script>
