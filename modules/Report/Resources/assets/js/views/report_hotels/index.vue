<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Reporte habitaciones hoteles</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource">
                        <tr slot="heading">
                            <th>#</th>
                            <th >Nombres y Apellidos</th>
<!--                            <th>T. Documento</th>-->
                            <th> Habitación </th>
                            <th  >Status de pago</th>
                            <th  >Status checkout</th>
                            <th  >Fecha de entrada</th>
                            <th  >Hora de entrada</th>
                            <th  >Fecha de salida</th>
                            <th  >Hora de salida</th>
                           <!-- <th  >Edad</th>
                            <th  >E. civil</th>
                            <th  >Nacionalidad</th>
                            <th  >Procedencia</th>
                            <th>Comprobante</th>-->

                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td>{{row.customer.description}}</td>
<!--                            <td>{{row.identity_document_type_description}}</td>-->
                            <td>{{ row.room?row.room.name:''}}</td>
                            <td>{{ row.payment_status === "PAID" ? "Pagado" : "Debe" }}</td>
                            <td>{{row.status}}</td>
                            <td>{{row.input_date}}</td>
                            <td>{{row.input_time}}</td>
                            <td>{{row.output_date}}</td>
                            <td>{{row.output_time}}</td>

                        <!--

                        <td>{{row.age}}</td>
                        <td>{{row.civil_status}}</td>
                        <td >{{ row.nacionality }}</td>
                        <td >{{ row.origin }}</td>
                        <td >{{ row.document }}</td>
                        -->

                        </tr>

                    </data-table>


                </div>
        </div>

    </div>
</template>

<script>

    import DataTable from '../../components/DataTableDocumentHotel.vue'

    export default {
        components: {DataTable},
        data() {
            return {
                resource: 'reports/report_hotels',
                form: {},

            }
        },
        async created() {
        },
        methods: {


        }
    }
</script>
