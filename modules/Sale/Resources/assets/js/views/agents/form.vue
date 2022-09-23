<template>
    <el-dialog :title="titleDialog" :visible="showDialog"  @close="close" @open="create" append-to-body top="7vh">
        <form autocomplete="off" @submit.prevent="submit" v-loading="loading">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.internal_id}">
                            <label class="control-label">Código</label>
                            <el-input v-model="form.internal_id" ></el-input>
                            <small class="form-control-feedback" v-if="errors.internal_id" v-text="errors.internal_id[0]"></small>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Nombre</label>
                            <el-input v-model="form.name" ></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.email}">
                            <label class="control-label">Correo electrónico</label>
                            <el-input v-model="form.email" ></el-input>
                            <small class="form-control-feedback" v-if="errors.email" v-text="errors.email[0]"></small>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.telephone}">
                            <label class="control-label">Teléfono</label>
                            <el-input v-model="form.telephone" ></el-input>
                            <small class="form-control-feedback" v-if="errors.telephone" v-text="errors.telephone[0]"></small>
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
                resource: 'agents', 
                form: {}, 
                loading: false,
            }
        },
        created() {
            this.initForm() 
        },
        methods: { 
            initForm() {

                this.loading_submit = false

                this.errors = {}

                this.form = {
                    id: null,
                    name: null, 
                    internal_id: null, 
                    email: null, 
                    telephone: null, 
                }
            }, 
            create() 
            {
                this.setTitle()
                this.getRecord()
            }, 
            getRecord()
            {
                if (this.recordId) 
                {
                    this.loading = true
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = response.data.data
                        })
                        .then(() => {
                            this.loading = false
                        })
                }
            },
            setTitle()
            {
                this.titleDialog = this.recordId ? 'Actualizar agente' : 'Registrar agente'
            },
            submit() { 

                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) 
                        {
                            this.$message.success(response.data.message) 
                            this.$eventHub.$emit('reloadData')
                            this.close()
                        } 
                        else 
                        {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) 
                        {
                            this.errors = error.response.data
                        } else {
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
