<template>
    <el-dialog width="35%" :title="title" :visible="showDialog" @close="close" @open="create" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <div class="form-body">
            <div class="row">

                <div class="col-md-12">
                    <div role="alert" class="el-alert el-alert--error is-light"><i class="el-alert__icon el-icon-error is-big"></i>
                        <div class="el-alert__content"><span class="el-alert__title is-bold fs-title">¿Está seguro de eliminar al cliente {{record.name}}?</span>
                            <p class="el-alert__description text-justify mt-3 fs-description">
                                <strong>Este proceso elimina al cliente y todos los recursos relacionados (base de datos, archivos, etc).</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-5"> 
                    
                    <div class="form-group" >
                        <label class="control-label font-weight-bold">Ingrese el número de ruc o nombre de la empresa para confirmar la acción<span class="text-danger"> *</span></label>
                        <p><el-tag type="info"><b>{{ record.number }}</b></el-tag> o <el-tag type="info"><b>{{ record.name }}</b></el-tag></p>
                        <el-input  v-model="form.input_validate" @input="inputCheckAvailableDelete"></el-input>
                    </div>
                </div> 
            </div> 
            
            <div class="form-actions text-right mt-3">
                <el-button @click.prevent="close()">Cancelar</el-button>

                <template v-if="is_available_delete">
                    <el-button :loading="loading_submit"
                            @click.prevent="clickDelete()"
                            type="danger">Eliminar
                    </el-button>
                </template>
                <template v-else>
                    <el-button type="info" disabled>Eliminar
                    </el-button>
                </template>
            </div>

        </div>
    </el-dialog>

</template>
<script>


    export default {
        props: ['showDialog', 'record'],

        data() {
            return {
                title: null,
                resource: 'clients',
                loading_submit: false,
                is_available_delete: false,
                form: {}
            }
        },
        async created() {
            await this.initForm() 
        },
        methods: { 
            inputCheckAvailableDelete(){

                if(this.form.input_validate)
                {
                    if(this.form.input_validate === this.record.name || this.form.input_validate === this.record.number)
                    {
                        this.is_available_delete = true
                    }
                    else
                    {
                        this.is_available_delete = false
                    }
                }
                else
                {
                    this.is_available_delete = false
                }
            },
            async clickDelete(){

                this.loading_submit = true

                await this.$http.delete(`${this.resource}/${this.record.id}/${this.form.input_validate}`)
                    .then(res => {

                        if(res.data.success) {
                            this.$message.success(res.data.message)
                            this.close()
                        }else{
                            this.$message.error(res.data.message)
                        }

                        this.$eventHub.$emit("reloadData")

                    })
                    .catch(error => {
                        if (error.response.status === 500) {
                            this.$message.error('Error al intentar eliminar');
                        } else {
                            console.log(error.response.data.message)
                        }
                    })
                    .then(()=>{
                        this.loading_submit = false
                    })

            },
            initForm() {
                this.form = {
                    input_validate: null
                }

                this.is_available_delete = false
            },
            create(){
                this.title = `Eliminar cliente`
            }, 
            close() {
                this.initForm()
                this.$emit('update:showDialog', false) 
            } 
        }
    }
</script>


<style scoped>

    .fs-title{
        font-size:15px;
    }

    .fs-description{
        font-size:13px;
    }

</style>