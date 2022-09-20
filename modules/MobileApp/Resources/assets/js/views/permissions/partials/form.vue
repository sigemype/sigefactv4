<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" >
        <form autocomplete="off" @submit.prevent="submit" v-loading="loading">
            <div class="form-body">
                <div class="row">
                    <!-- <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Nombre</label>
                            <el-input v-model="form.name" readonly></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.email}">
                            <label class="control-label">Correo Electrónico</label>
                            <el-input v-model="form.email" readonly></el-input>
                            <small class="form-control-feedback" v-if="errors.email" v-text="errors.email[0]"></small>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 mt-1 mb-1" v-for="(app_module, index) in form.app_modules" :key="index">
                                    <el-checkbox v-model="app_module.checked" :disabled="form.locked">{{ app_module.description }}</el-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>

</template>

<script>


    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'app-permissions',
                errors: {},
                form: {},
                app_configuration: {},
                app_modules: [],
                loading: false,
                pos_document_types: []
            }
        },
        async created() {
            await this.initForm()
        },
        methods: {
            async getTables(){
                
                this.loading = true
                await this.$http.get(`/${this.resource}/tables`)
                    .then(response => {
                        this.app_configuration = response.data.app_configuration
                        this.pos_document_types = response.data.pos_document_types
                    })
                    .then(()=>{
                        this.loading = false
                    })
            },
            initForm(){
                this.errors = {}
                this.form = {
                    id: null,
                    name: null,
                    email: null,
                    app_modules: [],
                }
            },
            async create(){
                await this.getTables()
                await this.getRecord()
            },
            async getRecord(){

                this.loading = true
                await this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                            this.titleDialog = `Asignar permisos: ${this.form.name}`
                        })
                        .then(()=>{
                            this.loading = false
                        })

            },
            validatePermissions(){

                const selected_modules = _.filter(this.form.app_modules, {checked:true})

                if(selected_modules.length == 0)
                {
                    return {
                        success: false,
                        message: 'Debe seleccionar al menos una opción'
                    }
                }

                if(this.app_configuration.app_mode === 'pos')
                {

                    let selected_module = 0

                    this.pos_document_types.forEach(value => {
                        
                        const find_selected_module = _.some(this.form.app_modules, { value: value, checked: true})

                        if(find_selected_module) selected_module++
                    })
                    
                    if(selected_module === 0)
                    {
                        return {
                            success: false,
                            message: 'Si tiene configurado el modo POS, debe habilitar al menos una opción: Factura, Boleta o Nota de venta.'
                        } 
                    }
                }

                return {
                    success: true
                }

            },
            submit() {

                const validate_permissions = this.validatePermissions()
                if(!validate_permissions.success) return this.$message.error(validate_permissions.message)

                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            this.$message.error(error.response.data.message)
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })

            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>
