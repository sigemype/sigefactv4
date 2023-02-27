<template>
    <el-dialog :title="titleDialog"
               :visible="showDialog"
               append-to-body
               width="30%"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false"
               @open="create">
        <div v-if="form.response_message"
             class="row mb-4">
            <div class="col-md-12">
                <el-alert
                    :title="form.response_message"
                    :type="form.response_type"
                    show-icon>
                </el-alert>
            </div>
        </div>
        <div v-if="sendSunat">
            <div>Enviando comprobante a sunat</div>
            <div>{{ response_sunat_send.message }}</div>
            <template v-if="response_sunat_send.success">
                <div>Consultando ticket a sunat</div>
                <template v-if="response_sunat_status_ticket">
                    <div>{{ response_sunat_status_ticket.message }}</div>
                    <div v-if="!response_sunat_status_ticket.success">
                        <el-button class="list"
                                   @click="clickStatusTicket">Consultar ticket
                        </el-button>
                    </div>
                </template>
            </template>
        </div>

        <div v-if="form.send_to_pse" class="row">
            <div v-if="form.response_signature_pse"
                 class="col-lg-12 col-md-12 col-sm-12">
                <el-alert :title="`Firma Xml PSE: ${form.response_signature_pse}`"
                          show-icon
                          type="success"></el-alert>
            </div>
            <div v-if="form.response_send_cdr_pse"
                 class="col-lg-12 col-md-12 col-sm-12 mt-3">
                <el-alert :title="`Envio CDR PSE: ${form.response_send_cdr_pse}`"
                          show-icon
                          type="success"></el-alert>
            </div>
        </div>

        <template v-if="(response_sunat_status_ticket && response_sunat_status_ticket.success) || !sendSunat">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold mt-3">
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickDownload('a4')">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>Descargar A4</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold mt-3">
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickDownload('ticket')">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>80MM</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold mt-3">
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickDownload('ticket_58')">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>58MM</p>
                </div>
                <div v-if="sendSunat" class="col-lg-6 col-md-6 col-sm-6 text-center font-weight-bold mt-3">
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickDownloadCdr()">
                        <i class="fa fa-file-download"></i>
                    </button>
                    <p>Descargar CDR</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <el-input v-model="form.customer_email">
                        <el-button slot="append"
                                   :loading="loading"
                                   icon="el-icon-message"
                                   @click="clickSendEmail">Enviar
                        </el-button>
                    </el-input>
                    <small v-if="errors.customer_email"
                           class="form-control-feedback"
                           v-text="errors.customer_email[0]"></small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <el-input v-model="form.customer_telephone">
                        <template slot="prepend">+51</template>
                        <el-button slot="append"
                                   @click="clickSendWhatsapp">Enviar
                            <el-tooltip class="item"
                                        content="Es necesario tener aperturado Whatsapp web"
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
        </template>

        <span slot="footer"
              v-if="!loading_sunat_send"
              class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list"
                           @click="clickFinalize">Ir al listado</el-button>
                <el-button type="primary"
                           @click="clickNewDocument">{{ text_button }}</el-button>
            </template>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ['showDialog',
        'recordId',
        'sendSunat',
        'showClose'
    ],
    name: 'DispatchFinish',
    data() {
        return {
            titleDialog: null,
            loading: false,
            loading_sunat_send: false,
            loading_sunat_send_response: false,
            loading_sunat_status_ticket: false,
            loading_sunat_status_ticket_response: false,
            resource: 'dispatches',
            errors: {},
            form: {},
            company: {},
            locked_emission: {},
            text_button: null,
            response_sunat_send: {},
            response_sunat_status_ticket: {},
        }
    },
    async created() {
        this.initForm()
        this.text_button = 'Nueva guía'
    },
    methods: {
        initForm() {
            this.loading_sunat_send = false;
            this.errors = {};
            this.form = {
                customer_email: null,
                download_external_pdf: null,
                external_id: null,
                number: null,
                id: null,
                response_message: null,
                response_type: null,
                customer_telephone: null,
                message_text: null,
                download_cdr: null,
                state_type_id: '05',
                has_cdr: true,
                send_to_pse: false,
                response_signature_pse: null,
                response_send_cdr_pse: null,
            }
            this.locked_emission = {
                success: true,
                message: null
            }
            this.company = {
                soap_type_id: null,
            }
            this.response_sunat_send = {
                'success': false,
                message: null
            }
            this.response_sunat_status_ticket = null
        },
        clickDownload(format = 'a4') {
            if ((this.form && this.form.external_id)) {
                window.open(`/print/dispatch/${this.form.external_id}/${format}`, '_blank');
            }
        },
        clickSendWhatsapp() {
            if (!this.form.customer_telephone) {
                return this.$message.error('El número es obligatorio')
            }
            window.open(`https://wa.me/51${this.form.customer_telephone}?text=${this.form.message_text}`, '_blank');
        },
        clickDownloadCdr() {
            window.open(this.form.download_cdr, '_blank');
        },
        timeout(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },
        async create() {
            this.initForm();
            this.loading_sunat_send = true;
            await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                this.form = response.data.data;
                this.titleDialog = 'Guía: ' + this.form.number;
            });
            if (this.sendSunat) {
                await this.$http.get(`/service/dispatch/send/${this.form.external_id}`)
                    .then(response => {
                        this.response_sunat_send = response.data;
                    });

                if (this.response_sunat_send.success) {
                    await this.timeout(3000);
                    await this.clickStatusTicket();
                }
            }
            this.loading_sunat_send = false;
        },
        clickPrint(format) {
            window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
        },
        clickSendEmail() {
            this.loading = true
            this.$http.post(`/${this.resource}/email`, {
                customer_email: this.form.customer_email,
                id: this.form.id
            })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success('El correo fue enviado satisfactoriamente')
                    } else {
                        this.$message.error('Error al enviar el correo')
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors
                    } else {
                        this.$message.error(error.response.data.message)
                    }
                })
                .then(() => {
                    this.loading = false
                })
        },
        clickFinalize() {
            if (this.form.document_type_id === '31') {
                location.href = `/dispatch_carrier`
            } else {
                location.href = `/${this.resource}`
            }
        },
        clickNewDocument() {
            if (this.form.document_type_id === '31') {
                location.href = `/dispatch_carrier/create`
            } else {
                location.href = `/${this.resource}/create`
            }
            // this.clickClose()
        },
        clickClose() {
            this.$emit('update:showDialog', false);
        },
        async clickStatusTicket() {
            await this.$http.get(`/service/dispatch/status_ticket/${this.form.external_id}`)
                .then(response => {
                    this.response_sunat_status_ticket = response.data;
                });
        }
    }
}
</script>
