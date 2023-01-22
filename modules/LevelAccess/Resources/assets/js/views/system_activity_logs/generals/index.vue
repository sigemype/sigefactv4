<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-list"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Transacciones generales</span></li>
            </ol>
            <div class="right-wrapper pull-right">
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Transacciones generales</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Transacción</th>
                        <th class="text-center">Ip</th>
                        <th class="text-center">Ubicación</th>
                        <th>Dispositivo</th>
                        <th>Tipo dispositivo</th>
                        <th>Plataforma</th>
                        <th>Navegador</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_time }}</td>
                        <td>{{ row.user_name }}</td>
                        <td class="text-justify">{{ row.system_activity_log_type_description }}</td>
                        <td class="text-center">{{ row.ip }}</td>
                        
                        <td class="text-center">

                            <template v-if="row.location">
                                <el-popover
                                    placement="right"
                                    width="300"
                                    trigger="click">
                                    <div>
                                        <p><b>País: </b> {{ row.location.country_code }} - {{ row.location.country_name }}</p>
                                        <p><b>Región: </b> {{ row.location.region_code }} - {{ row.location.region_name }}</p>
                                        <p><b>Ciudad: </b> {{ row.location.city_name }}</p>
                                        <p><b>Zona horaria: </b> {{ row.location.timezone }}</p>
                                    </div>
                                    <el-button slot="reference"> <i class="fa fa-eye"></i></el-button>
                                </el-popover>
                            </template>

                        </td>

                        <td>{{ row.device_name }}</td>
                        <td>{{ row.device_type_description }}</td>
                        <td>{{ row.platform_description }}</td>
                        <td>{{ row.browser_description }}</td>
                    </tr>
                </data-table>
            </div>

        </div>
    </div>
</template>

<script>

    import DataTable from '../../../components/DataTable.vue'

    export default {
        components: { DataTable },
        data() {
            return {
                showDialog: false,
                showDialogOptions: false,
                open_cash: true,
                resource: 'system-activity-logs/generals',
                recordId: null,
                cash:null,
            }
        },
        async created() {

        },
        methods: {
        }
    }
</script>
