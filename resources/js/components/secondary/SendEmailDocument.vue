<template>
    <div>
        <el-dialog
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :show-close="false"
            :title="titleDialog"
            :visible="showDialog"
            width="30%"
            @open="create"
        >
            <div class="row">
                <div class="col-md-12" :class="{'has-danger': errors.customer_email}">
                    <el-input v-model="form.customer_email" placeholder="Correo electrónico">
                        <el-button
                            slot="append"
                            :loading="loading"
                            icon="el-icon-message"
                            @click="clickSendEmail"
                        >Enviar
                        </el-button>
                    </el-input>
                    <small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <el-input v-model="form.customer_telephone">
                        <template slot="prepend">+51</template>
                        <el-button slot="append"
                                   @click="clickSendWhatsapp">Enviar
                            <el-tooltip class="item"
                                        content="Se recomienta tener abierta la sesión de Whatsapp web"
                                        effect="dark"
                                        placement="top-start">
                                <i class="fab fa-whatsapp"></i>
                            </el-tooltip>
                        </el-button>
                    </el-input>
                    <small v-if="errors.customer_telephone"
                           class="form-control-feedback"
                           v-text="errors.customer_telephone[0]"></small>
                </div>
            </div>


            <span slot="footer" class="dialog-footer">
                <el-button @click="clickClose">Cerrar</el-button>
            </span>

        </el-dialog>

    </div>
</template>

<script>

export default {

    props: [
        "showDialog",
        "recordId",
        "resource",
    ],
    computed:{
    },
    data() {
        return {
            customer_email: "",
            titleDialog: null,
            loading: false,
            errors: {},
            form: {},
            loading_submit: false,
        }
    },
    created() {
        this.initForm()
    },
    methods: {
        clickSendWhatsapp() 
        {
            if (!this.form.customer_telephone) 
            {
                return this.$message.error('El número es obligatorio')
            }
            window.open(`https://wa.me/51${this.form.customer_telephone}?text=${this.form.message_text}`, '_blank')
        },
        initForm() 
        {
            this.errors = {}

            this.form = {
                id: null,
                number_full: null,
                customer_email: null,
                customer_telephone: null,
                customer_id: null,
                message_text: null,
            }
        },
        async create() 
        {
            await this.getRecord()
        },
        async getRecord()
        {
            await this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then((response) => {

                            this.setDataToForm(response.data.data)

                        })
        },
        setDataToForm(data)
        {
            this.form.id = data.id
            this.form.number_full = data.number_full
            this.form.customer_email = data.customer_email
            this.form.customer_telephone = data.customer_telephone
            this.form.customer_id = data.customer_id
            this.form.message_text = data.message_text

            this.titleDialog =  `Documento: ` + this.form.number_full
        },
        clickClose() 
        {
            this.$emit("update:showDialog", false)
            this.initForm()
        },
        async clickSendEmail()
        {
            this.loading = true

            await this.$http.post(`/${this.resource}/email`, this.form)
                        .then((response) => {
                            if (response.data.success) 
                            {
                                this.$message.success("El correo fue enviado satisfactoriamente")
                            }
                            else 
                            {
                                this.$message.error("Error al enviar el correo")
                            }
                        })
                        .catch((error) => {
                            
                            if (error.response.status === 422) 
                            {
                                this.errors = error.response.data
                            }
                            else 
                            {
                                this.$message.error("Error al enviar el correo")
                            }
                        })
                        .then(() => {
                            this.loading = false
                        })
        },
    },
}
</script>
