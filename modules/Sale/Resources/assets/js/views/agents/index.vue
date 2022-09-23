<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Agentes</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Agentes</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading" width="100%">
                        <th>#</th>
                        <th>Código</th> 
                        <th>Nombre</th> 
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.internal_id }}</td>
                        <td>{{ row.name }}</td> 
                        <td>{{ row.email }}</td>
                        <td>{{ row.telephone }}</td>
                        <td class="text-right">
                            <template v-if="typeUser === 'admin'">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button>
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button>
                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>

            <agents-form :showDialog.sync="showDialog"
                        :recordId="recordId"></agents-form>
 
        </div>
    </div>
</template>
<script>

    import AgentsForm from './form.vue'
    import DataTable from '@components/DataTable.vue'
    import {deletable} from '@mixins/deletable'

    export default {
        mixins: [deletable],
        props:['typeUser'],
        components: {AgentsForm,  DataTable},
        data() {
            return {
                showDialog: false,
                showImportDialog: false,
                showWarehousesDetail: false,
                resource: 'agents',
                recordId: null,
            }
        },
        created() {
        },
        methods: { 
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            }, 
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>
