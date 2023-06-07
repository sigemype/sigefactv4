<template>
    <el-dialog :visible="showDialog" @close="close" @open="getData" width="65%" :close-on-click-modal="false" :close-on-press-escape="false">
        <span slot="title" class="d-flex justify-content-between h5 p-3"> {{title}}
            <el-button type="primary" icon="el-icon-plus" @click="clickAddRow" v-if="showAddButton">Agregar Despacho</el-button>
        </span>
        <!-- <div class="col-md-12 text-center pt-2" v-if="showAddButton && (document.total_difference > 0)">
            
        </div> -->
        <div class="form-body">
            <div class="row">
                <div class="col-md-12" v-if="records.length > 0">
                    <div class="table-responsive table-sm">
                        <table class="table">
                            <thead>
                            <tr>
                                <!-- <th></th> -->
                                <th>#</th>
                                <th>Tipo de entrega</th>
                                <th>Fecha de despacho</th>
                                <th>Hora de despacho</th>
                                <th>Persona quien recogio</th>
                                <th>Referencia</th>
                                <th>Personal que despacho</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in records" :key="index">
                                <template v-if="row.id">
                                    <!-- <td>
                                        <el-switch
                                            v-model="row.selected"
                                            @change="onFillSelectedDispatch"
                                        ></el-switch>
                                    </td> -->
                                    <td>DESPACHO-{{ row.id }}</td>
                                    <td>{{ row.type==null?'-':(row.type?'Entregado':'Parcial') }}</td>
                                    <td>{{ row.date_dispatch }}</td>
                                    <td>{{ row.time_dispatch }}</td>
                                    <td>{{ row.person_pick }}</td>
                                    <td>{{ row.reference }}</td>
                                    <td class="text-right">{{ row.person_dispatch }}</td>
                                    <td class="series-table-actions text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)" v-if="typeUser === 'admin'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <!--<el-button type="danger" icon="el-icon-delete" plain @click.prevent="clickDelete(row.id)"></el-button>-->
                                    </td>
                                </template>
                                <template v-else>
                                    <!-- <td></td> -->
                                    <td></td>
                                    <td>
                                        <el-select v-model="row.status">
                                            <el-option
                                                v-for="option in options_status"
                                                :key="option.value"
                                                :value="option.value"
                                                :label="option.description"
                                                >
                                            </el-option>
                                        </el-select>
                                    </td>
                                    <td>{{ row.date_dispatch }}</td>
                                    <td>{{ row.time_dispatch }}</td>
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.person_dispatch}">
                                            <el-input v-model="row.person_pick"></el-input>
                                            <small class="form-control-feedback" v-if="row.errors.person_dispatch" v-text="row.errors.person_dispatch[0]"></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.reference}">
                                            <el-input v-model="row.reference"></el-input>
                                            <small class="form-control-feedback" v-if="row.errors.reference" v-text="row.errors.reference[0]"></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group mb-0" :class="{'has-danger': row.errors.reference}">
                                            <el-input v-model="row.person_dispatch"></el-input>
                                            <small class="form-control-feedback" v-if="row.errors.reference" v-text="row.errors.reference[0]"></small>
                                        </div>
                                    </td>
                                    <td class="series-table-actions text-right">
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickSubmit(index)">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        
                                        <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </template>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- <div class="col-md-12 pt-2">
                    <div class="d-flex">
                        <div class="d-flex">
                            <div class="d-flex flex-column">
                                <el-radio v-model="status_display" @change="statusUpdate" :checked="checked_display" label="1">Entregado</el-radio>
                                <el-radio v-model="status_display" @change="statusUpdate" :checked="checked_display" label="0">Parcial</el-radio>
                            </div>
                            <button v-if="typeUser != 'seller'" type="button" class="btn waves-effect waves-light btn-xs btn-light" @click.prevent="statusUpdate('initial')">Borrar Check</button>
                        </div>
                        <div class="w-100 text-center">
                        </div>
                    </div>
                    
                </div> -->
                
            </div>
        </div>
    </el-dialog>

</template>

<style>
.el-upload-list__item-name [class^="el-icon"] {
    display: none;
}
.el-upload-list__item-name {
    margin-right: 25px;
}
.el-upload-list__item {
    font-size: 10px;
}
</style>

<script>

    import {deletable} from '../../../../mixins/deletable'
    import moment from 'moment'

    export default {
        props: ['showDialog', 'documentId', 'typeUser', 'statusDispatch'],
        mixins: [deletable],
        data() {
            return {
                title: null,
                resource: 'sale-notes',
                records: [],
                showAddButton: true,
                document: {},
                selecteds:[],
                dispatch_active:false,
                options_status:[
                    {
                        description: 'PARCIAL',
                        value: '0', //segun estructura del campo
                        label: '0'
                    }, {
                        description: 'ENTREGADO',
                        value: '1',
                        label: '1'
                    }
                ]
            }
        },
        async created()
        {
            await this.initForm()
        },
        methods: {
            initForm() {
                this.records = []
                this.showAddButton = true
            },
            async getData() 
            {
                this.initForm()
                // search document
                
                /*
                await this.$http.get(`/sale_note_payments/document/${this.documentId}`)
                    .then(response => {
                        this.document = response.data;
                        this.title = 'Estado de despacho del comprobante: '+this.document.number_full;
                    });

                */
               
                // dispatch sale notes
                await this.$http.get(`/${this.resource}/dispatch/${this.documentId}`) 
                    .then(response => {

                        this.document = response.data
                        this.records = response.data.records

                        this.title = 'Estado de despacho del comprobante: '+this.document.number_full;
                    })

                /*
                this.$eventHub.$emit('reloadDataUnpaid')
                */

            },
            // async getDataNote(id) {
            //     //this.initForm();
            //     // dispatch sale notes
            //     await this.$http.get(`/${this.resource}/dispatch_note/${id}`) 
            //         .then(response => {
            //             this.checked_display = response.data.data[0]['status']
            //             if(this.checked_display!=null){
            //                 this.status_display = response.data.data[0]['status']? '1':'0'
            //             }else{
            //                 this.status_display = null
            //             }
                        
            //         });
            //     this.$eventHub.$emit('reloadDataUnpaid')

            // },
            clickAddRow() 
            {
                if(this.document.status_dispatch === 'ENTREGADO')
                {
                    return this.$message.error('No puede agregar despachos, el documento tiene estado ENTREGADO');
                }

                this.records.push({
                    id: null,
                    date_dispatch: moment().format("YYYY/MM/DD"),
                    time_dispatch: new Date().toLocaleTimeString(),
                    person_pick:null,
                    person_dispatch: null,
                    reference: null,
                    errors: {},
                    loading: false,
                    status: '0'
                })

                this.showAddButton = false

            },
            clickCancel(index) {
                this.records.splice(index, 1);
                this.showAddButton = true;
            },
            clickSubmit(index) {

                let form = {
                    id: this.records[index].id,
                    sale_note_id: this.documentId,
                    date_dispatch: this.records[index].date_dispatch,
                    time_dispatch: this.records[index].time_dispatch,
                    person_pick:this.records[index].person_pick,
                    person_dispatch: this.records[index].person_dispatch,
                    reference: this.records[index].reference,
                    status: this.records[index].status,
                };
                
                this.$http.post(`/${this.resource}/dispatch`, form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message);
                            this.dispatch_active=true
                            this.getData();
                            this.$eventHub.$emit('reloadData')
                            this.showAddButton = true;
                        } else {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.records[index].errors = error.response.data;
                        } else {
                            console.log(error);
                        }
                    })
            },
            close() {
                this.$emit('update:showDialog', false);
            },
            clickDelete(id) {
                this.destroy(`/${this.resource}/dispatch/delete/${id}`).then(() =>{
                        this.getData()
                        this.$eventHub.$emit('reloadData')
                    }
                )
            },
            // statusUpdate(value=null){
            //     if( this.selecteds.length==0){
            //         this.status_display=null
            //         if(!this.dispatch_active){
            //             this.status_display=null
            //             return this.$message.error('Debe crear un pedido');
            //         }
            //         return this.$message.error('Debe seleccionar un pedido');
            //     }
                
            //     let form = {
            //         sale_note_id: this.documentId,
            //         status_display:this.status_display==0?false:true,
            //         dispatch_id:this.selecteds[0],
            //     };
            //     console.log(this.selecteds[0])
            //     if (value==='initial') {
            //         form.status_display=null
            //         this.status_display=null
            //     }
            //     this.$http.post(`/${this.resource}/dispatch/statusUpdate`, form)
            //         .then(response => {
            //             if (response.data.success) {
            //                 this.$message.success(response.data.message);
            //                 //this.getData();
            //                 // this.initDocumentTypes()
            //                     this.$eventHub.$emit('reloadData')
                            
            //                 this.showAddButton = true;
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
            // onFillSelectedDispatch() {
            //     this.selecteds = [];
                
            //     console.log(this.records)
                
            //     this.records.map((d) => {
            //         if (d.selected) {
            //                 this.selecteds.push(d.id);
            //                 this.getDataNote(d.id)
                        
            //         }
            //         if(this.selecteds.length==2){
            //                 d.selected=false
            //                 return this.$message.error('Solo se puede seleccionar un pedido');
            //         }
            //     });
            //     console.log(this.selecteds.length)
            // },
        },
    }
</script>
