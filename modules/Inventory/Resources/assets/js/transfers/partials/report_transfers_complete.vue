<template>
    <el-dialog :title="title" :visible="showDialog" @close="closeDialog">
        <div class="">
            <div class="row mt-2">
                <!-- <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Fecha inicio </label>
                        <el-date-picker v-model="form.date_start" type="date" style="width: 100%;" placeholder="Buscar" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Fecha término</label>
                        <el-date-picker v-model="form.date_end" type="date" style="width: 100%;" placeholder="Buscar" value-format="yyyy-MM-dd" :picker-options="pickerOptionsDates" :clearable="false"></el-date-picker>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 pb-4">
                    <div class="form-group">
                        <label class="control-label">Tipo</label>
                        <el-select v-model="form.type">
                            <el-option value="1" label="PDF"></el-option>
                            <el-option value="2" label="EXCEL"></el-option>
                        </el-select>
                    </div>
                </div> -->
            
                <div class="col-md-4">
                    <label class="control-label">Periodo</label>
                    <el-select v-model="form.period" @change="changePeriod">
                        <el-option key="day" value="day" label="Por día"></el-option>
                        <el-option key="between_days" value="between_days" label="Entre días"></el-option>
                        <el-option key="month" value="month" label="Por mes"></el-option>
                    </el-select>
                </div>
                <template v-if="form.period === 'day' || form.period === 'between_days'">
                    <div class="col-md-4">
                        <label class="control-label">Día</label>
                        <el-date-picker v-model="form.date_start" type="date" @change="pickerOptionsDates" value-format="yyyy-MM-dd" format="dd/MM/yyyy" :clearable="false"></el-date-picker>
                    </div>
                </template>
                <template v-if="form.period === 'month'">
                    <div class="col-md-4">
                        <label class="control-label">Mes de</label>
                        <el-date-picker v-model="form.month" type="month" @change="pickerOptionsDates" value-format="yyyy-MM" format="MM/yyyy" :clearable="false"></el-date-picker>
                    </div>
                </template>
                <template v-if="form.period === 'between_days'">
                    <div class="col-md-4">
                        <label class="control-label">al día</label>
                        <el-date-picker v-model="form.date_end" type="date" style="width: 100%;" placeholder="Buscar" value-format="yyyy-MM-dd" format="dd/MM/yyyy" :picker-options="pickerOptionsDates" :clearable="false"></el-date-picker>
                    </div>
                </template>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button type="warning" @click="closeDialog">Cancelar</el-button>
            <el-button type="primary" @click="downloadReportComplete('excel')">Descargar</el-button>
        </span>
    </el-dialog>
</template>

<script>
    import moment from 'moment'
    import queryString from 'query-string'
    export default {
        props: ["showDialog", "documentId"],
        data() {
            return {
                title: "Reporte de Transferencias",
                resource: "transfers",
                form: {},
                pickerOptionsDates: {
                    disabledDate: (time) => {
                        time = moment(time).format('YYYY-MM-DD')
                        return this.form.date_start > time
                    }
                },
            };
        },
        created() { 
            this.initForm()
        },
        methods: {
            changeDisabledDates() {
                if (this.form.date_end < this.form.date_start) {
                    this.form.date_end = this.form.date_start
                }
            },
            initForm(){
                this.form = {
                    date_start: moment().format('YYYY-MM-DD'),
                    date_end: moment().format('YYYY-MM-DD'),
                    month: moment().format('YYYY-MM'),
                    period: "day"
                }
            },
            closeDialog() {
                this.initForm()
                this.$emit("update:showDialog", false);
            },
            downloadReportComplete(type){
                window.open(`/${this.resource}/download?${this.getQueryParameters()}`, '_blank');
            },
            getQueryParameters() { 
                return queryString.stringify({
                    ...this.form
                })
            },
        }
    };
</script>
