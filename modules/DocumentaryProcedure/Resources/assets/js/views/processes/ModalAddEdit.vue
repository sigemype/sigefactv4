<template>
    <el-dialog
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
    >
        <form autocomplete="off" @submit.prevent="onSubmit">

            <el-tabs v-model="activeName">
                <el-tab-pane class name="first">
                    <span slot="label">Datos del tramite</span>
                    <div class="form-body row">
                        <div class="col-md-12 form-group">
                            <label for="name">Nombre del trámite</label>
                            <input
                                id="name"
                                v-model="form.name"
                                :class="{ 'is-invalid': errors.name }"
                                class="form-control"
                                type="text"
                            />
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.name[0] }}
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="description">Descripción del trámite</label>
                            <input
                                id="description"
                                v-model="form.description"
                                :class="{ 'is-invalid': errors.description }"
                                class="form-control"
                                type="text"
                            />
                            <div v-if="errors.description" class="invalid-feedback">
                                {{ errors.description[0] }}
                            </div>
                        </div>
                        <div class="col-md-6  form-group">
                            <label for="price">Precio</label>
                            <input
                                id="price"
                                v-model="form.price"
                                :class="{ 'is-invalid': errors.price }"
                                class="form-control"
                                min="0"
                                type="number"
                            />
                            <div v-if="errors.description" class="invalid-feedback">
                                {{ errors.description[0] }}
                            </div>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Mostrar trámite</label>
                            <el-switch v-model="form.active"></el-switch>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane class name="second">
                    <span slot="label">Seleccion de etapas</span>

                    <div v-if="stages!==undefined" class="col-md-12">
                        <div :class="{'has-danger': errors.stages_order}" class="form-group">
                            <label class="control-label">Etapas</label>
                            <el-transfer
                                v-model="form.stages"
                                :data="stages"
                                :filterable="true"
                                :props="{ key: 'id', label: 'selector_name',disabled:'disable' }"
                                :target-order="'push'"
                                :titles="titles"

                            >
                            </el-transfer>
                            <small v-if="errors.stages_order" class="form-control-feedback"
                                   v-text="errors.stages_order[0]"></small>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane class name="third">
                    <span slot="label">Lista de requirimientos</span>
                    <div v-if="stages!==undefined" class="col-md-12">
                        <div :class="{'has-danger': errors.stages_order}" class="form-group">
                            <label class="control-label">Etapas</label>
                            <el-transfer
                                v-model="form.requirements_id"
                                :data="requirements"
                                :filterable="true"
                                :props="{ key: 'id', label: 'name'}"
                                :target-order="'push'"
                                :titles="titles_requirements"

                            >
                            </el-transfer>
                            <small v-if="errors.stages_order" class="form-control-feedback"
                                   v-text="errors.stages_order[0]"></small>
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>

            <div class="col-md-12 row text-center p-t-20">
                <div class="col-6">
                    <el-button
                        :disabled="loading"
                        :loading="loading"
                        class="btn-block"
                        native-type="submit"
                        type="primary"
                    >Guardar
                    </el-button
                    >
                </div>
                <div class="col-6">
                    <el-button class="btn-block" @click="onClose">Cancelar</el-button>
                </div>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        process: {
            type: Object,
            required: false,
            default: {},
        },
        stages: {
            type: Array,
            required: false,
            default: [],
        },
        requirements: {
            type: Array,
            required: false,
            default: [],
        },
    },
    data() {
        return {
            activeName: 'first',
            titles: ['Etapas', 'Seleccionadas'],
            titles_requirements: ['Requerimientos', 'Seleccionadas'],
            form: {},
            title: "",
            errors: {},
            loading: false,
            basePath: "/documentary-procedure/processes",
        };
    },
    methods: {
        onUpdate() {
            this.loading = true;
            this.$http
                .put(`${this.basePath}/${this.process.id}/update`, this.form)
                .then((response) => {
                    this.$message({
                        message: response.data.message,
                        type: "success",
                    });
                    this.$emit("onUpdateItem", response.data.data);
                    this.onClose();
                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        onStore() {
            this.loading = true;
            this.$http
                .post(`${this.basePath}/store`, this.form)
                .then((response) => {
                    this.$message({
                        message: response.data.message,
                        type: "success",
                    });
                    this.$emit("onAddItem", response.data.data);
                    this.onClose();
                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        onSubmit() {
            if (this.process) {
                this.onUpdate();
            } else {
                this.onStore();
            }
        },
        onClose() {
            this.tabActive = 'first';
            this.$emit("update:visible", false);
        },
        onCreate() {
            if (this.process) {
                this.form = this.process;
                this.title = "Editar trámite";
            } else {
                this.title = "Crear trámite";
                this.form = {
                    active: true,
                };
            }
        },
    },
    computed: {
        stagesData: function () {
            let data = [];
            this.stages.forEach((val, index) => {
                    console.error(val)
                    console.dir(index)
                    data.push({
                        value: val.id,
                        desc: val.selector_name,
                        label: val.selector_name,
                        disabled: !val.active
                    })
                }
            )
            return data;
        }


    },
};
</script>
