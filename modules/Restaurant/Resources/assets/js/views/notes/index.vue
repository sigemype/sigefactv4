<template>
    <div>
        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <el-input
                            v-model="form.description"
                            placeholder="Ingresa la descripción"
                        ></el-input>
                    </div>
                    <div class="col-md-3">
                        <el-button type="primary" @click.prevent="save()"
                            >Guardar</el-button
                        >
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripción</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, index) in records"
                                    :key="index + 'IN'"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ row.description }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn waves-effect waves-light btn-xs btn-danger"
                                            @click.prevent="clickDelete(row.id)"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { deletable } from "@mixins/deletable";

export default {
    mixins: [deletable],
    components: {},
    props: ["typeUser"],
    data() {
        return {
            loading: false,
            resource: "restaurant/notes",
            recordId: null,
            records: [],
            form: {}
        };
    },
    async created() {
        this.getRecords();
        this.initForm();
    },
    methods: {
        initForm() {
            this.form = {
                description: null
            };
        },
        save() {
            this.loading = true;

            this.$http
                .post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.initForm();
                        this.getRecords();
                    } else {
                        this.$message.error("Error al guardar");
                    }
                })
                .catch(error => {
                    this.$message.error("Error al guardar");
                })
                .then(() => {
                    this.loading = false;
                });
        },
        getRecords() {
            return this.$http
                .get(`/${this.resource}/records`)
                .then(response => {
                    this.records = response.data.records;
                });
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() =>
                this.getRecords()
            );
        }
    }
};
</script>
