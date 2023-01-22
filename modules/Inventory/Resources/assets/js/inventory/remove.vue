<template>
    <el-dialog :title="titleDialog"
               :visible="showDialog"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               append-to-body
               @close="close"
               @open="create">
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
                            <label class="control-label">Cantidad</label>
                            <el-input v-model="form.quantity"></el-input>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">Almacén Inicial</label>
                            <el-input v-model="form.warehouse_description" :readonly="true"></el-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.quantity_remove}">
                            <label class="control-label">Cantidad a retirar</label>
                            <el-input v-model="form.quantity_remove"></el-input>
                            <small class="form-control-feedback" v-if="errors.quantity_remove" v-text="errors.quantity_remove[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-4 mt-4" v-if="form.item_id && form.warehouse_id && form.series_enabled && form.quantity_remove > 0">
                        <!-- <el-button type="primary" native-type="submit" icon="el-icon-check">Elegir serie</el-button> -->
                        <a href="#" class="text-center font-weight-bold text-info" @click.prevent="clickLotcodeOutput">[&#10004;
                            Seleccionar series]</a>
                    </div>

                    <div class="col-md-4 mt-4" v-if="form.item_id && form.warehouse_id && form.lots_enabled">
                        <a href="#"  class="text-center font-weight-bold text-info" @click.prevent="clickSelectLotsGroup">[&#10004; Seleccionar lotes]</a>
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
            :quantity="form.quantity_remove"
            :warehouseId="form.warehouse_id"
            @addRowOutputLot="addRowOutputLot">
        </output-lots-form>

        <lots-group
            :showDialog.sync="showDialogLotsGroup"
            :itemId="form.item_id"
            :lots-group-all="lotsGroupAll"
            :lots_group="form.lots_group"
            :quantity="form.quantity_remove"
            @addRowLotGroup="addRowLotGroup"
            :compromise-all-quantity="true">
        </lots-group>


    </el-dialog>

</template>

<script>
import OutputLotsForm from '../../../../../../resources/js/views/tenant/documents/partials/lots.vue'
import LotsGroup from '../../../../../../resources/js/views/tenant/documents/partials/lots_group'
// import OutputLotsForm from './partials/lots.vue'
//import LotsGroup from '@views/documents/partials/lots_group.vue'

export default {
    components: {OutputLotsForm, LotsGroup},
    props: ['showDialog', 'recordId'],
    data() {
        return {
            loading_submit: false,
            showDialogLotsOutput: false,
            titleDialog: null,
            resource: 'inventory',
            errors: {},
            form: {},
            items: [],
            warehouses: [],
            lotsAll: [],
            lotsGroupAll: [],
            showDialogLotsGroup: false,
        }
    },
    async created() {
        this.initForm()
        await this.$http.get(`/${this.resource}/tables`)
            .then(response => {
                this.items = response.data.items
                this.warehouses = response.data.warehouses
            })
    },
    methods:
    {
        addRowLotGroup(id)
        {
            this.form.selected_lots_group = id
        },
        addRowOutputLot(lots) {
            this.form.lots = lots
        },
        clickLotcodeOutput() {
            this.showDialogLotsOutput = true
        },
        clickSelectLotsGroup()
        {
            if(!this.form.quantity_remove) return this.$message.error('Ingrese la cantidad a retirar.');

            if(isNaN(this.form.quantity_remove)) return this.$message.error('La cantidad a retirar no es un número válido.');

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
                quantity: 0,
                quantity_remove: 0,
                lots_enabled: false,
                lots: [],
                lots_group: [],
                selected_lots_group: [],
            }
        },
        async create() {
            this.titleDialog = 'Retirar producto de almacén 3'
            await this.$http.get(`/${this.resource}/record/${this.recordId}`)
                .then(response => {
                    let data = response.data.data;
                    this.form = _.clone(data);
                    this.form.lots = [];
                    this.form.lots_group = []; //Object.values(response.data.data.lots)
                    this.lotsAll = data.lots;
                    this.lotsGroupAll = data.lots_group;//Object.values(response.data.data.lots);
                    this.form = Object.assign({}, this.form, {'quantity_remove': 0});
                })
        },
        validetLotsGroup()
        {
            if (this.form.lots_enabled)
            {
                if (!this.form.selected_lots_group) return this.getObjectResponse(false, 'Debe seleccionar los lotes.')

                if(this.getTotalCompromiseQuantity() != parseFloat(this.form.quantity_remove)) return this.getObjectResponse(false, 'La cantidad a retirar es diferente del total comprometido.')
            }

            return this.getObjectResponse()
        },
        getObjectResponse(success = true, message = null)
        {
            return {
                success: success,
                message: message
            }
        },
        getTotalCompromiseQuantity()
        {
            return _.sumBy(this.form.selected_lots_group, 'compromise_quantity')
        },
        async submit() {
            if (this.form.series_enabled) {
                // let select_lots = await _.filter(this.form.lots, {'has_sale':true})
                if (this.form.lots.length !== parseInt(this.form.quantity_remove)) {
                    return this.$message.error('La cantidad ingresada es diferente a las series seleccionadas');
                }
            }

            const validet_lots_group = this.validetLotsGroup()
            if(!validet_lots_group.success) return this.$message.error(validet_lots_group.message)

            this.loading_submit = true
            await this.$http.post(`/${this.resource}/remove`, this.form)
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
