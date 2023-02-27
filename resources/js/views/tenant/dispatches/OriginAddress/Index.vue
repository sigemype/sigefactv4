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
                        <th class="text-left">Dirección</th>
                        <th class="text-left">Ubigeo</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td class="text-left">{{ row.address }}</td>
                        <td class="text-left">{{ row.location_name }}</td>
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

            <origin-address-form :showDialog.sync="showDialog"
                                 :recordId="recordId"
                                 @success="successCreate"></origin-address-form>
        </div>
    </div>
</template>

<script>

import OriginAddressForm from './Form'
import DataTable from '@components/DataTable.vue'
import {deletable} from '@mixins/deletable'

export default {
    name: 'DispatchOriginAddressIndex',
    mixins: [deletable],
    props: ['typeUser'],
    components: {OriginAddressForm, DataTable},
    data() {
        return {
            title: null,
            showDialog: false,
            resource: 'origin_addresses',
            recordId: null,
        }
    },
    created() {
        this.title = 'Direcciones de partida'
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
