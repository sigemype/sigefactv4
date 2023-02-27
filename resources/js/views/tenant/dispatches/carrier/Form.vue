<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Nueva G.R. Transportista</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off"
                  @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.establishment}"
                                 class="form-group">
                                <label class="control-label">Establecimiento<span class="text-danger"> *</span></label>
                                <el-select v-model="form.establishment_id"
                                           @change="changeEstablishment">
                                    <el-option v-for="option in establishments"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.establishment"
                                       class="form-control-feedback"
                                       v-text="errors.establishment[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.series}"
                                 class="form-group">
                                <label class="control-label">Serie<span class="text-danger"> *</span></label>
                                <el-select v-model="form.series" :disabled="generalDisabledSeries()">
                                    <el-option v-for="option in series"
                                               :key="option.number"
                                               :label="option.number"
                                               :value="option.number"></el-option>
                                </el-select>
                                <small v-if="errors.series"
                                       class="form-control-feedback"
                                       v-text="errors.series[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.date_of_issue}"
                                 class="form-group">
                                <label class="control-label">Fecha de emisión<span class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_issue"
                                                :clearable="false"
                                                type="date"
                                                value-format="yyyy-MM-dd"></el-date-picker>
                                <small v-if="errors.date_of_issue"
                                       class="form-control-feedback"
                                       v-text="errors.date_of_issue[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.date_of_shipping}"
                                 class="form-group">
                                <label class="control-label">Fecha de traslado<span
                                    class="text-danger"> *</span></label>
                                <el-date-picker v-model="form.date_of_shipping"
                                                :clearable="false"
                                                type="date"
                                                value-format="yyyy-MM-dd"></el-date-picker>
                                <small v-if="errors.date_of_shipping"
                                       class="form-control-feedback"
                                       v-text="errors.date_of_shipping[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.unit_type_id}"
                                 class="form-group">
                                <label class="control-label">Unidad de medida<span class="text-danger"> *</span></label>
                                <el-select v-model="form.unit_type_id">
                                    <el-option v-for="option in unitTypes"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.unit_type_id"
                                       class="form-control-feedback"
                                       v-text="errors.unit_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.total_weight}"
                                 class="form-group">
                                <label class="control-label">Peso total<span class="text-danger"> *</span></label>
                                <el-input-number v-model="form.total_weight"
                                                 :max="9999999999"
                                                 :min="0"
                                                 :precision="2"
                                                 :step="1"></el-input-number>
                                <small v-if="errors.total_weight"
                                       class="form-control-feedback"
                                       v-text="errors.total_weight[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
<!--                        <div class="col-lg-2">-->
<!--                            <div :class="{'has-danger': errors.packages_number}"-->
<!--                                 class="form-group">-->
<!--                                <label class="control-label">Número de-->
<!--                                    paquetes-->
<!--                                    &lt;!&ndash; <span class="text-danger"> *</span> &ndash;&gt;-->
<!--                                </label>-->
<!--                                <el-input-number v-model="form.packages_number"-->
<!--                                                 :max="9999999999"-->
<!--                                                 :min="0"-->
<!--                                                 :precision="0"-->
<!--                                                 :step="1"></el-input-number>-->
<!--                                <small v-if="errors.packages_number"-->
<!--                                       class="form-control-feedback"-->
<!--                                       v-text="errors.packages_number[0]"></small>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-lg-6">
                            <div :class="{'has-danger': errors.observations}"
                                 class="form-group">
                                <label class="control-label">Observaciones</label>
                                <el-input v-model="form.observations"
                                          :rows="3"
                                          maxlength="250"
                                          placeholder="Observaciones..."
                                          type="textarea"></el-input>
                                <small v-if="errors.observations"
                                       class="form-control-feedback"
                                       v-text="errors.observations[0]"></small>
                            </div>
                        </div>
<!--                        <div class="col-lg-2" v-if="!order_form_id">-->
<!--                            <div :class="{'has-danger': errors.order_form_external}"-->
<!--                                 class="form-group">-->
<!--                                <label class="control-label">Orden de pedido-->
<!--                                    <el-tooltip class="item"-->
<!--                                                content="Pedidos externos"-->
<!--                                                effect="dark"-->
<!--                                                placement="top">-->
<!--                                        <i class="fa fa-info-circle"></i>-->
<!--                                    </el-tooltip>-->
<!--                                </label>-->
<!--                                <el-input v-model="form.order_form_external"></el-input>-->
<!--                                <small v-if="errors.order_form_external" class="form-control-feedback"-->
<!--                                       v-text="errors.order_form_external[0]"></small>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div :class="{'has-danger': errors.sender_id}"
                                 class="form-group">
                                <label class="control-label">
                                    Remitente<span class="text-danger"> *</span>
                                    <a href="#"
                                       @click.prevent="showDialogSenderForm = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.sender_id"
                                           :loading="loading_search"
                                           :remote-method="searchRemoteSenders"
                                           filterable
                                           placeholder="Escriba el nombre o número de documento del remitente"
                                           remote
                                           @change="changeSender">
                                    <el-option v-for="option in senders"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.sender_id"
                                       class="form-control-feedback"
                                       v-text="errors.sender_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div :class="{'has-danger': errors.sender_address_id}"
                                 class="form-group">
                                <label class="control-label">Punto de partida<span class="text-danger"> *</span>
                                    <a v-if="form.sender_id"
                                       href="#"
                                       @click.prevent="showDialogSenderAddressForm = true">[+ Nuevo]</a></label>
                                <el-select v-model="form.sender_address_id"
                                           placeholder="Seleccionar punto de partida">
                                    <el-option v-for="option in sender_addresses"
                                               :key="option.id"
                                               :label="option.address"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.sender_address_id"
                                       class="form-control-feedback"
                                       v-text="errors.sender_address_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div :class="{'has-danger': errors.receiver_id}"
                                 class="form-group">
                                <label class="control-label">
                                    Destinatario<span class="text-danger"> *</span>
                                    <a href="#"
                                       @click.prevent="showDialogReceiverForm = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.receiver_id"
                                           :loading="loading_search"
                                           :remote-method="searchRemoteReceivers"
                                           filterable
                                           placeholder="Escriba el nombre o número de documento del destinatario"
                                           popper-class="el-select-customers"
                                           remote
                                           @change="changeReceiver">
                                    <el-option v-for="option in receivers"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.receiver_id"
                                       class="form-control-feedback"
                                       v-text="errors.receiver_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div :class="{'has-danger': errors.receiver_address_id}"
                                 class="form-group">
                                <label class="control-label">Punto de llegada<span class="text-danger"> *</span>
                                    <a v-if="form.receiver_id"
                                       href="#"
                                       @click.prevent="showDialogReceiverAddressForm = true">[+ Nuevo]</a></label>
                                <el-select v-model="form.receiver_address_id"
                                           placeholder="Seleccionar punto de llegada">
                                    <el-option v-for="option in receiver_addresses"
                                               :key="option.id"
                                               :label="option.address"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.receiver_address_id"
                                       class="form-control-feedback"
                                       v-text="errors.receiver_address_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div :class="{'has-danger': errors.transport_id}"
                                 class="form-group">
                                <label class="control-label">Datos del vehículo
                                    <a v-if="can_add_new_product"
                                       href="#"
                                       @click.prevent="showDialogTransportForm = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.transport_id"
                                           clearable
                                           placeholder="Seleccionar vehículo">
                                    <el-option
                                        v-for="option in transports"
                                        :key="option.id"
                                        :label="option.plate_number +' - '+ option.model+' - '+ option.brand"
                                        :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.transport_id"
                                       class="form-control-feedback"
                                       v-text="errors.transport_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="control-label">
                                Datos del conductor
                                <a v-if="can_add_new_product"
                                   href="#"
                                   @click.prevent="showDialogDriverForm = true">[+ Nuevo]</a>
                            </label>
                            <span class="text-danger"> *</span>
                            <div :class="{'has-danger': errors.driver_id}"
                                 class="form-group">
                                <el-select v-model="form.driver_id"
                                           clearable
                                           placeholder="Seleccionar conductor">
                                    <el-option
                                        v-for="option in drivers"
                                        :key="option.id"
                                        :label="option.number +' - '+ option.name+' - '+ option.license"
                                        :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.dispacher"
                                       class="form-control-feedback"
                                       v-text="errors.dispacher[0]"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="font-weight-bold">Unidad</th>
                                    <th class="font-weight-bold">Descripción</th>
                                    <th class="text-right font-weight-bold">Cantidad</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row, index) in form.items">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ row.unit_type_id }}</td>
                                    <td>{{ row.description }}</td>
                                    <td class="text-right">{{ getFormatQuantity(row.quantity) }}</td>
                                    <!-- <td class="text-right">{{ row.quantity }}</td> -->
                                    <td class="text-right">
                                        <button class="btn waves-effect waves-light btn-xs btn-danger"
                                                type="button"
                                                @click.prevent="clickRemoveItem(index)">x
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="text-right hidden-sm-down"
                                        colspan="2">
                                        <label class="control-label">
                                            Producto
                                            <a v-if="can_add_new_product"
                                               href="#"
                                               @click.prevent="showDialogNewItem = true"
                                            >[+ Nuevo]</a>
                                        </label>
                                    </td>
                                    <td class="hidden-sm-down"
                                        colspan="2">
                                        <div class="row">
                                            <div class="col-8">
                                                <div :class="{'has-danger': errors.items}"
                                                     class="form-group" id="custom-select">

                                                    <el-input id="custom-input">

                                                        <el-select v-model="current_item"
                                                                   id="select-width"
                                                                   :loading="loading_search"
                                                                   :remote-method="searchRemoteItems"
                                                                   popper-class="el-select-items"
                                                                   filterable
                                                                   remote
                                                                   ref="selectItem"
                                                                   slot="prepend"
                                                                   @change="onChangeItem">

                                                            <el-option
                                                                v-for="option in items"
                                                                :key="option.id"
                                                                :label="option.full_description"
                                                                :value="option.id"></el-option>
                                                        </el-select>

                                                        <el-tooltip
                                                            slot="append"
                                                            class="item"
                                                            content="Ver Stock del Producto"
                                                            effect="dark"
                                                            placement="bottom">
                                                            <el-button
                                                                @click.prevent="clickWarehouseDetail()">
                                                                <i class="fa fa-search"></i>
                                                            </el-button>
                                                        </el-tooltip>

                                                    </el-input>

                                                    <small v-if="errors.items"
                                                           class="form-control-feedback"
                                                           v-text="errors.items[0]"></small>
                                                </div>
                                                <template v-if="item">
                                                    <div v-if="item.lots_enabled && item.lots_group.length > 0"
                                                         class="col-12 mt-2">
                                                        <a class="text-center font-weight-bold text-info"
                                                           href="#"
                                                           @click.prevent="clickLotGroup">
                                                            [&#10004; Seleccionar lote]
                                                        </a>
                                                    </div>
                                                </template>
                                                <!-- Selector para item -->
                                            </div>
                                            <div class="col-4">
                                                <!-- Aqui colocar cantidad -->
                                                <div :class="{'has-danger': errors.quantity}"
                                                     class="form-group">
                                                    <!--
                                                    <label class="control-label">Cantidad</label>
                                                    -->
                                                    <el-input-number
                                                        v-model="quantity"
                                                        :max="99999999"
                                                        :min="min_qty"
                                                        :precision="4"
                                                        :step="1"
                                                        placeholder="Cantidad"></el-input-number>
                                                    <small v-if="errors.quantity"
                                                           class="form-control-feedback"
                                                           v-text="errors.quantity[0]"></small>
                                                </div>
                                                <!-- Aqui colocar cantidad -->
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right hidden-sm-down">
                                        <el-button style="width:100%"
                                                   type="primary"
                                                   @click="addAItemInRow">Agregar
                                        </el-button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center hidden-md-up"
                                        colspan="5">
                                        <button class="btn waves-effect waves-light btn-primary"
                                                type="button"
                                                @click.prevent="showDialogAddItems = true">+ Agregar Producto
                                        </button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12"></div>
                    <div class="form-actions text-right mt-4">
                        <el-button @click.prevent="clickClose">Cancelar</el-button>
                        <el-button v-if="(form.items.length > 0)"
                                   :loading="loading_submit"
                                   native-type="submit"
                                   type="primary">Generar
                        </el-button>
                    </div>
                </div>
            </form>
        </div>

        <sender-form :showDialog.sync="showDialogSenderForm"
                     title="Nuevo Remitente"
                     @success="successSender"></sender-form>

        <receiver-form :showDialog.sync="showDialogReceiverForm"
                       title="Nuevo Destinatario"
                       @success="successReceiver"></receiver-form>

        <sender-address-form :showDialog.sync="showDialogSenderAddressForm"
                             :person-id="form.sender_id"
                             title="Nuevo punto de partida"
                             @success="successSenderAddress"></sender-address-form>

        <receiver-address-form :showDialog.sync="showDialogReceiverAddressForm"
                               title="Nuevo punto de llegada"
                               :person-id="form.receiver_id"
                               @success="successReceiverAddress"></receiver-address-form>

        <driver-form :showDialog.sync="showDialogDriverForm"
                     @success="successDriver"></driver-form>

        <transport-form :showDialog.sync="showDialogTransportForm"
                        @success="successTransport"></transport-form>

        <items
            :dialogVisible.sync="showDialogAddItems"
            @addItem="addItem"></items>

        <dispatch-finish :recordId="recordId"
                         :showClose="false"
                         :send-sunat="send_sunat"
                         :showDialog.sync="showDialogFinish"></dispatch-finish>
        <item-form :external="true"
                   :showDialog.sync="showDialogNewItem"></item-form>
        <lots-group
            v-if="item"
            :lots_group="item.lots_group"
            :quantity="quantity"
            :showDialog.sync="showDialogLots"
            @addRowLotGroup="addRowLotGroup">
        </lots-group>

        <warehouses-detail
            :showDialog.sync="showWarehousesDetail"
            :warehouses="warehousesDetail">
        </warehouses-detail>

    </div>
</template>

<script>
import PersonForm from '../../persons/form.vue';
import Items from '../items.vue';
import itemForm from '../../items/form.vue';
import LotsGroup from '../../documents/partials/lots_group.vue';
import DriverForm from '../drivers/form.vue';
import TransportForm from '../transports/form.vue';
import SenderForm from '../partials/DispatchPersonForm';
import ReceiverForm from '../partials/DispatchPersonForm';
import SenderAddressForm from '../partials/DispatchAddressForm';
import ReceiverAddressForm from '../partials/DispatchAddressForm';

import DispatchFinish from '../partials/finish'
import {mapActions, mapState} from "vuex/dist/vuex.mjs";
import WarehousesDetail from '@components/WarehousesDetail.vue'
import {setDefaultSeriesByMultipleDocumentTypes} from '@mixins/functions'

export default {
    props: [
        'parentTable',
        'parentId',
        'document',
        'documentItems',
        'order_form_id',
        'configuration',
        'authUser',
    ],
    components: {
        itemForm,
        LotsGroup,
        PersonForm,
        Items,
        DispatchFinish,
        WarehousesDetail,
        DriverForm,
        TransportForm,
        SenderForm,
        ReceiverForm,
        SenderAddressForm,
        ReceiverAddressForm
    },
    mixins: [setDefaultSeriesByMultipleDocumentTypes],
    computed: {
        ...mapState([
            'config',
            'item',
            'items',
            'all_items',
        ]),
    },
    data() {
        return {
            can_add_new_product: false,
            showDialogNewItem: false,
            showDialogAddItems: false,
            showDialogFinish: false,
            showDialogSenderForm: false,
            showDialogReceiverForm: false,
            showDialogSenderAddressForm: false,
            showDialogReceiverAddressForm: false,
            showDialogDriverForm: false,
            showDialogTransportForm: false,
            IdLoteSelected: false,
            showDialogLots: false,
            min_qty: 0.0001,
            input_person: {},
            identityDocumentTypes: [],
            related_document_types: [],
            resource: 'dispatch_carrier',
            loading_submit: false,
            establishments: [],
            drivers: [],
            driver: null,
            countries: [],
            seriesAll: [],
            unitTypes: [],
            all_customers: [],
            loading_search: false,
            search_item_by_barcode: false,
            customers: [],
            code: null,
            locations: [],
            series: [],
            current_item: null,
            quantity: 1,
            errors: {},
            form: {},
            recordId: null,
            company: {},
            customerAddresses: [],
            showWarehousesDetail: false,
            warehousesDetail: [],
            transports: [],
            send_sunat: false,
            senders: [],
            sender_addresses: [],
            receivers: [],
            receiver_addresses: [],
        }
    },
    created() {
        this.initForm();
        this.loadConfiguration()
        this.$store.commit('setConfiguration', this.configuration)
        this.canCreateProduct();
    },
    async mounted() {
        const itemsFromSummary = localStorage.getItem('items');
        const payload = {}
        if (itemsFromSummary) {
            const items = JSON.parse(itemsFromSummary);
            payload.itemIds = items.map(i => i.id);
        }
        await this.$http.post(`/${this.resource}/tables`, payload).then(response => {
            this.company = response.data.company;
            this.identityDocumentTypes = response.data.identityDocumentTypes;
            this.related_document_types = response.data.related_document_types
            this.establishments = response.data.establishments;
            this.unitTypes = response.data.unitTypes;
            this.all_customers = [];
            this.countries = response.data.countries;
            this.locations = response.data.locations;
            this.seriesAll = response.data.series;
            this.drivers = response.data.drivers;
            this.transports = response.data.transports;
            // this.senders = response.data.senders;
            // this.receivers = response.data.receivers;
            if (itemsFromSummary) {
                this.onLoadItemsFromSummary(response.data.itemsFromSummary, JSON.parse(itemsFromSummary));
            }
        });

        if (this.parentId) {
            this.form = Object.assign({}, this.form, this.document);
            await this.reloadDataCustomers(this.form.customer_id);
            await this.getDeliveryAddresses(this.form.customer_id);
            await this.changeEstablishment()
            if (this.parentTable !== 'dispatches') {
                this.setDefaults();
            }
        } else {
            await this.searchRemoteSenders('')
            await this.searchRemoteReceivers('')
            if (this.establishments.length > 0) {
                this.form.establishment_id = _.head(this.establishments).id;
            }
            await this.changeEstablishment()
            this.setDefaults();
        }
        this.$eventHub.$on('reloadDataPersons', (customer_id) => {
            this.reloadDataCustomers(customer_id)
        })
        this.$eventHub.$on('initInputPerson', () => {
            this.initInputPerson()
        });
    },
    methods: {
        ...mapActions([
            'loadItems',
            'loadConfiguration',
        ]),
        initForm() {
            this.errors = {}
            let customer_id = parseInt(this.config.establishment.customer_id);
            let establishment_id = parseInt(this.config.establishment.id);
            if (isNaN(customer_id)) customer_id = null;
            if (isNaN(establishment_id)) establishment_id = null;
            this.form = {
                id: null,
                establishment_id: establishment_id,
                document_type_id: '31',
                series: null,
                number: '#',
                date_of_issue: moment().format('YYYY-MM-DD'),
                time_of_issue: moment().format('HH:mm:ss'),
                date_of_shipping: moment().format('YYYY-MM-DD'),
                customer_id: customer_id,
                observations: '',
                transshipment_indicator: false,
                port_code: null,
                unit_type_id: 'KGM',
                total_weight: 1,
                packages_number: 1,
                container_number: null,
                driver_id: null,
                driver: {},
                transport_id: null,
                transport: {},
                items: [],
                reference_order_form_id: null,
                // license_plate: null,
                secondary_license_plates: {
                    semitrailer: null
                },
                related: {},
                order_form_external: null,
                terms_condition: null,

                sender_id: null,
                sender_data: {},
                sender_address_id: null,
                sender_address_data: {},
                receiver_id: null,
                receiver_data: {},
                receiver_address_id: null,
                receiver_address_data: {},
            }
        },
        setDefaults() {
            if (this.drivers.length > 0) {
                let driver = _.find(this.drivers, {'is_default': true});
                this.form.driver_id = (driver) ? driver.id : _.head(this.drivers).id;
            }
            if (this.transports.length > 0) {
                let transport = _.find(this.transports, {'is_default': true});
                this.form.transport_id = (transport) ? transport.id : _.head(this.transports).id;
            }
        },
        clickWarehouseDetail() {
            if (!this.current_item) {
                return this.$message.error('Seleccione un producto');
            }
            const item = _.find(this.items, {'id': this.current_item});
            this.warehousesDetail = item.warehouses
            this.showWarehousesDetail = true
        },
        getFormatQuantity(quantity) {
            return _.round(quantity, 4)
        },
        canCreateProduct() {
            if (this.config.typeUser === 'admin') {
                this.can_add_new_product = true
            } else if (this.config.typeUser === 'seller' && this.config.seller_can_create_product !== undefined) {
                this.can_add_new_product = this.config.seller_can_create_product;
            }
            return this.can_add_new_product;
        },
        getAllItems() {
            this.$http.post(`/${this.resource}/tables`).then(response => {
                this.all_items = this.items
                this.$store.commit('setItems', response.data.items)
                this.$store.commit('setAllItems', response.data.items)
            });
        },
        addRowLotGroup(id) {
            this.IdLoteSelected = id;
        },
        clickLotGroup() {
            this.showDialogLots = true
        },
        async searchRemoteItems(input) {
            if (input.length > 2) {
                this.loading_search = true
                const params = {
                    'input': input,
                    'search_by_barcode': this.search_item_by_barcode ? 1 : 0
                }
                await this.$http.get(`/documents/search-items`, {params})
                    .then(response => {
                        // this.items = response.data.items
                        this.$store.commit('setItems', response.data.items)
                        this.loading_search = false
                        // this.enabledSearchItemsBarcode()
                        if (this.items.length == 0) {
                            this.filterItems()
                        }
                    })
            } else {
                await this.filterItems()
            }
        },
        onChangeItem() {
            this.IdLoteSelected = null;
            this.$store.commit('setItem', this.items.find(it => it.id == this.current_item))
        },
        filterItems() {
            this.$store.commit('setItems', this.all_items)
        },
        addAItemInRow() {
            this.errors = {};
            if (this.item.lots_enabled) {
                if (!this.IdLoteSelected)
                    return this.$message.error('Debe seleccionar un lote.');
            }

            if ((this.current_item != null) && (this.quantity != null)) {
                this.quantity = Math.abs(this.quantity)
                if (isNaN(this.quantity)) {
                    this.quantity = 1;
                }
                const item = this.items.find((item) => item.id == this.current_item)
                item.IdLoteSelected = this.IdLoteSelected;

                this.addItem({
                    item: item,
                    quantity: this.quantity,
                })
                this.$store.commit('setItem', {})
                this.quantity = 1
                this.focusDescription()
                return null;
            }

            if (this.current_item == null) {
                this.$set(this.errors, 'items', ['Seleccione el producto']);
            }

            if (this.quantity == null) {
                this.$set(this.errors, 'quantity', ['Digite la cantidad']);
            }

            this.IdLoteSelected = null;
        },
        onLoadItemsFromSummary(items, itemsFromStorage) {
            items.map(it => {
                const quantityByItems = _.sumBy(itemsFromStorage.filter(i => i.id == it.id), function (row) {
                    return parseFloat(row.quantity)
                })
                if (quantityByItems) {
                    this.addItem({
                        item: it,
                        quantity: quantityByItems
                    });
                }
            });
            localStorage.removeItem('items');
        },
        async changeEstablishment() {
            if (this.form.establishment_id) {
                this.series = _.filter(this.seriesAll, {
                    'establishment_id': this.form.establishment_id,
                    'document_type_id': this.form.document_type_id
                });
                if(this.series.length > 0) {
                    this.form.series = this.series[0].number;
                }
            }
        },
        addItem(form) {
            let it = form.item;
            let qty = form.quantity;
            let exist = this.form.items.find((item) => item.id == it.id);
            let attributes = null
            if (it.attributes) {
                attributes = it.attributes
                this.incrementValueAttr(form)
            }
            if (exist) {
                exist.quantity += form.quantity;
                return;
            }
            let lot_group = null;
            if (it.IdLoteSelected) {
                lot_group = it.lots_group.find(l => l.id == it.IdLoteSelected);
            }
            this.form.items.push({
                attributes: attributes,
                description: it.description,
                internal_id: it.internal_id,
                quantity: form.quantity,
                item_id: it.id,
                unit_type_id: it.unit_type_id,
                id: it.id,
                IdLoteSelected: it.IdLoteSelected || '',
                lot_group: lot_group || null,
            });
        },
        keyupCustomer() {
            if (this.input_person.number) {
                if (!isNaN(parseInt(this.input_person.number))) {
                    switch (this.input_person.number.length) {
                        case 8:
                            this.input_person.identity_document_type_id = '1'
                            this.showDialogNewPerson = true
                            break;
                        case 11:
                            this.input_person.identity_document_type_id = '6'
                            this.showDialogNewPerson = true
                            break;
                        default:
                            this.input_person.identity_document_type_id = '6'
                            this.showDialogNewPerson = true
                            break;
                    }
                }
            }
        },
        decrementValueAttr(form) {
            let it = form
            let attrib = it.attributes
            let qty = parseFloat(it.quantity)
            this.form.packages_number -= qty
            let total_weight = 0
            if (attrib) {
                for (const [key, value] of Object.entries(attrib)) {
                    if (key === 'attributes' && value !== null) {
                        let attr = JSON.parse(value)
                        if (attr !== null) {
                            attr.forEach(attr => {
                                if (attr.attribute_type_id === '5032') {
                                    total_weight -= parseFloat(attr.value) * qty
                                }
                            });
                        }
                    }
                }
            }
            this.form.total_weight += total_weight
        },
        incrementValueAttr(form) {
            let qty = parseFloat(form.quantity)
            let it = form.item
            let attrib = it.attributes
            this.form.packages_number += qty
            let total_weight = 0
            if (attrib) {
                for (const [key, value] of Object.entries(attrib)) {
                    if (key === 'attributes' && value !== null) {
                        let attr = JSON.parse(value)
                        if (attr !== null) {
                            attr.forEach(attr => {
                                if (attr.attribute_type_id === '5032') {
                                    total_weight += parseFloat(attr.value) * qty
                                }
                            });
                        }
                    }
                }
            }
            this.form.total_weight += total_weight
        },
        clickRemoveItem(index) {
            this.decrementValueAttr(this.form.items[index])
            this.form.items.splice(index, 1);
        },
        async submit() {
            if (this.config.affect_all_documents) {
                this.form.terms_condition = this.config.terms_condition_sale;
            }
            if (!this.form.sender_id) {
                return this.$message.error('El remitente es requerido')
            }
            if (!this.form.receiver_id) {
                return this.$message.error('El destinatario es requerido')
            }
            if (!this.form.driver_id) {
                return this.$message.error('El conductor es requerido')
            }
            if (!this.form.transport_id) {
                return this.$message.error('El vehículo es requerido')
            }

            const validateQuantity = await this.verifyQuantityItems()
            if (!validateQuantity.validate) {
                return this.$message.error('Los productos no pueden tener cantidad 0.')
            }

            this.form.driver = _.find(this.drivers, {'id': this.form.driver_id});
            this.form.transport = _.find(this.transports, {'id': this.form.transport_id});
            this.form.sender_data = _.find(this.senders, {'id': this.form.sender_id});
            this.form.receiver_data = _.find(this.receivers, {'id': this.form.receiver_id});
            this.form.sender_address_data = _.find(this.sender_addresses, {'id': this.form.sender_address_id});
            this.form.receiver_address_data = _.find(this.receiver_addresses, {'id': this.form.receiver_address_id});


            if (this.form.driver.identity_document_type_id === '' || _.isNull(this.form.driver.identity_document_type_id)) {
                return this.$message.error('El tipo de documento del conductor es requerido')
            }
            if (this.form.driver.number === '' || _.isNull(this.form.driver.number)) {
                return this.$message.error('El número del conductor es requerido')
            }
            if (this.form.driver.name === '' || _.isNull(this.form.driver.name)) {
                return this.$message.error('El nombre del conductor es requerido')
            }
            if (this.form.driver.license === '' || _.isNull(this.form.driver.license)) {
                return this.$message.error('La licencia del conductor es requerido')
            }
            this.loading_submit = true;
            this.$http.post(`/${this.resource}`, this.form).then(response => {
                if (response.data.success) {
                    this.initForm();
                    this.recordId = response.data.data.id
                    this.send_sunat = response.data.data.send_sunat
                    this.showDialogFinish = true
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(error => {
                this.loading_submit = false;

                if (error.response.status === 422) {
                    this.errors = error.response.data;
                } else {
                    this.$message.error(error.response.data.message);
                }
            }).then(() => {
                // this.setDefaultCustomer();
                this.loading_submit = false;
            });
        },
        verifyQuantityItems() {
            let validate = true
            let v = 0;
            this.form.items.forEach((element) => {
                v = parseFloat(element.quantity);
                if (isNaN(v)) {
                    validate = false
                } else if (v < this.min_qty) {
                    validate = false
                }
            })
            return {validate}
        },
        focusDescription() {
            this.$refs.selectItem.$el.getElementsByTagName('input')[0].focus()
        },
        initInputPerson() {
            this.input_person = {
                number: null,
                identity_document_type_id: null
            }
        },
        async successDriver(id) {
            this.form.driver_id = id;
            await this.$http.get(`/drivers/get_options`)
                .then(response => {
                    this.drivers = response.data;
                });
        },
        async successTransport(id) {
            this.form.transport_id = id;
            await this.$http.get(`/transports/get_options`)
                .then(response => {
                    this.transports = response.data;
                });
        },
        async reloadDataSenders(customer_id) {
            await this.$http.get(`/documents/search/customer/${customer_id}`).then((response) => {
                this.senders = response.data.senders
            })
        },
        async reloadDataReceivers(customer_id) {
            await this.$http.get(`/documents/search/customer/${customer_id}`).then((response) => {
                this.receivers = response.data.receivers
            })
        },
        async changeSender() {
            this.form.sender_address_id = null;
            this.sender_addresses = [];
            await this.getSenderAddresses(this.form.sender_id);
            if (this.sender_addresses.length > 0) {
                this.form.sender_address_id = _.head(this.sender_addresses).id;
            }
        },
        async changeReceiver() {
            this.form.receiver_address_id = null;
            await this.getReceiverAddresses(this.form.receiver_id);
            if (this.receiver_addresses.length > 0) {
                this.form.receiver_address_id = _.head(this.receiver_addresses).id;
            }
        },
        async successSender(data) {
            this.form.sender_id = data['person_id'];
            await this.$http.get(`/dispatch_persons/get_options`)
                .then(response => {
                    this.senders = response.data;
                });
            await this.successSenderAddress(data['address_id'])
        },
        async successReceiver(data) {
            this.form.receiver_id = data['person_id'];
            await this.$http.get(`/dispatch_persons/get_options`)
                .then(response => {
                    this.receivers = response.data;
                });
            await this.successReceiverAddress(data['address_id'])
        },
        async successSenderAddress(id) {
            this.form.sender_address_id = id;
            await this.getSenderAddresses(this.form.sender_id);
        },
        async successReceiverAddress(id) {
            this.form.receiver_address_id = id;
            await this.getReceiverAddresses(this.form.receiver_id);
        },
        async getSenderAddresses(sender_id) {
            await this.$http.get(`/dispatch_addresses/get_options/${sender_id}`)
                .then(response => {
                    this.sender_addresses = response.data;
                });
        },
        async getReceiverAddresses(receiver_id) {
            await this.$http.get(`/dispatch_addresses/get_options/${receiver_id}`)
                .then(response => {
                    this.receiver_addresses = response.data;
                });
        },
        async searchRemoteSenders(input) {
            this.loading_search = true
            await this.$http.post(`/dispatch_persons/get_filter_options`, {
                'input': input,
            })
                .then(response => {
                    this.senders = response.data
                })
            this.loading_search = false
        },
        async searchRemoteReceivers(input) {
            this.loading_search = true
            await this.$http.post(`/dispatch_persons/get_filter_options`, {
                'input': input,
            })
                .then(response => {
                    this.receivers = response.data
                })
            this.loading_search = false
        },
        clickClose() {
            location.href = '/dispatches';
        },
    }
}
</script>
