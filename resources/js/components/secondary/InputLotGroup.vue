<template>
    <div>
        <el-dialog
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :show-close="false"
            :title="titleDialog"
            :visible="showDialog"
            @open="create"
        >
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label">
                            Código lote
                        </label>
                        <el-input v-model="form.lot_code"></el-input>
                        <small v-if="errors.lot_code" class="form-control-feedback" v-text="errors.lot_code[0]"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Fec. Vencimiento</label>
                        <el-date-picker v-model="form.date_of_due"
                                        type="date"
                                        value-format="yyyy-MM-dd"></el-date-picker>
                        <small v-if="errors.date_of_due" class="form-control-feedback" v-text="errors.date_of_due[0]"></small>
                    </div>
                </div>
            </div>


            <span slot="footer" class="dialog-footer">
                <el-button @click="clickClose">Cerrar</el-button>
                <el-button type="primary" @click.prevent="clickSubmit">Guardar
                </el-button>
            </span>

        </el-dialog>

    </div>
</template>

<script>

export default {

    props: [
        'showDialog',
        'rowItem',
        'rowIndex',
    ],
    computed:{
    },
    data() {
        return {
            titleDialog: null,
            errors: {},
            form: {},
            loading_submit: false,
        }
    },
    created()
    {
        this.initForm()
    },
    methods: {
        clickSubmit()
        {
            if(!this.form.lot_code) return  this.$message.error('El campo código de lote es obligatorio.')
            if(!this.form.date_of_due) return  this.$message.error('El campo fecha vencimiento es obligatorio.')

            this.$emit('saveInputLotGroup', {
                data: this.form,
                index: this.rowIndex
            })
            
            this.clickClose()
        },
        initForm() 
        {
            this.errors = {}

            this.form = {
                lot_code: null,
                date_of_due: null,
            }
        },
        async create() 
        {
            this.titleDialog =  'Ingresar lote'
            this.setData()
        },
        setData()
        {
            if(this.rowItem)
            {
                this.form.lot_code = this.rowItem.lot_code
                this.form.date_of_due = this.rowItem.date_of_due
            }
        },
        clickClose() 
        {
            this.$emit("update:showDialog", false)
            this.initForm()
        },
    },
}
</script>
