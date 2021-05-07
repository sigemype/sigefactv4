<template>
    <el-dialog :title="title" :visible="showDialog" @close="close" @open="getData" width="45%">
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
                                <th>Fecha</th>
                                <th>Monto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in records" :key="index">
                                <template>
                                    <td>Cuota - {{ index+1 }}</td>
                                    <td>{{ row.date }}</td>
                                    <td>{{ row.amount }}</td>
                                </template>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </el-dialog>

</template>

<script>

    import {deletable} from '../../../../mixins/deletable'

    export default {
        props: ['showDialog', 'documentId'],
        mixins: [deletable],
        data() {
            return {
                title: null,
                resource: 'document_fee',
                records: [],
                payment_destinations:  [],
                headers: headers_token,
                fileList: [],
                payment_method_types: [],
                showAddButton: true,
                document: {},
                index_file: null,
            }
        },
        async created() {
            await this.initForm();
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.payment_method_types = response.data.payment_method_types;
                    this.payment_destinations = response.data.payment_destinations
                    //this.initDocumentTypes()
                })
        },
        methods: {
            // clickDownloadFile(filename) {
            //     window.open(
            //         `/finances/payment-file/download-file/${filename}/documents`,
            //         "_blank"
            //     );
            // },
            // onSuccess(response, file, fileList) {

            //     // console.log(response, file, fileList)
            //     this.fileList = fileList

            //     if (response.success) {

            //         this.index_file = response.data.index
            //         this.records[this.index_file].filename = response.data.filename
            //         this.records[this.index_file].temp_path = response.data.temp_path

            //     } else {

            //         this.cleanFileList()
            //         this.$message.error(response.message)
            //     }

            //     // console.log(this.records)
            
            // },
            cleanFileList(){
                this.fileList = []
            },
            // handleRemove(file, fileList) {       
                
            //     this.records[this.index_file].filename = null
            //     this.records[this.index_file].temp_path = null
            //     this.fileList = []
            //     this.index_file = null

            // }, 
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
                        this.title = 'Cuotas del comprobante: '+this.document.number_full;
                    });
                await this.$http.get(`/${this.resource}/records/${this.documentId}`)
                    .then(response => {
                        this.records = response.data.data
                    });

                this.$eventHub.$emit('reloadDataUnpaid')

            },
            // clickAddRow() {
            //     this.records.push({
            //         id: null,
            //         date_of_payment: moment().format('YYYY-MM-DD'),
            //         payment_method_type_id: null,
            //         payment_destination_id:null,
            //         reference: null,
            //         filename: null,
            //         temp_path: null,
            //         payment: 0,
            //         errors: {},
            //         loading: false
            //     });
            //     this.showAddButton = false;
            // },
            clickCancel(index) {
                this.records.splice(index, 1);
                this.fileList = []
                this.showAddButton = true;
            },
            // clickSubmit(index) {
            //     if(this.records[index].payment > parseFloat(this.document.total_difference)) {
            //         this.$message.error('El monto ingresado supera al monto pendiente de pago, verifique.');
            //         return;
            //     }
            //     let form = {
            //         id: this.records[index].id,
            //         document_id: this.documentId,
            //         date_of_payment: this.records[index].date_of_payment,
            //         payment_method_type_id: this.records[index].payment_method_type_id,
            //         payment_destination_id: this.records[index].payment_destination_id,
            //         reference: this.records[index].reference,
            //         filename: this.records[index].filename,
            //         temp_path: this.records[index].temp_path,
            //         payment: this.records[index].payment,
            //     };
            //     this.$http.post(`/${this.resource}`, form)
            //         .then(response => {
            //             if (response.data.success) {
            //                 this.$message.success(response.data.message);
            //                 this.getData();
            //                 // this.initDocumentTypes()
            //                 this.showAddButton = true;
            //                 this.$eventHub.$emit('reloadData')
            //             } else {
            //                 this.$message.error(response.data.message);
            //             }
            //         })
            //         .catch(error => {
            //             if (error.response.status === 422) {
            //                 this.records[index].errors = error.response.data;
            //             } else {
            //                 console.log(error);
            //             }
            //         })
            // },
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
            // clickDownloadReport(id)
            // {
            //     window.open(`/${this.resource}/report/${this.documentId}`, '_blank');
            // }
        }
    }
</script>
