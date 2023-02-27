<template>
    <el-dialog :close-on-click-modal="false"
               :title="titleDialog"
               :visible="showDialog"
               :append-to-body="true"
               @close="clickClose"
               @open="create">
        <form autocomplete="off"
              @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div :class="{'has-danger': errors.identity_document_type_id}"
                             class="form-group">
                            <label class="control-label">Tipo Doc. Identidad <span class="text-danger">*</span></label>
                            <el-select v-model="form.identity_document_type_id"
                                       filterable>
                                <el-option v-for="option in identity_document_types"
                                           :key="option.id"
                                           :label="option.description"
                                           :value="option.id"></el-option>
                            </el-select>
                            <small v-if="errors.identity_document_type_id"
                                   class="form-control-feedback"
                                   v-text="errors.identity_document_type_id[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div :class="{'has-danger': errors.number}"
                             class="form-group">
                            <label class="control-label">Número <span class="text-danger">*</span></label>
                            <x-input-service v-model="form.number"
                                             :identity_document_type_id="form.identity_document_type_id"
                                             @search="searchNumber"></x-input-service>

                            <small v-if="errors.number"
                                   class="form-control-feedback"
                                   v-text="errors.number[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div :class="{'has-danger': errors.name}"
                             class="form-group">
                            <label class="control-label">Nombre <span class="text-danger">*</span></label>
                            <el-input v-model="form.name"
                                      dusk="name"></el-input>
                            <small v-if="errors.name"
                                   class="form-control-feedback"
                                   v-text="errors.name[0]"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div :class="{'has-danger': errors.address}"
                             class="form-group">
                            <label class="control-label">Dirección</label>
                            <el-input v-model="form.address"></el-input>
                            <small v-if="errors.address"
                                   class="form-control-feedback"
                                   v-text="errors.address[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div :class="{'has-danger': errors.location_id}"
                             class="form-group">
                            <label class="control-label">Ubigeo</label>
                            <el-cascader v-model="form.location_id"
                                         :options="locations"
                                         filterable></el-cascader>
                            <small v-if="errors.location_id"
                                   class="form-control-feedback"
                                   v-text="errors.location_id[0]"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="clickClose">Cancelar</el-button>
                <el-button :loading="loading_submit"
                           native-type="submit"
                           type="primary">Guardar
                </el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

import {serviceNumber} from '../../../../mixins/functions'

export default {
    name: 'DispatchPersonForm',
    mixins: [serviceNumber],
    props: [
        'showDialog',
        'title'
    ],
    data() {
        return {
            loading_submit: false,
            titleDialog: '',
            resource: 'dispatch_persons',
            api_service_token: false,
            errors: {},
            form: {},
            identity_document_types: [],
            locations: []
        }
    },
    async created() {
        await this.initForm()
    },
    computed: {
        maxLength: function () {
            if (this.form.identity_document_type_id === '6') {
                return 11
            }
            if (this.form.identity_document_type_id === '1') {
                return 8
            }
        },
    },
    methods: {
        initForm() {
            this.errors = {}
            this.form = {
                id: null,
                type: 'customers',
                country_id: 'PE',
                identity_document_type_id: '6',
                number: '',
                name: null,
                location_id: null,
                address: null,
                condition: null,
                state: null
            }
        },
        async create() {
            this.titleDialog = this.title;
            await this.$http.get(`/${this.resource}/tables`)
                .then(response => {
                    this.identity_document_types = response.data.identity_document_types
                    this.locations = response.data.locations
                })
        },
        validateDigits() {
            const pattern_number = new RegExp('^[0-9]+$', 'i');
            if (this.form.identity_document_type_id === '6') {
                if (this.form.number.length !== 11) {
                    return {
                        success: false,
                        message: `El campo número debe tener 11 dígitos.`
                    }
                }
                if (!pattern_number.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número debe contener solo números`
                    }
                }
            }
            if (this.form.identity_document_type_id === '1') {
                if (this.form.number.length !== 8) {
                    return {
                        success: false,
                        message: `El campo número debe tener 8 dígitos.`
                    }
                }
                if (!pattern_number.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número debe contener solo números`
                    }
                }
            }
            if (['4', '7', '0'].includes(this.form.identity_document_type_id)) {
                const pattern = new RegExp('^[A-Z0-9\-]+$', 'i');
                if (!pattern.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número no cumple con el formato establecido`
                    }
                }
            }
            return {
                success: true
            }
        },
        async submit() {
            let val_digits = await this.validateDigits()
            if (!val_digits.success) {
                return this.$message.error(val_digits.message)
            }

            this.loading_submit = true
            await this.$http.post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$emit('success', response.data.data)
                        this.clickClose()
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        console.log(error)
                    }
                })
                .finally(() => {
                    this.loading_submit = false
                })
        },
        searchNumber(data) {
            this.form.name = data.name;
            this.form.location_id = data.location_id;
            this.form.address = data.address;
            this.form.condition = data.condition;
            this.form.state = data.state;
        },
        clickClose() {
            this.$emit('update:showDialog', false)
        },
    }
}
</script>
