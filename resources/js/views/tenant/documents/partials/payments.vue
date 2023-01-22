<template>
    <el-dialog :title="title" :visible="showDialog" @close="close" @open="getData" width="80%">
        <div class="form-body">
            <div class="row">
                <div class="col-md-12" v-if="records.length > 0">
                    <!--<div class="right-wrapper pull-right">
                        <button type="button" @click.prevent="clickDownloadReport()" class="btn btn-custom btn-sm  mt-2 mr-2"><i class="fas fa-money-bill-wave-alt"></i> Reporte</button>
                    </div>-->

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha de pago</th>
                                <th>Método de pago <span class="text-danger">*</span></th>
                                <th>Destino <span class="text-danger">*</span></th>
                                <th class="text-center">Monto <span class="text-danger">*</span></th>
                                <!-- <th>Referencia</th> -->
                                <th>¿Pago recibido?</th>
                                <template v-if="external">
                                    <th>Imprimir</th>
                                </template>
                                
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in records" :key="index">
                                <template v-if="row.id">
                                    <td>PAGO-{{ row.id }}</td>
                                    <td>{{ row.date_of_payment }}</td>
                                    <td>{{ row.payment_method_type_description }}</td>
                                    <td>{{ row.destination_description }}</td>
                                    <!-- <td>{{ row.reference }}</td> -->
                                    <td class="text-center">{{ row.payment }}</td>

                                    <td class="text-left">

                                        <!-- pagos que no cuenten con la opcion pago recibido -->
                                        <template v-if="row.payment_received === null">

                                            <span class="d-block" v-if="row.reference"><b>Referencia:</b> {{ row.reference }}</span>
                                            <button  type="button" v-if="row.filename" class="btn waves-effect waves-light btn-xs btn-primary mb-2  mt-2" @click.prevent="clickDownloadFile(row.filename)">
                                                <i class="fas fa-fw fa-file-download"></i>
                                                Descargar voucher
                                            </button>
                                            <!-- <el-button type="primary" @click="showDialogLinkPayment(row)">Link de pago</el-button> -->

                                        </template>
                                        <!-- nuevo flujo -->
                                        <template v-else>

                                            <span class="d-block mb-2 font-bold">{{ row.payment_received_description }}</span>

                                            <template v-if="row.payment_received">

                                                <span class="d-block" v-if="row.reference"><b>Referencia:</b> {{ row.reference }}</span>
                                                <button  type="button" v-if="row.filename" class="btn btn-sm btn-primary mb-2  mt-2" @click.prevent="clickDownloadFile(row.filename)">
                                                    <i class="fas fa-fw fa-file-download"></i>
                                                    Descargar voucher
                                                </button>

                                            </template>
                                            <template v-else>
                                                <button  type="button" class="btn btn-sm btn-primary" @click="showDialogLinkPayment(row)">
                                                    <i class="fas fa-fw fa-link"></i>
                                                    Link de pago
                                                </button>
                                            </template>

                                        </template>

                                    </td>
                                <template  v-if="external">
                                    <td class="series-table-actions text-center">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-primary" @click.prevent="clickOptionsPrint()"><i class="fas fa-file-upload"></i></button>
                                    </td>
                                </template>
                                    

                                    <td class="series-table-actions text-right">

                                        <template v-if="permissions.delete_payment">
                                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button>
                                        </template>

                                        <!--<el-button type="danger" icon="el-icon-delete" plain @click.prevent="clickDelete(row.id)"></el-button>-->
                                    </td>
                                </template>
                                <template v-else>
                                    <td></td>
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.date_of_payment}">
                                            <el-date-picker v-model="row.date_of_payment"
                                                            type="date"
                                                            :clearable="false"
                                                            format="dd/MM/yyyy"
                                                            value-format="yyyy-MM-dd"></el-date-picker>
                                            <small class="form-control-feedback" v-if="row.errors.date_of_payment" v-text="row.errors.date_of_payment[0]"></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.payment_method_type_id}">
                                            <el-select v-model="row.payment_method_type_id">
                                                <el-option v-for="option in payment_method_types" v-show="option.id != '09'" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="row.errors.payment_method_type_id" v-text="row.errors.payment_method_type_id[0]"></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.payment_destination_id}">
                                            <el-select v-model="row.payment_destination_id" filterable :disabled="row.payment_destination_disabled">
                                                <el-option v-for="option in payment_destinations" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="row.errors.payment_destination_id" v-text="row.errors.payment_destination_id[0]"></small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.payment}">
                                            <el-input v-model="row.payment"></el-input>
                                            <small class="form-control-feedback" v-if="row.errors.payment" v-text="row.errors.payment[0]"></small>
                                        </div>
                                    </td>

                                    <!-- <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.reference}">
                                            <el-input v-model="row.reference"></el-input>
                                            <small class="form-control-feedback" v-if="row.errors.reference" v-text="row.errors.reference[0]"></small>
                                        </div>
                                    </td> -->
                                    <td class="row no-gutters px-0">

                                        <div class="col-md-7">
                                            <div class="row no-gutters">
                                                <div class="col-md-3">
                                                    <el-radio class="mb-3 pt-2" v-model="row.payment_received" label="1">SI</el-radio>
                                                    <el-radio v-model="row.payment_received" label="0">NO</el-radio>
                                                </div>
                                                <div class="col-md-9">
                                                    <el-upload
                                                        :data="{'index': index}"
                                                        :headers="headers"
                                                        :multiple="false"
                                                        :on-remove="handleRemove"
                                                        :action="`/finances/payment-file/upload`"
                                                        :show-file-list="true"
                                                        :file-list="fileList"
                                                        :on-success="onSuccess"
                                                        :limit="1"
                                                        :disabled="row.payment_received == '0'"
                                                        class="pb-1"
                                                        >

                                                        <template v-if="row.payment_received == '0'">
                                                            <el-button type="info" class="btn btn-sm">
                                                                <i class="fas fa-fw fa-upload"></i>
                                                                Cargar voucher
                                                            </el-button>
                                                        </template>
                                                        <template v-else>
                                                            <button  type="button" class="btn btn-sm btn-primary"  slot="trigger">
                                                                <i class="fas fa-fw fa-upload"></i>
                                                                Cargar voucher
                                                            </button>
                                                        </template>
                                                    </el-upload>
                                                    <template v-if="row.payment_received == '1'">
                                                        <el-button type="info" class="btn btn-sm">
                                                            <i class="fas fa-fw fa-link"></i>
                                                            Link de pago
                                                        </el-button>
                                                    </template>
                                                    <template v-else>
                                                        <button  type="button" class="btn btn-sm btn-primary" @click="showDialogLinkPayment(row)">
                                                            <i class="fas fa-fw fa-link"></i>
                                                            Link de pago
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group mb-0" :class="{'has-danger': row.errors.reference}">
                                                <el-input v-model="row.reference" placeholder="Referencia y/o N° Operación" :disabled="row.payment_received == '0'"></el-input>
                                                <small class="form-control-feedback" v-if="row.errors.reference" v-text="row.errors.reference[0]"></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="series-table-actions text-right px-0">
                                        <button type="button" class="btn waves-effect waves-light btn-sm btn-info" @click.prevent="clickSubmit(index)">
                                            <i class="fa fa-check d-block"></i>
                                        </button>

                                        <button type="button" class="btn waves-effect waves-light btn-sm btn-danger" @click.prevent="clickCancel(index)">
                                            <i class="fa fa-trash d-block"></i>
                                        </button>
                                    </td>
                                </template>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6" class="text-right">TOTAL PAGADO</td>
                                <td class="text-right">{{ document.total_paid }}</td>
                            </tr>
                            <tr v-if="document.credit_notes_total">
                                <td colspan="6" class="text-right">TOTAL NOTA CRÉDITO</td>
                                <td class="text-right">{{ document.credit_notes_total }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">TOTAL A PAGAR</td>
                                <td class="text-right">{{ document.total }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">PENDIENTE DE PAGO</td>
                                <td class="text-right">{{ document.total_difference }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-12 text-center pt-2" v-if="showAddButton && (document.total_difference > 0)">
                    <template v-if="permissions.create_payment">
                        <el-button type="primary" icon="el-icon-plus" @click="clickAddRow">Nuevo</el-button>
                    </template>
                </div>
            </div>
        </div>

        <dialog-link-payment
            :documentPaymentId="documentPayment.id"
            :currencyTypeId="document.currency_type_id"
            :exchangeRateSale="document.exchange_rate_sale"
            :payment="documentPayment.payment"
            :showDialog.sync="showDialogLink"
            :documentPayment="documentPayment"
            >
        </dialog-link-payment>

        <document-options
            :recordId="this.documentId"
            :showDialogOptions.sync="showDialogOptions"
            :showClose="showDialogClose"
            :type="this.type"
            :configuration="this.configuration"
        ></document-options>
    </el-dialog>

</template>

<script>

    import {deletable} from '../../../../mixins/deletable'
    import DialogLinkPayment from './dialog_link_payment'
    import DocumentOptions from '../../../../../../modules/Finance/Resources/assets/js/views/unpaid/partials/options'
    export default {
        props: ['showDialog', 'documentId', 'external','configuration'],
        mixins: [deletable],
        components: {
            DialogLinkPayment,
            DocumentOptions
        },
        data() {
            return {
                title: null,
                resource: 'document_payments',
                records: [],
                payment_destinations:  [],
                headers: headers_token,
                fileList: [],
                payment_method_types: [],
                showAddButton: true,
                document: {},
                permissions: {},
                index_file: null,
                documentPayment: {},
                showDialogLink: false,
                showDialogOptions: false,
                showDialogClose:false,
                type:'document',
            }
        },
        async created() {
            await this.initForm();
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.payment_method_types = response.data.payment_method_types;
                    this.payment_destinations = response.data.payment_destinations
                    this.permissions = response.data.permissions
                    //this.initDocumentTypes()
                })
            await this.events();

        },
        methods: {
            events(){
                this.$eventHub.$on('reloadDataPayments', ()=>{
                    this.getData()
                })
            },
            getObjectResponse(success, message = null){
                return {
                    success: success,
                    message: message,
                }
            },
            validateDataPayment(row){

                if(!row.payment_destination_id) return this.getObjectResponse(false, 'El campo destino es obligatorio.')

                if(!row.payment_method_type_id) return this.getObjectResponse(false, 'El campo método de pago es obligatorio.')

                if(!row.payment || row.payment <= 0 || isNaN(row.payment)) return this.getObjectResponse(false, 'El campo monto es obligatorio y debe ser mayor que 0.')

                return this.getObjectResponse(true)

            },
            showDialogLinkPayment(row){

                if(!row.id)
                {
                    const validate_data_payment = this.validateDataPayment(row)
                    if(!validate_data_payment.success) return this.$message.error(validate_data_payment.message)
                }

                this.showDialogLink = true
                this.documentPayment = row
                this.documentPayment.document_id = this.documentId

            },
            clickDownloadFile(filename) {
                window.open(
                    `/finances/payment-file/download-file/${filename}/documents`,
                    "_blank"
                );
            },
            onSuccess(response, file, fileList) {

                // console.log(response, file, fileList)
                this.fileList = fileList

                if (response.success) {

                    this.index_file = response.data.index
                    this.records[this.index_file].filename = response.data.filename
                    this.records[this.index_file].temp_path = response.data.temp_path

                } else {

                    this.cleanFileList()
                    this.$message.error(response.message)
                }

                // console.log(this.records)

            },
            cleanFileList(){
                this.fileList = []
            },
            handleRemove(file, fileList) {

                this.records[this.index_file].filename = null
                this.records[this.index_file].temp_path = null
                this.fileList = []
                this.index_file = null

            },
            initForm() {
                this.records = [];
                this.fileList = [];
                this.showAddButton = true;
            },
            async getData() {
                this.initForm();
                await this.$http.get(`/${this.resource}/document/${this.documentId}`)
                    .then(response => {
                        this.document = response.data;
                        this.title = 'Pagos del comprobante: '+this.document.number_full;
                    });
                await this.$http.get(`/${this.resource}/records/${this.documentId}`)
                    .then(response => {
                        this.records = response.data.data
                    });

                this.$eventHub.$emit('reloadDataUnpaid')

            },
            clickAddRow() {
                
                this.records.push({
                    id: null,
                    date_of_payment: moment().format('YYYY-MM-DD'),
                    payment_method_type_id: null,
                    payment_destination_id:null,
                    reference: null,
                    filename: null,
                    temp_path: null,
                    payment: parseFloat(this.document.total_difference),
                    // payment: 0,                    
                    errors: {},
                    loading: false,
                    payment_received: '1',
                });
                this.showAddButton = false;
            },
            clickCancel(index) {
                this.records.splice(index, 1);
                this.fileList = []
                this.showAddButton = true;
            },
            clickSubmit(index) {

                if(this.records[index].payment > parseFloat(this.document.total_difference)) {
                    this.$message.error('El monto ingresado supera al monto pendiente de pago, verifique.');
                    return;
                }

                let form = {
                    id: this.records[index].id,
                    document_id: this.documentId,
                    date_of_payment: this.records[index].date_of_payment,
                    payment_method_type_id: this.records[index].payment_method_type_id,
                    payment_destination_id: this.records[index].payment_destination_id,
                    reference: this.records[index].reference,
                    filename: this.records[index].filename,
                    temp_path: this.records[index].temp_path,
                    payment: this.records[index].payment,
                    payment_received: this.records[index].payment_received,
                };

                this.$http.post(`/${this.resource}`, form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message);
                            this.getData();
                            // this.initDocumentTypes()
                            this.showAddButton = true;
                            this.$eventHub.$emit('reloadData')
                        } else {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.records[index].errors = error.response.data;
                        } else {
                            console.log(error);
                            this.$message.error(error.response.data.message)
                        }
                    })
            },
            // filterDocumentType(row){
            //
            //     if(row.contingency){
            //         this.document_types = _.filter(this.all_document_types, item => (item.id == '01' || item.id =='03'))
            //         row.document_type_id = (this.document_types.length > 0)?this.document_types[0].id:null
            //     }else{
            //         row.document_type_id = null
            //         this.document_types = this.all_document_types
            //     }
            // },
            // initDocumentTypes(){
            //     this.document_types = (this.all_document_types.length > 0) ? this.all_document_types : []
            // },
            close() {
                this.$emit('update:showDialog', false);
                // this.initDocumentTypes()
                // this.initForm()
            },
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>{
                        this.getData()
                        this.$eventHub.$emit('reloadData')
                        // this.initDocumentTypes()
                    }
                )
            },
            clickDownloadReport(id)
            {
                window.open(`/${this.resource}/report/${this.documentId}`, '_blank');
            },
            clickPrint(external_id) {
                 window.open(`/finances/unpaid/print/${external_id}/document`, '_blank');
            },
            clickOptionsPrint() {
                this.showDialogOptions = true
                this.showDialogClose=true
            },
        }
    }
</script>
