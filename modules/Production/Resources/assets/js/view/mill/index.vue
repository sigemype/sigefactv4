<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Ingreso de insumo</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right pt-2">
                <!--
                <el-button class="submit"
                           type="success"
                           @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel"></i> Exportar Excel
                </el-button>
                -->
                <a :href="`/${resource}/create`"
                   class="btn btn-custom btn-sm "><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-body">
                <div class="col-12">

                    <el-button class="submit" type="danger"  icon="el-icon-tickets" @click.prevent="clickDownloadPdf()" >Exportar PDF</el-button>

                    <el-button class="submit" type="success" @click.prevent="clickDownloadExcel()"><i class="fa fa-file-excel" ></i>  Exportal Excel</el-button>
                </div>
                <div class="col-12 table-responsive p-t-20">
                    <data-table :resource="resource">
                    <tr slot="heading">

                        <th>#</th>
                        <th class="text-center">Número de registro</th>
                        <th>Fecha de inicio</th>
                        <th>Hora de inicio</th>
                        <th>Fecha de fin</th>
                        <th>Hora de fin</th>
                        <th>Usuario</th>
                        <th>Comentario</th>
<!--                        <th class="text-right">Acciones</th>-->
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>

                        <td>{{ row.name }}</td>
                        <td>{{ row.date_start }}</td>
                        <td>{{ row.time_start }}</td>
                        <td>{{ row.date_end }}</td>
                        <td>{{ row.time_end }}</td>
                        <td>{{ row.user }}</td>
                        <td>{{ row.comment }}</td>
                    <!--
<td class="text-right">
 <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Ver</button>

 <template v-if="typeUser === 'admin'">
     <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"  @click.prevent="clickDelete(row.id)">Eliminar</button>
 </template>
                    </td> -->

                </tr>
                </data-table>
                </div>
            </div>

            <!--
            <document-payments :showDialog.sync="showDialogPayments"
                               :expenseId="recordId"></document-payments>
            <expense-voided :showDialog.sync="showDialogVoided"
                            :expenseId="recordId"></expense-voided>

            <expense-payments
                :showDialog.sync="showDialogExpensePayments"
                :expenseId="recordId"
                :external="true"
            ></expense-payments>
            -->
        </div>
    </div>

</template>

<script>

import DataTable from '@components/DataTable.vue'
// import DocumentPayments from './partials/payments.vue'
// import ExpenseVoided from './partials/voided.vue'
// import ExpensePayments from '@viewsModuleExpense/expense_payments/payments.vue'
import queryString from 'query-string'

export default {
    components: {
        // DocumentPayments,
        // ExpenseVoided,
        // ExpensePayments,
        DataTable

    },
    data() {
        return {
            showDialogVoided: false,
            resource: 'mill-production',
            showDialogPayments: false,
            showDialogExpensePayments: false,
            recordId: null,
            showDialogOptions: false
        }
    },
    created() {
    },
    methods: {
        clickCreate(id = '') {
            location.href = `/${this.resource}/create/${id}`
        },
        clickExpensePayment(recordId) {
            this.recordId = recordId;
            this.showDialogExpensePayments = true
        },
        clickVoided(recordId) {
            this.recordId = recordId;
            this.showDialogVoided = true;
        },
        clickDownload(download) {
            let data = this.$root.$refs.DataTable.getSearch();
            let query = queryString.stringify({
                'column': data.column,
                'value': data.value
            });

            window.open(`/${this.resource}/report/excel/?${query}`, '_blank');
        },
        clickOptions(recordId = null) {
            this.recordId = recordId
            this.showDialogOptions = true
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },


        clickDownloadPdf() {
            window.open(`${this.resource}/pdf`, '_blank');
        },
        clickDownloadExcel() {
            window.open(`${this.resource}/excel`, '_blank');
        },
    }
}
</script>
