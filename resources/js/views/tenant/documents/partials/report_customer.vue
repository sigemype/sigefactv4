<template>
  <el-dialog
    title="Consulta de documentos"
    :visible.sync="showDialog"
    @close="close"
    @open="create"
    width="80%"
    :show-close="false">
    <data-table :resource="'reports/customers'" :customerId="customerId">
        <tr slot="heading">
            <th class="">#</th>
            <th class="">Fecha</th>
            <th class="">Tipo Documento</th>
            <th class="">Moneda</th>
            <th class="">Número de Placa</th>
            <th class="">Serie</th>
            <th class="">Número</th>
            <th class="">Monto</th>
        <tr>
        <tr slot-scope="{ index, row }">
            <td>{{ index }}</td>
            <td>{{row.date_of_issue}}</td>
            <td>{{row.document_type_description}}</td>
            <td>{{row.currency_type_id}}</td>
            <td>{{row.plate_number}}</td>
            <td>{{row.series}}</td>
            <td>{{row.alone_number}}</td>
            <td>{{ (row.document_type_id == '07') ? ( (row.total == 0) ? '0.00': '-'+row.total) : ((row.document_type_id!='07' && (row.state_type_id =='11'||row.state_type_id =='09')) ? '0.00':row.total) }}</td>
        </tr>

    </data-table>
    <span slot="footer" class="dialog-footer">
      <el-button @click.prevent="close()">Close</el-button>
    </span>
  </el-dialog>
</template>

<script>
  import DataTable from '../../../../../../modules/Report/Resources/assets/js/components/DataTableCustomers.vue'

  export default {
    props: [
      'showDialog',
      'customerId'
    ],
    components: {
      DataTable
    },
    data() {
      return {
        customer_id: 0,
      };
    },
    methods: {
      create(){
        // if(this.customerId) {
        //   this.customer_id = this.customerId
        // }
      },
      close() {
          this.$emit('reloadData')
          this.$emit('update:showDialog', false)
      },
    }
  };
</script>