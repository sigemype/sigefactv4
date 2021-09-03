<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <el-tabs v-model="activeName">
                    <el-tab-pane class name="first">
                        <span slot="label">Datos de Usuario</span>
                <div class="row">
                    <div class="col-md-6">
                        <div :class="{ 'has-danger': errors.name }" class="form-group">
                            <label class="control-label">Nombre</label>
                            <el-input v-model="form.name"></el-input>
                            <small
                                v-if="errors.name"
                                class="form-control-feedback"
                                v-text="errors.name[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div :class="{ 'has-danger': errors.email }" class="form-group">
                            <label class="control-label">Correo Electrónico</label>
                            <el-input
                                v-model="form.email"
                                :disabled="form.id != null"
                            ></el-input>
                            <small
                                v-if="errors.email"
                                class="form-control-feedback"
                                v-text="errors.email[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            :class="{ 'has-danger': errors.establishment_id }"
                            class="form-group"
                        >
                            <label class="control-label">Establecimiento</label>
                            <el-select v-model="form.establishment_id" filterable @change="getSeries">
                                <el-option
                                    v-for="option in establishments"
                                    :key="option.id"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.establishment_id"
                                class="form-control-feedback"
                                v-text="errors.establishment_id[0]"
                            ></small>
                        </div>
                    </div>
                    <!-- Documento por defecto -->
                    <div class="col-md-4">
                        <div
                            :class="{ 'has-danger': errors.document_id }"
                            class="form-group"
                        >
                            <label class="control-label">Documento</label>
                            <el-select v-model="form.document_id" filterable clearable @change="getSeries">
                                <el-option
                                    v-for="option in documents"
                                    :key="option.id"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.document_id"
                                class="form-control-feedback"
                                v-text="errors.document_id[0]"
                            ></small>
                        </div>
                    </div>
                    <!-- Documento por defecto -->
                    <!-- Serie por defecto -->
                    <div class="col-md-4">
                        <div
                            :class="{ 'has-danger': errors.series_id }"
                            class="form-group"
                        >
                            <label class="control-label">Serie</label>
                            <el-select v-model="form.series_id" filterable clearable>
                                <el-option
                                    v-for="option in series"
                                    :key="option.id"
                                    :label="option.number"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.series_id"
                                class="form-control-feedback"
                                v-text="errors.series_id[0]"
                            ></small>
                        </div>
                    </div>
                    <!-- Serie por defecto -->

                    <div class="col-md-4">
                        <div :class="{ 'has-danger': errors.password }" class="form-group">
                            <label class="control-label">Contraseña</label>
                            <el-input v-model="form.password"></el-input>
                            <small
                                v-if="errors.password"
                                class="form-control-feedback"
                                v-text="errors.password[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            :class="{ 'has-danger': errors.password_confirmation }"
                            class="form-group"
                        >
                            <label class="control-label">Confirmar Contraseña</label>
                            <el-input v-model="form.password_confirmation"></el-input>
                            <small
                                v-if="errors.password_confirmation"
                                class="form-control-feedback"
                                v-text="errors.password_confirmation[0]"
                            ></small>
                        </div>
                    </div>
                    <div v-if="typeUser != 'integrator'" class="col-md-4">
                        <div :class="{ 'has-danger': errors.type }" class="form-group">
                            <label class="control-label">Perfil</label>
                            <el-select v-model="form.type" :disabled="form.id === 1">
                                <el-option
                                    v-for="option in types"
                                    :key="option.type"
                                    :label="option.description"
                                    :value="option.type"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.type"
                                class="form-control-feedback"
                                v-text="errors.type[0]"
                            ></small>
                        </div>
                    </div>
                    <div v-show="form.id" class="col-md-9">
                        <div :class="{ 'has-danger': errors.api_token }" class="form-group">
                            <label class="control-label">Api Token</label>
                            <el-input
                                v-model="form.api_token"
                                :readonly="form.id != null"
                            ></el-input>
                            <small
                                v-if="errors.api_token"
                                class="form-control-feedback"
                                v-text="errors.api_token[0]"
                            ></small>
                        </div>
                    </div>
                    <div v-show="form.id" class="col-md-3 text-center">
                        <label class="control-label full">&nbsp;</label>
                        <el-button
                            @click.prevent="updateToken()">
                            Generar Token
                        </el-button>

                    </div>
                </div>
                    </el-tab-pane>
                    <el-tab-pane class name="second">
                        <span slot="label">Permisos</span>
                        <div class="row">
                    <div v-if="typeUser != 'integrator'" class="col-md-12">
                        <div class="form-comtrol">
                            <label class="control-label">Permisos Módulos</label>
                            <div class="form-group tree-container-admin">
                                <el-tree
                                    ref="tree"
                                    :check-strictly="true"
                                    :data="modules"
                                    :props="defaultProps"
                                    accordion
                                    highlight-current
                                    node-key="id"
                                    show-checkbox
                                    @check="FixChildren"
                                >
                                </el-tree>
                            </div>
                        </div>
                    </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button :loading="loading_submit" native-type="submit" type="primary"
                >Guardar
                </el-button
                >
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "recordId", "typeUser"],
    data() {
        return {
            defaultProps: {
                children: "childrens",
                label: "description",
            },
            loading_submit: false,
            titleDialog: null,
            resource: "users",
            errors: {},
            form: {
                id: null,
                name: null,
                email: null,
                api_token: null,
                establishment_id: null,
                password: null,
                password_confirmation: null,
                locked: false,
                type: null,
                series_id: null,
                document_id: null,
                modules: [],
                levels: [],
            },
            modules: [],
            datai: [],
            establishments: [],
            documents: [],
            series: [],
            types: [],
            // define the default value
            value: null,
            // define options
            alwaysOpen: true,
            options: [],
            activeName: 'first'
        };
    },
    updated() {
        // Set default values for multiple selection trees
        if (this.modules !== undefined && this.$refs.tree !== undefined) {
            // this.$refs.tree.setCheckedKeys(this.modules)
        }
    },
    async created() {
        await this.$http.get(`/${this.resource}/tables`).then((response) => {
            this.modules = response.data.modules;
            this.establishments = response.data.establishments;
            this.types = response.data.types;
            this.documents = response.data.documents;
            this.getSeries();
        });
        await this.initForm();
    },
    methods: {
        getSeries(){
            this.series = [];
            if(this.form.establishment_id !== null) {
                let url = `/series/records/${this.form.establishment_id}`;
                if (this.form.document_id !== null) {
                    url = url + `/${this.form.document_id}`;
                }
                this.$http
                    .get(url)
                    .then((response) => {
                        this.series = response.data.data;
                    });
            }
        },
        updateToken(){
            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}/token/${this.form.id}`, {})
                .then((response) => {
                    if (response.data.success) {
                        this.form.api_token = response.data.api_token;
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error(error.response.data.message);
                    }
                    this.loading_submit = false;

                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        FixChildren(currentObj, treeStatus) {
            if (currentObj !== undefined) {
                let selected = treeStatus.checkedKeys.indexOf(currentObj.id) // -1 is unchecked
                if (selected !== -1) {
                    this.SelectParent(currentObj)
                    this.FixSameValueToChild(currentObj, true)
                } else {
                    if (currentObj.childrens !== undefined && currentObj.childrens.length !== 0) {
                        this.FixSameValueToChild(currentObj, false)
                    }
                }
            }
        },
        FixSameValueToChild(treeList, isSelected) {
            if (treeList !== undefined) {
                this.$refs.tree.setChecked(treeList.id, isSelected)
                if (treeList.childrens !== undefined) {
                    for (let i = 0; i < treeList.childrens.length; i++) {
                        this.FixSameValueToChild(treeList.childrens[i], isSelected)
                    }
                }
            }
        },
        SelectParent(currentObj) {
            if (currentObj !== undefined) {
                let currentNode = this.$refs.tree.getNode(currentObj)
                if (currentNode.parent.key !== undefined) {
                    this.$refs.tree.setChecked(currentNode.parent, true)
                    this.SelectParent(currentNode.parent)
                }
            }
        },


        initForm() {
            this.errors = {};
            this.form = {
                id: null,
                name: null,
                email: null,
                api_token: null,
                establishment_id: null,
                password: null,
                password_confirmation: null,
                locked: false,
                type: null,
                series_id: null,
                document_id: null,
                modules: [],
                levels: [],
            };
        },
        create() {
            this.titleDialog = this.recordId ? "Editar Usuario" : "Nuevo Usuario";
            if (this.recordId) {
                this.$http
                    .get(`/${this.resource}/record/${this.recordId}`)
                    .then((response) => {
                        this.form = response.data.data;

                        this.$refs.tree.setCheckedKeys([]);
                        const preSelecteds = [];
                        const preSelectedsModules = this.form.modules;
                        const preSelectedsLevels = this.form.levels;
                        this.getSeries();
                        this.modules.map((m) => {
                            if (preSelectedsModules.includes(m.id)) {
                                preSelecteds.push(m.id);
                            }
                            m.childrens.map((c) => {
                                const idArray = c.id.split("-");
                                if (preSelectedsLevels.includes(parseInt(idArray[1]))) {
                                    preSelecteds.push(c.id);
                                }
                            });
                        });
                        setTimeout(() => {
                            this.$refs.tree.setCheckedKeys(preSelecteds);
                        }, 1000);
                    });
            } else {
                this.$http.get(`/${this.resource}/tables`).then((response) => {
                    this.$refs.tree.setCheckedKeys([]);
                    this.modules = response.data.modules;
                    this.establishments = response.data.establishments;
                    this.types = response.data.types;
                    this.documents = response.data.documents;
                    this.series = response.data.series;
                });
            }
        },
        submit() {
            const modulesAndLevelsSelecteds = this.$refs.tree.getCheckedNodes();
            const modules = [];
            modulesAndLevelsSelecteds.map((m) => {
                if (m.is_parent) {
                    modules.push(m.id);
                }
            });
            const levels = [];
            modulesAndLevelsSelecteds.filter((l) => {
                if (!l.is_parent) {
                    const idArray = l.id.split("-");
                    levels.push(idArray[1]);
                }
            });
            this.form.modules = modules;
            this.form.levels = levels;

            if (modules.length < 1) {
                return this.$message.error("Debe seleccionar al menos un módulo");
            }
            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}`, this.form)
                .then((response) => {
                    if (response.data.success) {
                        this.form.password = null;
                        this.form.password_confirmation = null;
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                        this.close();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error(error.response.data.message);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        close() {
            this.$emit("update:showDialog", false);
            this.initForm();
        },
    },
};
</script>
