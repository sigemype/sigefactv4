<template>
    <el-dialog width="40%" :title="titleDialog" :visible="showDialog" @open="create" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row" >
                    <div class="col-md-12">
                        <el-alert
                            :title="getAlertTitle"
                            type="error"
                            :closable="false"
                            show-icon>
                        </el-alert>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div :class="{ 'has-danger': errors.token }" class="form-group">
                            <label class="control-label">Token de autorización</label>
                            <el-input v-model="form.token"></el-input>
                            <small v-if="errors.token" class="form-control-feedback" v-text="errors.token[0]"></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions text-right mt-3">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Validar token</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'sellersDiscountLimit', 'totalDiscountPercentage'],
        data() {
            return {
                titleDialog: 'Permiso para registrar descuento',
                loading: false,
                errors: {},
                form: {},
                resource: 'authorized-discount-users',
                loading_submit: false,
            }
        },
        computed:
        {
            getAlertTitle()
            {
                return `El porcentaje de descuento de la venta (${this.totalDiscountPercentage}%), es superior al límite configurado (${this.sellersDiscountLimit}%). Si desea continuar, comuníquese con el administrador.`
            }
        },
        async created() 
        {

        },
        methods: {
            initForm()
            {
                this.form = {
                    token: null
                }
            },
            create()
            {

            },
            async submit()
            {
                if(!this.form.token) return this.$message.error('Ingrese el token de autorización')

                this.loading_submit = true

                await this.$http.post(`/${this.resource}/validate-token`, this.form).then(response => {
                    
                    if (response.data.success) 
                    {
                        this.$message.success(response.data.message)
                        this.$emit('tokenValidated')
                        this.close()

                    } else {
                        this.$message.error(response.data.message)
                    }

                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        this.$message.error(error.response.data.message)
                    }
                }).then(() => {
                    this.loading_submit = false
                })

            },
            close() 
            {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>
