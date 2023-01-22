<template>
    <div>
        <div class="col-lg-12 col-md-12 mt-1">
            <label>
                <strong>
                    Registrar propina
                    <el-tooltip class="item"
                                content="Para registrar la propina debe ingresar los datos del empleado y el monto debe ser mayor a 0"
                                effect="dark"
                                placement="top">
                        <i class="fa fa-info-circle"></i>
                    </el-tooltip>
                </strong>
            </label>
        </div>

        <div :class="class_worker_full_name">
            <div class="form-group">
                <label class="control-label">Empleado</label>
                <el-input v-model="tip.worker_full_name_tips" @change="changeWorkerFullNameTips"></el-input>
            </div>
        </div>

        <div :class="class_total_tips">
            <div class="form-group">
                <label class="control-label">Monto</label>
                <el-input-number v-model="tip.total_tips" :min="0" controls-position="right" @change="changeTotalTips"></el-input-number>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            class_worker_full_name: {
                type: String,
                required: false,
                default: 'col-lg-12 col-md-12'
            },
            class_total_tips: {
                type: String,
                required: false,
                default: 'col-lg-12 col-md-12'
            },
        },
        data() {
            return {
                tip: {},
            }
        },
        created() 
        {
            this.initTip()
            this.onEvents()
        },
        methods: {
            onEvents()
            {
                this.$eventHub.$on('eventInitTip', () => this.initTip())

            },
            changeWorkerFullNameTips()
            {
                this.changeDataTip()
            },
            changeTotalTips()
            {
                this.changeDataTip()
            },
            initTip()
            {
                this.tip = {
                    worker_full_name_tips: null,
                    total_tips: 0,
                }
            },
            changeDataTip() 
            {
                this.$emit('changeDataTip', this.tip)
            },
        }
    }
</script>