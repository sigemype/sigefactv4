<template>
    <data-table :resource="resource">
        <tr slot="heading">
            <th width="5%">#</th>
            <th>Codigo</th>
            <th class="text-center">Serie</th>
            <th>Nombre</th>

            <th>Und</th>
            <th>Fecha</th>
            <th class="text-center">Estado</th>
        </tr>

        <tr slot-scope="{ index, row }">
            <td>{{ index + 1 }}</td>
            <td>{{ row.code_item }}</td>
            <td class="text-center">{{ row.series }}</td>

            <td>{{ row.name_item }}</td>

            <td>{{ row.und_item }}</td>
            <td>{{ row.date }}</td>

            <td class="text-center">{{ row.status }}</td>
        </tr>
    </data-table>
</template>

<script>
import DataTable from "../../components/DataTableKardex.vue";

export default {
    components: {DataTable},
    data() {
        return {
            resource: "reports/kardex_series",
            form: {},
            item_id: "",
            records: [],
            items: []
        };
    },
    created() {
        this.$eventHub.$on("emitItemID", item_id => {
            // console.log(item_id)
            this.item_id = item_id;
        });
    },
    methods: {}
};
</script>
