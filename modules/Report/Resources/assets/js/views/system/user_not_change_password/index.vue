<template>
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="my-0">Clientes - Usuarios con contraseña desactualizada</h3>
        </div>
        <div class="card-body">  
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dominio</th>
                        <th>Nombre</th>
                        <th>RUC</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in records" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.hostname }}</td>
                        <td>{{ row.name }}</td>
                        <td>{{ row.number }}</td>
                        <td>{{ row.email }}</td>
                        <td>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-primary"  @click.prevent="clickCreate(row.id)"> Visualizar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <record-form :showDialog.sync="showDialog"
                           :recordId="recordId"></record-form>
    </div>
</template>

<script>

    import RecordForm from './form.vue'

    export default {
        components: {RecordForm},
        data() {
            return {
                showDialog: false,
                resource: 'user-not-change-password',
                recordId: null,
                record: {},
                records: [],

            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getClients()
            })
            this.getClients()
        },
        methods: {
            getClients() {
                this.$http.get(`/reports/clients`)
                    .then(response => {
                        this.records = response.data.data
                    })
            },
            clickCreate(recordId) {
                this.recordId = recordId
                this.showDialog = true
            }
        }
    }
</script>
