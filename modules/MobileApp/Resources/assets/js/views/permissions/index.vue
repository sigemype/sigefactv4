<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4>Gestionar permisos</h4>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Nombre</th>
                            <th>Perfil</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.email }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.type }}</td>
                            <td class="text-center">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-primary" @click.prevent="clickShowPermissions(row.id)">
                                    <i class="fas fa-user-lock"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <permission-form :showDialog.sync="showDialog"
                        :typeUser="typeUser"
                        :recordId="recordId"></permission-form>
        </div>
    </div>
</template>

<script>

    import PermissionForm from './partials/form.vue'

    export default {
        props: ['typeUser'],
        components: {PermissionForm},
        data() {
            return {
                showDialog: false,
                resource: 'users',
                recordId: null,
                records: [],
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        methods: {
            getData() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data.data
                    })
            },
            clickShowPermissions(recordId) {

                if(recordId != 1)
                {
                    this.recordId = recordId
                    this.showDialog = true
                }
                else
                {
                    this.$message.warning('El usuario principal tiene todos los permisos asignados, no puede modificarlos.')
                }

            },
        }
    }
</script>
