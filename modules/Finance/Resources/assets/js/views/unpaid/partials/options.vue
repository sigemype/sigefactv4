<template>
    <el-dialog
               :title="titleDialog"
               :visible="showDialogOptions"
               append-to-body
               width="30%"
               @open="create"
               @close="closeUnpaid">
        <!--
        <Keypress
            key-event="keyup"
            @success="checkKey"
        />
        -->
        <div v-loading="loading">

            <div class="row">

                <div class="col text-center font-weight-bold mt-3">
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('a4')">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>A4</p>
                </div>

                <div v-if="ShowTicket80"
                     class="col text-center font-weight-bold mt-3">

                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('ticket')">
                        <i class="fa fa-receipt"></i>
                    </button>
                    <p>80MM</p>
                </div>

                <div v-if="ShowTicket58"
                     class="col text-center font-weight-bold mt-3">

                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('ticket_58')">
                        <i class="fa fa-receipt"></i>
                    </button>
                    <p>58MM</p>
                </div>

                <div v-if="ShowTicket50"
                     class="col text-center font-weight-bold mt-3">

                    <el-popover
                        placement="top-start"
                        :open-delay="1000"
                        width="145"
                        trigger="hover"
                        content="Presiona ALT + P">
                        <el-button slot="reference"
                                   class="btn btn-lg btn-info waves-effect waves-light"
                                   type="button"
                                   @click="clickPrint('ticket_50')">
                            <i class="fa fa-receipt"></i>
                        </el-button>
                    </el-popover>
                    <p>50MM</p>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>
import {mapState, mapActions} from "vuex/dist/vuex.mjs";

export default {
    props: ['showDialogOptions', 'recordId', 'showClose', 'isUpdate', 'configuration', 'type'],
    components: {
    },
    data() {
        return {
            titleDialog: null,
            loading: false,
            resource: 'unpaid',
            errors: {},
            form: {},
            company: {},
            resource_document:null,
        }
    },
    created() {
        this.loadConfiguration(this.$store)
        this.$store.commit('setConfiguration', this.configuration)

    },
    mounted() {
        this.initForm()
    },
    computed: {
        ...mapState([
            'config',
        ]),
        ShowTicket58: function () {
            if (this.config === undefined) return false;
            if (this.config == null) return false;
            if (this.config.show_ticket_58 === undefined) return false;
            if (this.config.show_ticket_58 == null) return false;
            if (
                this.config.show_ticket_58 !== undefined &&
                this.config.show_ticket_58 !== null) {
                return this.config.show_ticket_58;
            }
            return false;
        },
        ShowTicket80: function () {
            if (this.config === undefined) return false;
            if (this.config == null) return false;
            if (this.config.show_ticket_80 === undefined) return false;
            if (this.config.show_ticket_80 == null) return false;
            if (
                this.config.show_ticket_80 !== undefined &&
                this.config.show_ticket_80 !== null) {
                return this.config.show_ticket_80;
            }
            return false;
        },
        ShowTicket50: function () {
            if (this.config === undefined) return false;
            if (this.config == null) return false;
            if (this.config.show_ticket_50 === undefined) return false;
            if (this.config.show_ticket_50 == null) return false;
            if (
                this.config.show_ticket_50 !== undefined &&
                this.config.show_ticket_50 !== null) {
                return this.config.show_ticket_50;
            }
            return false;
        }
    },
    methods: {
        ...mapActions(['loadConfiguration']),
        initForm() {
            this.errors = {};
            this.form = {
                download_pdf: null,
                external_id: null,
                number: null,
                id: null,
                response_message: null,
                response_type: null,
                group_id: null,
            };
            this.company = {
                soap_type_id: null,
            }
        },
        async create() {

            await this.getCompany()
            await this.getRecord()

        },
        async getCompany() {
            this.loading = true;
            await this.$http.get(`/companies/record`)
                .then(response => {
                    if (response.data !== '') {
                        this.company = response.data.data
                    }
                }).finally(() => this.loading = false);
        },
        async getRecord() {
            this.loading = true;
            if (this.type=='sale') {
                this.resource_document='sale-notes';
            } else {
                this.resource_document='documents'
            }
            await this.$http.get(`/${this.resource_document}/record/${this.recordId}`).then(response => {
                this.form = response.data.data;
                this.titleDialog = 'Comprobante de documento por cobrar: ' + this.form.number;
            }).finally(() => {
                this.loading = false
            });
        },
        clickPrint(format) {
            window.open(`${this.resource}/print/${this.form.external_id}/${this.type}/${format}`, '_blank');
        },
        clickCloseUnpaid() {
            this.$emit('update:showDialogOptions', false)
            this.initForm()
        },
        closeUnpaid() {
            this.$emit('update:showDialogOptions', false);
            // this.initDocumentTypes()
            // this.initForm()
        },
    }
}
</script>
