<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header bg-info">
            <h3 class="my-0">Nueva Guía de Remisión</h3>
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
                        <div class="col-lg-4">
                            <div :class="{'has-danger': errors.customer_id}"
                                 class="form-group">
                                <label class="control-label">
                                    Cliente<span class="text-danger"> *</span>
                                    <a href="#"
                                       @click.prevent="showDialogNewPerson = true">[+ Nuevo]</a>
                                </label>
                                <el-select v-model="form.customer_id"
                                           :loading="loading_search"
                                           :remote-method="searchRemoteCustomers"
                                           filterable
                                           placeholder="Escriba el nombre o número de documento del cliente"
                                           popper-class="el-select-customers"
                                           remote
                                           @change="changeCustomer"
                                           @keyup.enter.native="keyupCustomer">
                                    <el-option v-for="option in customers"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.customer_id"
                                       class="form-control-feedback"
                                       v-text="errors.customer_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.transport_mode_type_id}"
                                 class="form-group">
                                <label class="control-label">Modo de traslado<span class="text-danger"> *</span></label>
                                <el-select v-model="form.transport_mode_type_id">
                                    <el-option v-for="option in transportModeTypes"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.transport_mode_type_id"
                                       class="form-control-feedback"
                                       v-text="errors.transport_mode_type_id[0]"></small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div :class="{'has-danger': errors.transfer_reason_type_id}"
                                 class="form-group">
                                <label class="control-label">Motivo de traslado<span
                                    class="text-danger"> *</span></label>
                                <el-select v-model="form.transfer_reason_type_id"
                                           @change="changeTransferReasonType">
                                    <el-option v-for="option in transferReasonTypes"
                                               :key="option.id"
                                               :label="option.description"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.transfer_reason_type_id"
                                       class="form-control-feedback"
                                       v-text="errors.transfer_reason_type_id[0]"></small>
                            </div>
                        </div>
                        <!-- numero de DAM -->
                        <template v-if="form.transfer_reason_type_id === '09'">
                            <div class="col-lg-3">
                                <div :class="{'has-danger': errors['related.number']}"
                                     class="form-group">
                                    <label class="control-label">Número de documento (DAM)
                                        <el-tooltip class="item"
                                                    content="Formato del campo: XXXX-XX-XXX-XXXXXX, Ejemplo: 0001-01-002-001234"
                                                    effect="dark"
                                                    placement="top">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                        <span class="text-danger"> *</span>
                                    </label>
                                    <el-input v-model="form.related.number" placeholder="0001-01-002-001234"></el-input>
                                    <small v-if="errors['related.number']" class="form-control-feedback"
                                           v-text="errors['related.number'][0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div :class="{'has-danger': errors['related.document_type_id']}"
                                     class="form-group">
                                    <label class="control-label">Tipo documento relacionado<span
                                        class="text-danger"> *</span></label>
                                    <el-select v-model="form.related.document_type_id" disabled>
                                        <el-option v-for="option in related_document_types"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors['related.document_type_id']" class="form-control-feedback"
                                           v-text="errors['related.document_type_id'][0]"></small>
                                </div>
                            </div>
                        </template>
                        <div :class="form.transfer_reason_type_id === '09' ? 'col-lg-12' : 'col-lg-6'">
                            <div :class="{'has-danger': errors.transfer_reason_description}"
                                 class="form-group">
                                <label class="control-label">Descripción de motivo de traslado</label>
                                <el-input v-model="form.transfer_reason_description"
                                          :rows="3"
                                          maxlength="100"
                                          placeholder="Descripción de motivo de traslado..."
                                          type="textarea"></el-input>
                                <small v-if="errors.transfer_reason_description"
                                       class="form-control-feedback"
                                       v-text="errors.transfer_reason_description[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-lg-2">
                            <div :class="{'has-danger': errors.packages_number}"
                                 class="form-group">
                                <label class="control-label">Número de
                                    paquetes
                                    <!-- <span class="text-danger"> *</span> -->
                                </label>
                                <el-input-number v-model="form.packages_number"
                                                 :max="9999999999"
                                                 :min="0"
                                                 :precision="0"
                                                 :step="1"></el-input-number>
                                <small v-if="errors.packages_number"
                                       class="form-control-feedback"
                                       v-text="errors.packages_number[0]"></small>
                            </div>
                        </div>
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
                        <div class="col-lg-2" v-if="!order_form_id">
                            <div :class="{'has-danger': errors.order_form_external}"
                                 class="form-group">
                                <label class="control-label">Orden de pedido
                                    <el-tooltip class="item"
                                                content="Pedidos externos"
                                                effect="dark"
                                                placement="top">
                                        <i class="fa fa-info-circle"></i>
                                    </el-tooltip>
                                </label>
                                <el-input v-model="form.order_form_external"></el-input>
                                <small v-if="errors.order_form_external" class="form-control-feedback"
                                       v-text="errors.order_form_external[0]"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <div class="row">
                    </div>
                    <hr>
                    <h4>Datos envío</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <div :class="{'has-danger': errors.origin_address_id}"
                                 class="form-group">
                                <label class="control-label">Punto de partida<span class="text-danger"> *</span>
                                    <a href="#"
                                       @click.prevent="showDialogOriginAddressForm = true">[+ Nuevo]</a></label>
                                <el-select v-model="form.origin_address_id"
                                           placeholder="Seleccionar punto de partida">
                                    <el-option v-for="option in origin_addresses"
                                               :key="option.id"
                                               :label="option.address"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.origin_address_id"
                                       class="form-control-feedback"
                                       v-text="errors.origin_address_id[0]"></small>
                            </div>
                        </div>
                        <!--                        <div class="col-lg-5">-->
                        <!--                            <div :class="{'has-danger': errors.origin}"-->
                        <!--                                 class="form-group">-->
                        <!--                                <label class="control-label">Ubigeo<span class="text-danger"> *</span></label>-->
                        <!--                                <el-cascader v-model="origin.location_id"-->
                        <!--                                             :options="locations"-->
                        <!--                                             filterable></el-cascader>-->
                        <!--                                <small v-if="errors.origin"-->
                        <!--                                       class="form-control-feedback"-->
                        <!--                                       v-text="errors.origin.location_id[0]"></small>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <!--                        <div class="col-lg-7">-->
                        <!--                            <div :class="{'has-danger': errors['origin.address']}"-->
                        <!--                                 class="form-group">-->
                        <!--                                <label class="control-label">Dirección<span class="text-danger"> *</span></label>-->
                        <!--                                <el-input v-model="origin.address"-->
                        <!--                                          :maxlength="100"-->
                        <!--                                          placeholder="Dirección..."></el-input>-->
                        <!--                                <small v-if="errors['origin.address']"-->
                        <!--                                       class="form-control-feedback"-->
                        <!--                                       v-text="errors['origin.address'][0]"></small>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div :class="{'has-danger': errors.delivery_address_id}"
                                 class="form-group">
                                <label class="control-label">Punto de llegada<span class="text-danger"> *</span>
                                    <a href="#"
                                       v-if="form.customer_id"
                                       @click.prevent="showDialogDeliveryAddressForm = true">[+ Nuevo]</a></label>
                                <el-select v-model="form.delivery_address_id"
                                           placeholder="Seleccionar punto de llegada">
                                    <el-option v-for="option in delivery_addresses"
                                               :key="option.id"
                                               :label="option.address"
                                               :value="option.id"></el-option>
                                </el-select>
                                <small v-if="errors.delivery_address_id"
                                       class="form-control-feedback"
                                       v-text="errors.delivery_address_id[0]"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h4>Datos modo de traslado</h4>
                    <div class="row">
                        <template v-if="form.transport_mode_type_id === '01'">
                            <div class="col-lg-6">
                                <label class="control-label font-bold">
                                    Datos del transportista
                                    <a v-if="can_add_new_product"
                                       href="#"
                                       @click.prevent="showDialogDispatcherForm = true">[+ Nuevo]</a>
                                </label>
                                <span class="text-danger"> *</span>
                                <div :class="{'has-danger': errors.dispatcher_id}"
                                     class="form-group">
                                    <el-select v-model="form.dispatcher_id"
                                               clearable
                                               placeholder="Seleccionar transportista">
                                        <el-option
                                            v-for="option in dispatchers"
                                            :key="option.id"
                                            :label="option.number +' - '+ option.name +' - '+ option.number_mtc"
                                            :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.dispatcher_id"
                                           class="form-control-feedback"
                                           v-text="errors.dispatcher_id[0]"></small>
                                </div>
                            </div>
                        </template>
                        <template v-if="form.transport_mode_type_id === '02'">
                            <div class="col-lg-6">
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
                            <div class="col-lg-3">
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
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="control-label">N° placa semirremolque</label>
                                    <el-input v-model="form.secondary_license_plates.semitrailer"></el-input>
                                </div>
                            </div>
                        </template>
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
                                <tr v-for="(row, index) in form.items" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ row.unit_type_id }}</td>
                                    <td v-html="setDescriptionOfItem(row)" class="text-dark"></td>
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
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button v-if="(form.items.length > 0)"
                                   :loading="loading_submit"
                                   native-type="submit"
                                   type="primary">Generar
                        </el-button>
                    </div>
                </div>
            </form>
        </div>

        <person-form :external="true"
                     :showDialog.sync="showDialogNewPerson"
                     :input_person="input_person"
                     type="customers"></person-form>

        <driver-form :showDialog.sync="showDialogDriverForm"
                     @success="successDriver"></driver-form>

        <dispatcher-form :showDialog.sync="showDialogDispatcherForm"
                         @success="successDispatcher"></dispatcher-form>

        <transport-form :showDialog.sync="showDialogTransportForm"
                        @success="successTransport"></transport-form>

        <origin-address-form :showDialog.sync="showDialogOriginAddressForm"
                             @success="successOriginAddress"></origin-address-form>

        <delivery-address-form :showDialog.sync="showDialogDeliveryAddressForm"
                               title="Nuevo punto de llegada"
                               :person-id="form.customer_id"
                               @success="successDeliveryAddress"></delivery-address-form>

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
            :lotsGroup="item.lots_group"
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
import PersonForm from '../persons/form.vue';
import Items from './items.vue';
import itemForm from '../items/form.vue';
import LotsGroup from '../documents/partials/lots_group.vue';
import DriverForm from './drivers/form.vue';
import DispatcherForm from './dispatchers/form.vue';
import TransportForm from './transports/form.vue';
import OriginAddressForm from './OriginAddress/Form';
import DeliveryAddressForm from './partials/DispatchAddressForm';

import DispatchFinish from './partials/finish'
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
        DispatcherForm,
        TransportForm,
        OriginAddressForm,
        DeliveryAddressForm
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
            showDialogNewPerson: false,
            showDialogDriverForm: false,
            showDialogTransportForm: false,
            showDialogDispatcherForm: false,
            showDialogOriginAddressForm: false,
            showDialogDeliveryAddressForm: false,
            IdLoteSelected: false,
            showDialogLots: false,
            min_qty: 0.0001,
            input_person: {},
            identityDocumentTypes: [],
            transferReasonTypes: [],
            related_document_types: [],
            transportModeTypes: [],
            resource: 'dispatches',
            loading_submit: false,
            establishments: [],
            drivers: [],
            driver: null,
            dispatchers: [],
            dispatcher: null,
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
            origin: null,
            delivery: null,
            delivery_addresses: [],
            origin_addresses: [],
            send_sunat: false,
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
            this.transferReasonTypes = response.data.transferReasonTypes;
            this.related_document_types = response.data.related_document_types
            this.transportModeTypes = response.data.transportModeTypes;
            this.establishments = response.data.establishments;
            this.unitTypes = response.data.unitTypes;
            this.all_customers = [];
            this.countries = response.data.countries;
            this.locations = response.data.locations;
            this.seriesAll = response.data.series;
            this.drivers = response.data.drivers;
            this.dispatchers = response.data.dispatchers;
            this.transports = response.data.transports;
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
            this.searchRemoteCustomers('')
            if (this.establishments.length > 0) {
                this.form.establishment_id = _.head(this.establishments).id;
            }
            await this.changeEstablishment()
            this.changeSeries();
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
                document_type_id: '09',
                series: null,
                number: '#',
                date_of_issue: moment().format('YYYY-MM-DD'),
                time_of_issue: moment().format('HH:mm:ss'),
                date_of_shipping: moment().format('YYYY-MM-DD'),
                customer_id: customer_id,
                observations: '',
                transport_mode_type_id: '02',
                transfer_reason_type_id: '01',
                transfer_reason_description: null,
                transshipment_indicator: false,
                port_code: null,
                unit_type_id: 'KGM',
                total_weight: 1,
                packages_number: 1,
                container_number: null,
                dispatcher_id: null,
                dispatcher: {},
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
                origin_address_id: null,
                delivery_address_id: null,
            }
        },
        setDescriptionOfItem(item) {
            console.log(this.config.show_pdf_name)
            console.log(item.item == undefined)
            if (this.config.show_pdf_name) {
                if(item.item != undefined && item.item.name_product_pdf != undefined) {
                    if (item.item.name_product_pdf !== '' && !_.isNull(item.item.name_product_pdf)) {
                        return item.item.name_product_pdf;
                    }
                }
                if(item.name_product_pdf != undefined) {
                    if (item.name_product_pdf !== '' && !_.isNull(item.name_product_pdf)) {
                        return item.name_product_pdf;
                    }
                }
            }
            return item.description;
        },
        setDefaults() {
            if (this.origin_addresses.length > 0) {
                this.form.origin_address_id = _.head(this.origin_addresses).id;
            }
            if (this.drivers.length > 0) {
                let driver = _.find(this.drivers, {'is_default': true});
                this.form.driver_id = (driver) ? driver.id : _.head(this.drivers).id;
            }
            if (this.transports.length > 0) {
                let transport = _.find(this.transports, {'is_default': true});
                this.form.transport_id = (transport) ? transport.id : _.head(this.transports).id;
            }
            if (this.dispatchers.length > 0) {
                let dispatcher = _.find(this.dispatchers, {'is_default': true});
                this.form.dispatcher_id = (dispatcher) ? dispatcher.id : _.head(this.dispatchers).id;
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
        changeTransferReasonType() {
            if (this.form.transfer_reason_type_id === '09') {
                this.form.related = {
                    number: null,
                    document_type_id: '01'
                }
                this.form.customer_id = null;
                this.delivery = {
                    country_id: 'PE',
                    location_id: [],
                    address: null,
                }
            } else {
                this.form.related = {};
                this.delivery.country_id = 'PE';
            }
            this.searchRemoteCustomers('');
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
        async reloadDataCustomers(customer_id) {
            await this.$http.get(`/documents/search/customer/${customer_id}`).then((response) => {
                this.customers = response.data.customers
                // this.form.customer_id = customer_id
            })
        },
        async changeCustomer() {
            await this.getDeliveryAddresses(this.form.customer_id);
            if (this.delivery_addresses.length > 0) {
                this.form.delivery_address_id = _.head(this.delivery_addresses).id;
            }
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
        searchRemoteCustomers(input) {
            this.loading_search = true
            let identity_document_type_id = ['6', '4', '1', '0'];
            if (this.form.transfer_reason_type_id === '09') {
                identity_document_type_id = ['0'];
            }
            this.$http.post(`/store/get_customers`, {
                'identity_document_type_id': identity_document_type_id,
                'input': input,
            })
                .then(response => {
                    this.customers = response.data.customers
                    this.loading_search = false
                    this.input_person.number = (this.customers.length == 0) ? input : null
                })
        },
        filterCustomers() {
            if (this.form.document_type_id === '01') {
                this.customers = _.filter(this.all_customers, {'identity_document_type_id': '6'})
            } else {
                if (this.document_type_03_filter) {
                    this.customers = _.filter(this.all_customers, (c) => {
                        return c.identity_document_type_id !== '6'
                    })
                } else {
                    this.customers = this.all_customers
                }
            }
        },
        setDefaultCustomer() {
            if (this.config.establishment.customer_id) {
                let temp_customers = this.customers;
                let customer_id = this.config.establishment.customer_id;
                let custom = temp_customers.find(l => l.id == customer_id);
                if (custom === undefined) {
                    this.$http.get(`/${this.resource}/search/customer/${customer_id}`).then((response) => {
                        let data_customer = response.data.customers
                        temp_customers = temp_customers.push(...data_customer)
                    })
                    temp_customers = this.customers.filter((item, index, self) =>
                            index === self.findIndex((t) => (
                                t.id === item.id
                            ))
                    )
                    this.customers = temp_customers;
                }
                let alt = _.find(this.customers, {'id': customer_id});
                if (alt !== undefined) {
                    this.form.customer_id = customer_id
                    this.changeCustomer();
                }
            }
        },
        async changeEstablishment() {
            if (this.form.establishment_id) {
                this.series = _.filter(this.seriesAll, {
                    'establishment_id': this.form.establishment_id,
                    'document_type_id': this.form.document_type_id
                });
                await this.getOriginAddresses(this.form.establishment_id)
            }
        },
        changeSeries() {
            this.form.series = null;
            this.setDefaultSeries();
            //this.generalSetDefaultSerieByDocumentType('09');
        },
        setDefaultSeries() {
            let series_id = parseInt(this.config.user.serie);
            if (isNaN(series_id)) series_id = null;
            let searchSeries = _.find(this.series, {
                'establishment_id': this.form.establishment_id,
                'document_type_id': this.form.document_type_id,
                'id': series_id
            });
            if (searchSeries !== undefined && searchSeries.length > 0) {
                this.form.series = searchSeries.number;
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
            if (this.form.transport_mode_type_id === '02') {
                this.form.dispatcher_id = null;
                this.form.dispatcher = null;
                if (!this.form.driver_id) {
                    return this.$message.error('El conductor es requerido')
                }
                if (!this.form.transport_id) {
                    return this.$message.error('El vehículo es requerido')
                }
                this.form.driver = _.find(this.drivers, {'id': this.form.driver_id});
                this.form.transport = _.find(this.transports, {'id': this.form.transport_id});
                // this.form.license_plate = this.form.transport.plate_number;

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
                // if (this.form.license_plate === '' || _.isNull(this.form.license_plate)) {
                //     return this.$message.error('El número de placa es requerido')
                // }
            }
            if (this.form.transport_mode_type_id === '01') {
                this.form.driver_id = null;
                this.form.driver = null;
                if (!this.form.dispatcher_id) {
                    return this.$message.error('El transportista es requerido')
                }
                let v = _.find(this.dispatchers, {'id': this.form.dispatcher_id})
                this.form.dispatcher.identity_document_type_id = v.identity_document_type_id;
                this.form.dispatcher.number = v.number;
                this.form.dispatcher.name = v.name;
                this.form.dispatcher.number_mtc = v.number_mtc;

                if (this.form.dispatcher.identity_document_type_id === '' || _.isNull(this.form.dispatcher.identity_document_type_id)) {
                    return this.$message.error('El tipo de documento del transportista es requerido')
                }
                if (this.form.dispatcher.number === '' || _.isNull(this.form.dispatcher.number)) {
                    return this.$message.error('El número del transportista es requerido')
                }
                if (this.form.dispatcher.name === '' || _.isNull(this.form.dispatcher.name)) {
                    return this.$message.error('El nombre del transportista es requerido')
                }
                // if (this.form.dispatcher.number_mtc === '' || _.isNull(this.form.dispatcher.number_mtc)) {
                //     return this.$message.error('El MTC del transportista es requerido')
                // }
            }
            const validateQuantity = await this.verifyQuantityItems()
            if (!validateQuantity.validate) {
                return this.$message.error('Los productos no pueden tener cantidad 0.')
            }

            this.form.origin = _.find(this.origin_addresses, {'id': this.form.origin_address_id});
            this.form.delivery = _.find(this.delivery_addresses, {'id': this.form.delivery_address_id});
            // this.form.origin = this.origin;

            // if (this.form.origin.location_id.length !== 3 || this.form.delivery.location_id.length !== 3) {
            //     return this.$message.error('El campo ubigeo es obligatorio')
            // }
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
                this.setDefaultCustomer();
                this.loading_submit = false;
            });
        },
        close() {
            location.href = '/dispatches';
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
        async successDispatcher(id) {
            this.form.dispatcher_id = id;
            await this.$http.get(`/dispatchers/get_options`)
                .then(response => {
                    this.dispatchers = response.data;
                });
        },
        async successTransport(id) {
            this.form.transport_id = id;
            await this.$http.get(`/transports/get_options`)
                .then(response => {
                    this.transports = response.data;
                });
        },
        async successOriginAddress(id) {
            this.form.origin_address_id = id;
            await this.getOriginAddresses(this.form.establishment_id);
        },
        async successDeliveryAddress(id) {
            this.form.delivery_address_id = id;
            await this.getDeliveryAddresses(this.form.customer_id);
        },
        async getOriginAddresses(establishment_id) {
            await this.$http.get(`/${this.resource}/get_origin_addresses/${establishment_id}`)
                .then(response => {
                    this.origin_addresses = response.data;
                });
        },
        async getDeliveryAddresses(customer_id) {
            await this.$http.get(`/dispatch_addresses/get_options/${customer_id}`)
                .then(response => {
                    this.delivery_addresses = response.data;
                });
        },
    }
}
</script>
