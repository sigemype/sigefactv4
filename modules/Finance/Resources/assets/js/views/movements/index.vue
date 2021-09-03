<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Movimientos de ingresos y egresos</h3>
        </div>
        <div class="card mb-0">
            <div class="card-body">
                <data-table
                    :configuration="config"
                    :filter="filter"
                    :resource="resource">
                    <tr slot="heading">
                        <th class="">#</th>
                        <th :class="(filter.column === 'date_of_payment')?'text-info':''"
                            @click="ChangeOrder('date_of_payment')">Fecha
                        </th>
                        <th :class="(filter.column === 'person_name')?'text-info':''"  @click="ChangeOrder('person_name')">Adquiriente</th>
                        <th :class="(filter.column === 'number_full')?'text-info':''"  @click="ChangeOrder('number_full')">Documento/Transacción</th>
                        <th :class="(filter.column === 'items')?'text-info':''"  @click="ChangeOrder('items')">
                            Detalle
                            <el-tooltip
                                class="item"
                                content="Aplica a Ingresos/Gastos"
                                effect="dark"
                                placement="top-start"
                            >
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                        </th>
                        <th :class="(filter.column === 'currency_type_id')?'text-info':''"  @click="ChangeOrder('currency_type_id')">Moneda</th>
                        <th :class="(filter.column === 'instance_type_description')?'text-info':''"  @click="ChangeOrder('instance_type_description')">Tipo</th>
                        <th class="">Ingresos</th>
                        <th class="">Gastos</th>
                        <th class="">Saldo</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ row.index }}</td>
                        <td>{{ row.date_of_payment }}</td>
                        <td>
                            {{ row.person_name }}<br/><small
                            v-text="row.person_number"
                        ></small>
                        </td>
                        <td>
                            {{ row.number_full }}<br/>
                            <small v-text="row.document_type_description"></small>
                        </td>
                        <td>
                            <template v-for="(item, index) in row.items">
                                <label :key="index">- {{ item.description }}<br/></label>
                            </template>
                        </td>
                        <td>{{ row.currency_type_id }}</td>
                        <td>{{ row.instance_type_description }}</td>
                        <td>
                            <label v-show="row.input > 0 || row.input != '-'">S/ </label
                            >{{ row.input }}
                        </td>
                        <td>
                            <label v-show="row.output > 0 || row.output != '-'">S/ </label
                            >{{ row.output }}
                        </td>
                        <td>
                            <label v-show="row.balance > 0 || row.balance != '-'">S/ </label
                            >{{ row.balance }}
                        </td>
                    </tr>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/DataTableMovement.vue";

export default {
    components: {DataTable},
    props:[
        'configuration',
    ],
    data() {
        return {
            config:{},
            resource: "finances/movements",
            form: {},
            filter: {
                column: '',
                order: ''
            }
        };
    },
    created(){
        if(this.configuration !== undefined && this.configuration !== null && this.configuration.length > 0){
            this.$setStorage('configuration',this.configuration)
        }
        this.config = this.$getStorage('configuration');

    },
    methods: {
        ChangeOrder(col) {
            if (this.filter.order !== 'DESC') {
                this.filter.order = 'DESC'
            } else {
                this.filter.order = 'ASC'
            }
            this.filter.column = col
            this.$eventHub.$emit('filtrado', this.filter)
            console.log('sale')
        }
    }
};
</script>
