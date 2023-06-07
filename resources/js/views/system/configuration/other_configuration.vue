<template>
<div class="card">
    <div class="card-header bg-info">
        <h3 class="my-0">Otras configuraciones</h3>
    </div>
    <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row">
                <div class="col-md-6">
                    
                    <label class="control-label">
                        Mostrar publicidad
                        <el-tooltip class="item"
                                    content="Se visualizará la imágen cargada en el header de todos los clientes."
                                    effect="dark"
                                    placement="top-start">
                            <i class="fa fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    
                    <div :class="{'has-danger': errors.tenant_show_ads}"
                            class="form-group">
                        <el-switch v-model="form.tenant_show_ads"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.tenant_show_ads"
                                class="form-control-feedback"
                                v-text="errors.tenant_show_ads[0]"></small>
                    </div>
                </div>

                <div class="col-md-6" v-if="form.tenant_show_ads">
                    <div class="form-group">
                        <label class="control-label">Imágen para publicidad</label>
                        <el-input v-model="form.tenant_image_ads" :readonly="true">
                            <el-upload
                                slot="append"
                                :headers="headers"
                                :on-success="successUpload"
                                :on-error="errorUpload"
                                :show-file-list="false"
                                action="/configurations/upload-tenant-ads">
                                <el-button icon="el-icon-upload" type="primary"></el-button>
                            </el-upload>
                        </el-input>
                        <div class="sub-title text-danger"><small>Se recomienda resoluciones 500x50</small>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-6">
                    
                    <label class="control-label">
                        Habilitar contraseña segura
                        <el-tooltip class="item"
                                    content="Se solicitará una contraseña segura (cumplir patrón) al registrar cliente"
                                    effect="dark"
                                    placement="top-start">
                            <i class="fa fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    
                    <div :class="{'has-danger': errors.regex_password_client}"
                            class="form-group">
                        <el-switch v-model="form.regex_password_client"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.regex_password_client"
                                class="form-control-feedback"
                                v-text="errors.regex_password_client[0]"></small>
                    </div>
                </div>
            </div>
            <!-- <div class="form-actions text-right pt-2">
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div> -->
        </form>
    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            headers: headers_token,
            loading_submit: false,
            resource: 'configurations',
            errors: {},
            form: {},
        }
    },
    async created() {
        await this.initForm()
        await this.getData()
    },
    methods: {
        async getData()
        {
            await this.$http.get(`/${this.resource}/get-other-configuration`)
                .then(response => {
                    this.form = response.data
                })
        },
        successUpload(response, file, fileList) 
        { 
            if (response.success) {
                this.$message.success(response.message)
                this.form.tenant_image_ads = response.name
            } else {
                this.$message({message: 'Error al subir el archivo', type: 'error'})
            }
        },
        errorUpload(error)
        {
            this.$message({message: 'Error al subir el archivo', type: 'error'})
        },
        initForm() {
            this.errors = {}
            this.form = {
                regex_password_client: false,
                tenant_show_ads: false,
                tenant_image_ads: null,
            }
        },
        submit() {
            this.loading_submit = true
            this.$http
                .post(`/${this.resource}/other-configuration`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                    } else {
                        this.$message.error(response.data.message)
                    }
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
    }
}
</script>
