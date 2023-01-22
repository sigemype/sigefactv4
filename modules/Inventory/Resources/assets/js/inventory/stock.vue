<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">Producto</label>
                            <el-input v-model="form.item_description" :readonly="true"></el-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Stock en el sistema</label>
                            <el-input v-model="form.quantity" :readonly="true"></el-input>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">Almacén</label>
                            <el-input v-model="form.warehouse_description" :readonly="true"></el-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Stock real</label>
                            <el-input v-model="form.quantity_real"></el-input>
                        </div>
                    </div>

                    <div class="col-md-4 mt-4" v-if="form.item_id && form.warehouse_id && form.series_enabled">
                        <!-- <el-button type="primary" native-type="submit" icon="el-icon-check">Elegir serie</el-button> -->
                        <a href="#" class="text-center font-weight-bold text-info" @click.prevent="clickLotcodeOutput">[&#10004;
                            Seleccionar series]</a>
                    </div>

                    <div class="col-md-4 mt-4" v-if="form.item_id && form.warehouse_id && form.lots_enabled">
                        <a href="#" class="text-center font-weight-bold text-info"
                           @click.prevent="clickSelectLotsGroup">[&#10004; Seleccionar lotes]</a>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Aceptar</el-button>
            </div>
        </form>
        <output-lots-form
            :showDialog.sync="showDialogLotsOutput"
            :itemId="form.item_id"
            :lots-all="lotsAll"
            :lots="form.lots"
            :quantity="form.quantity_real"
            :warehouseId="form.warehouse_id"
            @addRowOutputLot="addRowOutputLot">
        </output-lots-form>

        <output-lots-group-form
            :showDialog.sync="showDialogLotsGroup"
            :itemId="form.item_id"
            :lots-group-all="lotsGroupAll"
            :lots_group="form.lots_group"
            :quantity="form.quantity_real"
            @addRowLotGroup="addRowLotGroup"
            :compromise-all-quantity="true">
        </output-lots-group-form>

    </el-dialog>

</template>

<script>

import OutputLotsForm from '../../../../../../resources/js/views/tenant/documents/partials/lots.vue'
import OutputLotsGroupForm from '../../../../../../resources/js/views/tenant/documents/partials/lots_group'
//import OutputLotsForm from './partials/lots.vue'

export default {
    components: {OutputLotsForm, OutputLotsGroupForm},
    props: ['showDialog', 'recordId'],
    data() {
        return {
            loading_submit: false,
            titleDialog: null,
            showDialogLotsOutput: false,
            showDialogLotsGroup: false,
            resource: 'inventory',
            errors: {},
            form: {},
            warehouses: [],
            lotsAll: [],
            lotsGroupAll: [],
        }
    },
    async created() {
        this.initForm()
        await this.$http.get(`/${this.resource}/tables`)
            .then(response => {
                this.warehouses = response.data.warehouses
            })
    },
    methods: {
        addRowOutputLot(lots) {
            this.form.lots = lots
        },
        addRowLotGroup(id) {
            this.form.selected_lots_group = id
        },
        clickLotcodeOutput() {
            this.showDialogLotsOutput = true
        },
        clickSelectLotsGroup() {
            this.showDialogLotsGroup = true
        },
        initForm() {
            this.errors = {}
            this.form = {
                id: null,
                item_id: null,
                item_description: null,
                warehouse_id: null,
                warehouse_description: null,
                quantity: null,
                quantity_real: null,
                lots_enabled: false,
                series_enabled: false,
                lots: [],
            }
        },
        async create() {
            this.titleDialog = 'Ajuste de stock 3'
            await this.$http.get(`/${this.resource}/record/${this.recordId}`)
                .then(response => {
                    let data = response.data.data;
                    this.form = _.clone(data);
                    this.form.lots = [];
                    this.form.lots_group = []; //Object.values(response.data.data.lots)
                    this.lotsAll = data.lots;
                    this.lotsGroupAll = data.lots_group; //Object.values(response.data.data.lots);
                    this.form = Object.assign({}, this.form, {'quantity_real': 0});
                })
        },
        async submit() {
            if (this.form.series_enabled) {
                //let select_lots = await _.filter(this.form.lots, {'has_sale': true})
                if (this.form.lots.length !== parseInt(this.form.quantity_real)) {
                    return this.$message.error('La cantidad ingresada es diferente a las series seleccionadas');
                }
            }

            this.loading_submit = true
            await this.$http.post(`/${this.resource}/stock`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.$eventHub.$emit('reloadData')
                        this.close()
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors
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
