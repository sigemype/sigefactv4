<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
    >
        <form autocomplete="off" @submit.prevent="submit" v-loading="loading">
            <div class="form-body">
                <el-tabs v-model="activeName">
                    <el-tab-pane class name="first">
                        <span slot="label">Datos de Usuario</span>
                <div class="row">
                    <div class="col-md-6">
                        <div :class="{ 'has-danger': errors.name }" class="form-group">
                            <label class="control-label">
                                Nombre
                                <el-tooltip class="item"
                                            content="Nombre que se muestra en el sistema al haber iniciado sesión"
                                            effect="dark"
                                            placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
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
                            <label class="control-label">Correo electrónico
                                <el-tooltip class="item"
                                            content="Correo de acceso/login"
                                            effect="dark"
                                            placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
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
                            <el-select v-model="form.establishment_id" filterable @change="changeEstablishment">
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
                    <!-- Zona por usuario -->
                    <!--
                    <div class="col-md-4">
                        <div :class="{'has-danger': errors.zone_id}"
                             class="form-group">
                            <label class="control-label">
                                Zona
                            </label>

                            <a v-if="form_zone.add == false"
                               class="control-label font-weight-bold text-info"
                               href="#"
                               @click="form_zone.add = true"> [ + Nuevo]</a>
                            <a v-if="form_zone.add == true"
                               class="control-label font-weight-bold text-info"
                               href="#"
                               @click="saveZone()"> [ + Guardar]</a>
                            <a v-if="form_zone.add == true"
                               class="control-label font-weight-bold text-danger"
                               href="#"
                               @click="form_zone.add = false"> [ Cancelar]</a>
                            <el-input v-if="form_zone.add == true"
                                      v-model="form_zone.name"
                                      dusk="item_code"
                                      style="margin-bottom:1.5%;"></el-input>

                            <el-select v-if="form_zone.add == false"
                                       v-model="form.zone_id"
                                       clearable
                                       filterable>
                                <el-option v-for="option in zones"
                                           :key="option.id"
                                           :label="option.name"
                                           :value="option.id"></el-option>
                            </el-select>
                            <small v-if="errors.zone_id"
                                   class="form-control-feedback"
                                   v-text="errors.zone_id[0]"></small>
                        </div>
                    </div>
                    -->

                    <div class="col-md-4">
                        <div :class="{ 'has-danger': errors.password }" class="form-group">
                            <label class="control-label">Contraseña
                                <el-tooltip class="item" effect="dark" placement="top-start" v-if="config_regex_password_user">
                                    <i class="fa fa-info-circle"></i>
                                    <div slot="content">
                                        <strong>FORMATO DE CONTRASEÑA</strong><br/><br/>
                                        La contraseña debe contener al menos una letra minúscula.<br/>
                                        La contraseña debe contener al menos una letra mayúscula.<br/>
                                        La contraseña debe contener al menos un dígito.<br/>
                                        La contraseña debe contener al menos un carácter especial [@.$!%*#?&-].<br/>
                                    </div>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.password" show-password></el-input>
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
                            <el-input v-model="form.password_confirmation" show-password></el-input>
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
                    <div v-show="form.id" class="col-md-8">
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
                    <div v-show="form.id" class="col-md-3">
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


                        <div class="row" v-if="typeUser != 'integrator'">
                            <div  class="col-md-12 mt-4">
                                <div class="form-comtrol">
                                    <label class="control-label">Otros Permisos</label>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1" v-if="config_permission_to_edit_cpe">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.permission_edit_cpe">
                                        Editar CPE
                                    </el-checkbox>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.recreate_documents">
                                        Recrear documentos
                                    </el-checkbox>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mt-1" v-if="form.type === 'admin'">
                                <div class="form-comtrol">
                                    
                                    <el-tooltip class="item"
                                                content="Se habilita el permiso para modificar el tipo de envío de las boletas - envío individual a resumen de boletas (solo aplica si la boleta fue enviada de forma individual y se encuentra en estado registrado)"
                                                effect="dark"
                                                placement="top">
                                        <el-checkbox v-model="form.permission_force_send_by_summary">
                                            Modificar envio individual
                                        </el-checkbox>
                                    </el-tooltip>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="typeUser != 'integrator'">
                            <div  class="col-md-12 mt-4">
                                <div class="form-comtrol">
                                    <label class="control-label">Pagos
                                        <el-tooltip class="item"
                                                    content="Disponible en: Listado de comprobantes - Pagos"
                                                    effect="dark"
                                                    placement="top">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.create_payment">
                                        Agregar
                                    </el-checkbox>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.delete_payment">
                                        Eliminar
                                    </el-checkbox>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="typeUser != 'integrator'">
                            <div  class="col-md-12 mt-4">
                                <div class="form-comtrol">
                                    <label class="control-label">Compras
                                        <el-tooltip class="item"
                                                    content="Disponible en: Listado de compras"
                                                    effect="dark"
                                                    placement="top">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.edit_purchase">
                                        Editar
                                    </el-checkbox>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.annular_purchase">
                                        Anular
                                    </el-checkbox>
                                </div>
                            </div>
                            <div  class="col-md-4 mt-1">
                                <div class="form-comtrol">
                                    <el-checkbox v-model="form.delete_purchase">
                                        Eliminar
                                        <el-tooltip class="item"
                                                    content="Disponible cuando se anule la compra"
                                                    effect="dark"
                                                    placement="top">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </el-checkbox>
                                </div>
                            </div>
                        </div>

                    </el-tab-pane>
                    
                    <el-tab-pane class name="third">
                        <span slot="label">Datos personales</span>
                        <div class="row">

                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.identity_document_type_id}"
                                     class="form-group">
                                    <label class="control-label">Tipo Doc. Identidad</label>
                                    <el-select v-model="form.identity_document_type_id" clearable>
                                        <el-option v-for="option in identity_document_types" :key="option.id" :label="option.description" :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.identity_document_type_id" class="form-control-feedback" v-text="errors.identity_document_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.number }" class="form-group">
                                    <label class="control-label">Número</label>
                                    <el-input v-model="form.number"></el-input>
                                    <small v-if="errors.number" class="form-control-feedback" v-text="errors.number[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.names }" class="form-group">
                                    <label class="control-label">Nombres</label>
                                    <el-input v-model="form.names"></el-input>
                                    <small v-if="errors.names" class="form-control-feedback" v-text="errors.names[0]"></small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.last_names }" class="form-group">
                                    <label class="control-label">Apellidos</label>
                                    <el-input v-model="form.last_names"></el-input>
                                    <small v-if="errors.last_names" class="form-control-feedback" v-text="errors.last_names[0]"></small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.date_of_birth }" class="form-group">
                                    <label class="control-label">Fecha de nacimiento</label>
                                    <el-date-picker v-model="form.date_of_birth" type="date" value-format="yyyy-MM-dd"></el-date-picker>
                                    <small v-if="errors.date_of_birth" class="form-control-feedback" v-text="errors.date_of_birth[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.personal_email }" class="form-group">
                                    <label class="control-label">Correo electrónico personal</label>
                                    <el-input v-model="form.personal_email"></el-input>
                                    <small v-if="errors.personal_email" class="form-control-feedback" v-text="errors.personal_email[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.personal_cell_phone }" class="form-group">
                                    <label class="control-label">N° Celular personal</label>
                                    <el-input v-model="form.personal_cell_phone"></el-input>
                                    <small v-if="errors.personal_cell_phone" class="form-control-feedback" v-text="errors.personal_cell_phone[0]"></small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.address }" class="form-group">
                                    <label class="control-label">Dirección</label>
                                    <el-input v-model="form.address"></el-input>
                                    <small v-if="errors.address" class="form-control-feedback" v-text="errors.address[0]"></small>
                                </div>
                            </div> 

                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.corporate_email }" class="form-group">
                                    <label class="control-label">Correo electrónico corporativo</label>
                                    <el-input v-model="form.corporate_email"></el-input>
                                    <small v-if="errors.corporate_email" class="form-control-feedback" v-text="errors.corporate_email[0]"></small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.contract_date }" class="form-group">
                                    <label class="control-label">Fecha de contratación</label>
                                    <el-date-picker v-model="form.contract_date" type="date" value-format="yyyy-MM-dd"></el-date-picker>
                                    <small v-if="errors.contract_date" class="form-control-feedback" v-text="errors.contract_date[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.corporate_cell_phone }" class="form-group">
                                    <label class="control-label">N° Celular corporativo</label>
                                    <el-input v-model="form.corporate_cell_phone"></el-input>
                                    <small v-if="errors.corporate_cell_phone" class="form-control-feedback" v-text="errors.corporate_cell_phone[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div :class="{ 'has-danger': errors.position }" class="form-group">
                                    <label class="control-label">Cargo</label>
                                    <el-input v-model="form.position"></el-input>
                                    <small v-if="errors.position" class="form-control-feedback" v-text="errors.position[0]"></small>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label class="control-label">Foto</label>
                                    <el-upload class="avatar-uploader"
                                            :headers="headers"
                                            :action="`/general-upload-temp-image`"
                                            :show-file-list="false"
                                            :on-success="onUploadSuccess">
                                        <img v-if="form.photo_temp_image" :src="form.photo_temp_image" class="avatar">
                                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                                    </el-upload>
                                </div>
                            </div>

                        </div>
                    </el-tab-pane>

                    <el-tab-pane class name="fourth">
                        <span slot="label">Config. Documentos</span>
                        <div class="row">
                            
                            
                            <div class="col-md-12">
                                <label class="control-label">¿Múltiples tipos de documento por defecto?</label>
                                <div class="form-group">
                                    <el-switch v-model="form.multiple_default_document_types" active-text="Si" inactive-text="No" @change="changeMultipleDefaultDocumentType"></el-switch>
                                </div>
                            </div>

                            <template v-if="form.multiple_default_document_types"> 

                                <div class="col-md-12 mt-3">
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                            <tr width="100%">
                                                <template v-if="form.default_document_types.length > 0">
                                                    <th class="pb-2" width="42%">Tipo de documento</th>
                                                    <th class="pb-2" width="42%">Serie
                                                        <el-tooltip class="item"
                                                                    content="Si modifica el establecimiento, se filtrarán nuevamente las series"
                                                                    effect="dark"
                                                                    placement="top">
                                                            <i class="fa fa-info-circle"></i>
                                                        </el-tooltip>
                                                    </th>
                                                </template>
                                                <th width="16%"><a href="#" @click.prevent="clickAddDefaultDocumentType" class="text-center font-weight-bold text-info">[+ Agregar]</a></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in form.default_document_types" :key="index" width="100%">
                                                <td>
                                                    <div class="form-group mb-2 mr-2">
                                                        <el-select v-model="row.document_type_id" @change="changeDefaultDocumentType(index)">
                                                            <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                        </el-select>
                                                        
                                                        <template v-if="errors[`default_document_types.${index}.document_type_id`]">
                                                            <div class="form-group" :class="{'has-danger': errors[`default_document_types.${index}.document_type_id`]}">
                                                                <small class="form-control-feedback" v-text="errors[`default_document_types.${index}.document_type_id`][0]"></small>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-2 mr-2">
                                                        <el-select v-model="row.series_id" filterable >
                                                            <el-option v-for="option in row.default_series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                                                        </el-select>

                                                        <template v-if="errors[`default_document_types.${index}.series_id`]">
                                                            <div class="form-group" :class="{'has-danger': errors[`default_document_types.${index}.series_id`]}">
                                                                <small class="form-control-feedback" v-text="errors[`default_document_types.${index}.series_id`][0]"></small>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="series-table-actions text-center">
                                                    <button  type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDeleteDefaultDocumentType(index)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                                <br>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </template>
                            <template v-else>
                                
                                <!-- Documento por defecto -->
                                <div class="col-md-4 mt-3">
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
                                <div class="col-md-4 mt-3">
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
                            </template>
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
            headers: headers_token,
            defaultProps: {
                children: "childrens",
                label: "description",
            },
            form_zone: {add: false, name: null, id: null},
            loading_submit: false,
            titleDialog: null,
            resource: "users",
            errors: {},
            zones:[],
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
                permission_edit_cpe: false,
                recreate_documents: false,
                permission_force_send_by_summary: false,
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
            activeName: 'first',
            config_permission_to_edit_cpe : false,
            config_regex_password_user: false,
            identity_document_types: [],
            document_types: [],
            loading: false,
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
            this.zones = response.data.zones;
            this.types = response.data.types;
            this.documents = response.data.documents;
            this.config_permission_to_edit_cpe = response.data.config_permission_to_edit_cpe
            this.config_regex_password_user = response.data.config_regex_password_user
            this.identity_document_types = response.data.identity_document_types
            this.document_types = this.filterDocumentTypes(response.data.documents)

            this.getSeries();
        });
        await this.initForm();
    },
    methods: {
        onUploadSuccess(response, file, fileList) {
            if (response.success) 
            {
                this.form.photo_filename = response.data.filename
                this.form.photo_temp_image = response.data.temp_image
                this.form.photo_temp_path = response.data.temp_path
            } 
            else
            {
                this.$message.error(response.message)
            }
        },
        async getSeries(){
            this.series = [];
            if(this.form.establishment_id !== null) {
                let url = `/series/records/${this.form.establishment_id}`;
                if (this.form.document_id !== null) {
                    url = url + `/${this.form.document_id}`;
                }
                await this.$http
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
                permission_edit_cpe: false,
                recreate_documents: false,
                create_payment: true,
                delete_payment: true,
                edit_purchase: true,
                annular_purchase: true,
                delete_purchase: true,
                
                identity_document_type_id: null,
                number: null,
                address: null,
                names: null,
                last_names: null,
                personal_email: null,
                corporate_email: null,
                personal_cell_phone: null,
                corporate_cell_phone: null,
                date_of_birth: null,
                contract_date: null,
                position: null,

                photo_filename: null,
                photo_temp_image: null,
                photo_temp_path: null,
                multiple_default_document_types: false,
                default_document_types: [],
                permission_force_send_by_summary: false,
            };
        },
        async changeEstablishment()
        {
            await this.getSeries()
            await this.initDataDefaultDocumentTypes()
        },
        initDataDefaultDocumentTypes(init_series_id = true)
        {
            this.form.default_document_types.forEach(row => {
                if(init_series_id) row.series_id = null
                row.default_series = this.getDefaultDocumentTypeSeries(row.document_type_id)
            })
        },
        clickAddDefaultDocumentType()
        {
            if(!this.form.establishment_id) return this.$message.warning('Seleccione un establecimiento para buscar las series disponibles.')

            this.form.default_document_types.push({
                document_type_id: null,
                series_id: null,
                default_series: [],
            })
        },
        changeMultipleDefaultDocumentType()
        {
            if(this.form.multiple_default_document_types)
            {
                this.form.document_id = null
                this.getSeries()
            } 
            else
            {
                this.form.default_document_types = []
            }
        },
        clickDeleteDefaultDocumentType(index)
        {
            this.form.default_document_types.splice(index, 1)
        },
        changeDefaultDocumentType(index)
        {
            this.form.default_document_types[index].series_id = null

            const current_document_type_id = this.form.default_document_types[index].document_type_id

            const exist_document_type = this.getExistDocumentType(current_document_type_id, index)

            if(exist_document_type)
            {
                this.form.default_document_types[index].document_type_id = null
                return this.$message.warning('Ya agregó ese tipo de documento')
            }

            this.form.default_document_types[index].default_series = this.getDefaultDocumentTypeSeries(current_document_type_id)
        },
        getExistDocumentType(current_document_type_id, index)
        {
            return this.form.default_document_types.find((row, row_index)=>{
                    return row.document_type_id === current_document_type_id && index !== row_index
                })
        },
        getDefaultDocumentTypeSeries(document_type_id)
        {
            return _.filter(this.series, { document_type_id : document_type_id })
        },
        async create() {
            this.titleDialog = this.recordId ? "Editar Usuario" : "Nuevo Usuario"

            this.loading = true

            if (this.recordId) 
            {
                await this.$http
                    .get(`/${this.resource}/record/${this.recordId}`)
                    .then((response) => {
                        this.form = response.data.data;

                        this.$refs.tree.setCheckedKeys([]);
                        const preSelecteds = [];
                        const preSelectedsModules = this.form.modules;
                        const preSelectedsLevels = this.form.levels;
                        // this.getSeries();
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

                await this.getSeries()
                await this.initDataDefaultDocumentTypes(false)

            } 
            else 
            {
                await this.$http.get(`/${this.resource}/tables`).then((response) => {
                    this.$refs.tree.setCheckedKeys([]);
                    this.modules = response.data.modules;
                    this.establishments = response.data.establishments;
                    this.zones = response.data.zones;
                    this.types = response.data.types;
                    this.documents = response.data.documents;
                    this.series = response.data.series;
                })
            }

            this.loading = false

        },
        filterDocumentTypes(data)
        {
            return data.filter(element => {
                return ['01', '03', '80', '09'].includes(element.id) 
            })
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

            this.form.config_regex_password_user = this.config_regex_password_user
            
            
            if (this.form.multiple_default_document_types && this.form.default_document_types.length == 0) return this.$message.error('Debe agregar al menos un tipo de documento por defecto')

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
        saveZone() {
            this.form_zone.add = false

            this.$http.post(`/zones`, this.form_zone)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.zones.push(response.data.data)
                        this.form_zone.name = null

                    } else {
                        this.$message.error('No se guardaron los cambios')
                    }
                })
                .catch(error => {

                })


        },

    },
};
</script>