<template>
    <el-dialog :title="title"
               :visible="showDialog"
               @close="clickClose"
               @open="handleOpen"
               width="600px">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{'has-danger': errors.voucher_date_of_issue}">
                        <label class="control-label">Fecha de emisón</label>
                        <el-date-picker v-model="form.voucher_date_of_issue"
                                        type="date"
                                        :clearable="false"
                                        format="dd/MM/yyyy"
                                        value-format="yyyy-MM-dd"></el-date-picker>
                        <small class="form-control-feedback" v-if="errors.voucher_date_of_issue"
                               v-text="errors.voucher_date_of_issue[0]"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0" :class="{'has-danger': errors.voucher_number}">
                        <label class="control-label">Número de comprobante</label>
                        <el-input v-model="form.voucher_number"></el-input>
                        <small class="form-control-feedback" v-if="errors.voucher_number"
                               v-text="errors.voucher_number[0]"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0" :class="{'has-danger': errors.amount}">
                        <label class="control-label">Monto de retención (PEN)</label>
                        <el-input v-model="form.amount"
                                  :readonly="true"></el-input>
                        <small class="form-control-feedback" v-if="errors.amount"
                               v-text="errors.amount[0]"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0" :class="{'has-danger': errors.voucher_amount}">
                        <label class="control-label">Monto pagado (PEN)</label>
                        <el-input v-model="form.voucher_amount"></el-input>
                        <small class="form-control-feedback" v-if="errors.voucher_amount"
                               v-text="errors.voucher_amount[0]"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0" :class="{'has-danger': errors.voucher_filename}">
                        <label class="control-label">Archivo</label>
                        <template v-if="form.voucher_filename">
                            <div>{{ form.voucher_filename }}</div>
                        </template>
                        <el-upload
                            :data="{}"
                            :headers="headers"
                            :multiple="false"
                            :on-remove="handleRemove"
                            :action="`/documents/retention/upload`"
                            :show-file-list="true"
                            :file-list="fileList"
                            :on-success="onSuccess"
                            :limit="1"
                            style="width: 100%;"
                            v-else>
                            <button type="button" class="btn btn-sm btn-primary"
                                    slot="trigger">
                                <i class="fas fa-fw fa-upload"></i>
                                Cargar comprobante
                            </button>
                        </el-upload>
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <el-button @click.prevent="clickClose"
                               style="margin-right: 4px">Cancelar
                    </el-button>
                    <el-button :loading="loadingSubmit"
                               @click="onSubmit"
                               type="primary">Guardar
                    </el-button>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>

import {deletable} from '../../../../mixins/deletable'
import DialogLinkPayment from './dialog_link_payment'

export default {
    props: ['showDialog', 'documentId'],
    mixins: [deletable],
    components: {
        DialogLinkPayment,
    },
    data() {
        return {
            loadingSubmit: false,
            title: null,
            resource: 'documents',
            errors: {},
            form: {},
            records: [],
            payment_destinations: [],
            headers: headers_token,
            fileList: [],
            payment_method_types: [],
            showAddButton: true,
            document: {},
            permissions: {},
            index_file: null,
        }
    },
    async created() {
        await this.initForm();
        // await this.$http.get(`/${this.resource}/tables`)
        //     .then(response => {
        //         this.payment_method_types = response.data.payment_method_types;
        //         this.payment_destinations = response.data.payment_destinations
        //         this.permissions = response.data.permissions
        //         //this.initDocumentTypes()
        //     })
        // await this.events();

    },
    methods: {
        initForm() {
            this.title = null;
            this.errors = {};
            this.form = {
                'document_id': null,
                'document_number': null,
                'voucher_date_of_issue': null,
                'voucher_number': null,
                'amount': 0,
                'voucher_amount': 0,
                'voucher_filename': null,
                'temp_path': null,
            }
            this.fileList = [];
            this.showAddButton = true;
        },
        async handleOpen() {
            this.initForm();
            await this.$http.get(`/${this.resource}/retention/${this.documentId}`)
                .then(response => {
                    if (response.data.success) {
                        this.form = response.data.form
                    } else {
                        this.clickClose();
                    }
                });
            this.title = 'Retención (' + this.form.document_number + ')';
        },
        // events() {
        //     // this.$eventHub.$on('reloadDataPayments', () => {
        //     //     this.getData()
        //     // })
        // },
        getObjectResponse(success, message = null) {
            return {
                success: success,
                message: message,
            }
        },
        validateDataPayment(row) {
            if (!row.payment_destination_id) return this.getObjectResponse(false, 'El campo destino es obligatorio.')
            if (!row.payment_method_type_id) return this.getObjectResponse(false, 'El campo método de pago es obligatorio.')
            if (!row.payment || row.payment <= 0 || isNaN(row.payment)) return this.getObjectResponse(false, 'El campo monto es obligatorio y debe ser mayor que 0.')
            return this.getObjectResponse(true)
        },
        clickDownloadFile(filename) {
            window.open(
                `/finances/payment-file/download-file/${filename}/documents`,
                "_blank"
            );
        },
        onSuccess(response, file, fileList) {
            this.fileList = fileList
            if (response.success) {
                this.form.voucher_filename = response.data.filename
                this.form.temp_path = response.data.temp_path
            } else {
                this.cleanFileList()
                this.$message.error(response.message)
            }
        },
        cleanFileList() {
            this.fileList = []
        },
        handleRemove(file, fileList) {
            this.form.filename = null
            this.form.temp_path = null
        },
        async onSubmit(index) {
            if (_.isNull(this.form.voucher_number) || (this.form.voucher_number === '')) {
                this.$message.error('El número del comprobante es requerido.');
                return;
            }
            if (_.isNull(this.form.voucher_date_of_issue) || (this.form.voucher_date_of_issue === '')) {
                this.$message.error('La fecha de emisión es requerida.');
                return;
            }
            if (_.isNull(this.form.voucher_amount) || (this.form.voucher_date_of_issue === '')) {
                this.$message.error('El monto pagado es requerido.');
                return;
            }
            if (parseFloat(this.form.voucher_amount) !== parseFloat(this.form.amount)) {
                this.$message.error('El monto pagado y el monto son diferentes.');
                return;
            }

            this.loadingSubmit = true;
            await this.$http.post(`/${this.resource}/retention`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.clickClose();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    console.log(error);
                })
            this.loadingSubmit = false;
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() => {
                    this.getData()
                    this.$eventHub.$emit('reloadData')
                }
            )
        },
        clickClose() {
            this.$emit('update:showDialog', false);
        },
    }
}
</script>
