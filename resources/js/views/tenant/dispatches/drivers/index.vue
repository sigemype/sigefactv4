<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i
                    class="fa fa-plus-circle"></i> Nuevo
                </button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Nombre</th>
                        <th class="text-left">Tipo de documento</th>
                        <th class="text-left">Número</th>
                        <th class="text-left">Licencia</th>
                        <th class="text-center">Predeterminado</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.name }}</td>
                        <td class="text-left">{{ row.document_type }}</td>
                        <td class="text-left">{{ row.number }}</td>
                        <td class="text-left">{{ row.license }}</td>
                        <td class="text-center">{{ row.is_default }}</td>
                        <td class="text-right">

                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickCreate(row.id)">Editar
                            </button>

                            <template v-if="typeUser === 'admin'">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                        @click.prevent="clickDelete(row.id)">Eliminar
                                </button>
                            </template>

                        </td>
                    </tr>
                </data-table>
            </div>

            <drivers-form :showDialog.sync="showDialog"
                          :recordId="recordId"
                          @success="successCreate"></drivers-form>


        </div>
    </div>
</template>

<script>

import DriversForm from './form.vue'
import DataTable from '@components/DataTable.vue'
import {deletable} from '@mixins/deletable'

export default {
    name: 'DispatchDriverIndex',
    mixins: [deletable],
    props: ['typeUser'],
    components: {DriversForm, DataTable},
    data() {
        return {
            title: null,
            showDialog: false,
            resource: 'drivers',
            recordId: null,
        }
    },
    created() {
        this.title = 'Conductores'
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
        },
        successCreate() {
            this.$eventHub.$emit('reloadData')
        }
    }
}
</script>
