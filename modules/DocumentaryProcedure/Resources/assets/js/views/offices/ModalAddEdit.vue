<template>
    <el-dialog
        v-if="OfficeNotNull"
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
    >
        <form autocomplete="off" @submit.prevent="onSubmit">
            <div class="form-body row">
                <div class="form-group col-md-12">
                    <label for="name">Nombre de la etapa</label>
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
                <div class="form-group col-md-6">
                    <label for="description">Descripción</label>
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
                <div class="form-group col-md-6">
                    <div :class="{'has-danger': errors.days}" class="form-group">
                        <label class="control-label">Dias de tramite</label>
                        <el-input-number
                            v-model="form.days"
                        >

                        </el-input-number>

                        <small v-if="errors.days" class="form-control-feedback"
                               v-text="errors.days[0]"></small>
                    </div>
                </div>
                <!-- v-if="hasParent"  -->
                <!--
                <div  :class="{'has-danger': errors.type}" class="form-group">
                    <label class="control-label">Etapa principal</label>
                    <el-select
                        v-model="form.parent_id"
                        :clearable="true">
                        <el-option
                            v-for="item in parent_offices"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        ></el-option>
                    </el-select>
                    <small v-if="errors.parent_id" class="form-control-feedback"
                           v-text="errors.parent_id[0]"></small>


                </div>
                -->

                <div v-if="office!==undefined" class="form-group col-md-12">
                    <div :class="{'has-danger': errors.type}" class="form-group">
                        <label class="control-label">Responsable</label>
                        <el-select
                            v-model="form.users"
                            :clearable="true"
                        :multiple="true"
                        >
                            <el-option v-for="option in workers"
                                       :key="option.id"
                                       :label="option.name"
                                       :value="option.id"></el-option>
                        </el-select>
                        <small v-if="errors.rel_user_to_documentary_offices" class="form-control-feedback"
                               v-text="errors.rel_user_to_documentary_offices[0]"></small>
                    </div>
                </div>

                <div class="form-group col-md-12">
                    <label>Mostrar etapa</label>
                    <el-switch v-model="form.active"></el-switch>
                </div>
                <div class="row text-center col-md-12">
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
            </div>
        </form>
    </el-dialog>
</template>

<script>
import {mapActions, mapState} from "vuex";

export default {
    props: {
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },/*
        parent_offices: {
            type: Array,
            required: false,
            default: [],
        }*/
        /*
        office: {
            type: Object,
            required: false,
            default: {},
        },
        */
    },
    computed: {
        ...mapState([
            'offices',
            'office',
            'workers',
        ]),
        OfficeNotNull: function () {
            if (this.office !== null) return true
            return false;
        },
        hasParent: function () {
            if (
                this.office !== undefined &&
                this.office.parent !== undefined &&
                this.office.parent.id > 0) {
                return true;
            }

            return false;
        },
    },
    created() {
        this.loadWorkers()


    },
    mounted() {
    },
    data() {
        return {
            form: {},
            title: "",
            errors: {},
            loading: false,
            basePath: "/documentary-procedure/offices",
        };
    },
    methods: {
        ...mapActions([
            'loadWorkers',
        ]),
        onUpdate() {
            this.loading = true;
            this.$http
                .put(`${this.basePath}/${this.office.id}/update`, this.form)
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
            if (this.office) {
                this.onUpdate();
            } else {
                this.onStore();
            }
        },
        onClose() {
            this.$emit("update:visible", false);
            this.$store.commit('setOffice', {})
        },
        onCreate() {
            if (this.office) {
                this.form = this.office;
                this.title = "Editar Etapa";
            } else {
                this.title = "Crear Etapa";
                this.form = {
                    active: true,
                };
            }
        },
    },
};
</script>
