<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Avanzado</span></li>
            </ol>
        </div>
        <div class="card card-dashboard border">
            <div class="card-body">
                <template>
                    <form autocomplete="off">
                        <el-tabs v-model="activeName">
                            <el-tab-pane class="mb-3"
                                         name="first">
                                <span slot="label"><h3>Servicios</h3></span>
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Envío de comprobantes automático</label>
                                        <div :class="{'has-danger': errors.send_auto}"
                                             class="form-group">
                                            <el-switch v-model="form.send_auto"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.send_auto"
                                                   class="form-control-feedback"
                                                   v-text="errors.send_auto[0]"></small>
                                        </div>
                                    </div>
                                    <div v-if="typeUser != 'integrator'"
                                         class="col-md-6 mt-4">
                                        <label class="control-label">Crontab <small>Tareas Programadas</small></label>
                                        <div :class="{'has-danger': errors.cron}"
                                             class="form-group">
                                            <el-switch v-model="form.cron"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.cron"
                                                   class="form-control-feedback"
                                                   v-text="errors.cron[0]"></small>
                                        </div>
                                    </div>
                                </div>
                                <!-- sunat ha dado de baja estos servidores -->
                                <!-- <div class="row">
                                    <div v-if="typeUser != 'integrator'"
                                         class="col-md-6 mt-4">
                                        <label class="control-label">Envío de comprobantes a servidor alterno de
                                                                     SUNAT</label>
                                        <div :class="{'has-danger': errors.sunat_alternate_server}"
                                             class="form-group">
                                            <el-switch v-model="form.sunat_alternate_server"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.sunat_alternate_server"
                                                   class="form-control-feedback"
                                                   v-text="errors.sunat_alternate_server[0]"></small>
                                        </div>
                                    </div>
                                </div> -->
                            </el-tab-pane>
                            <el-tab-pane class="mb-3"
                                         name="second">
                                <span slot="label"><h3>Contable</h3></span>
                                <div class="row">
                                    <div v-if="typeUser != 'integrator'"
                                         class="col-md-4 mt-4">
                                        <label class="control-label">Cantidad decimales POS</label>
                                        <div :class="{'has-danger': errors.decimal_quantity}"
                                             class="form-group">
                                            <el-input-number v-model="form.decimal_quantity"
                                                             :max="10"
                                                             :min="2"
                                                             @change="submit"></el-input-number>
                                            <small v-if="errors.decimal_quantity"
                                                   class="form-control-feedback"
                                                   v-text="errors.decimal_quantity[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div v-if="typeUser != 'integrator'"
                                         class="col-md-4 mt-4">
                                        <label class="control-label">Impuesto bolsa plástica</label>
                                        <div :class="{'has-danger': errors.amount_plastic_bag_taxes}"
                                             class="form-group">
                                            <el-input-number v-model="form.amount_plastic_bag_taxes"
                                                             :max="0.5"
                                                             :min="0.1"
                                                             :precision="2"
                                                             :step="0.1"
                                                             @change="changeAmountPlasticBagTaxes"></el-input-number>
                                            <small v-if="errors.amount_plastic_bag_taxes"
                                                   class="form-control-feedback"
                                                   v-text="errors.amount_plastic_bag_taxes[0]"></small>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4" v-if="typeUser != 'integrator'"> <br>
                                        <label class="control-label">Cantidad de columnas en productos</label>
                                        <div class="form-group" :class="{'has-danger': errors.amount_plastic_bag_taxes}">
                                            <el-slider  @change="submit" v-model="form.colums_grid_item" :min="2" :max="6"></el-slider>
                                            <small class="form-control-feedback" v-if="errors.amount_plastic_bag_taxes" v-text="errors.amount_plastic_bag_taxes[0]"></small>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6 mt-4">
                                        <div :class="{'has-danger': errors.affectation_igv_type_id}"
                                             class="form-group">
                                            <label class="control-label">Tipo de afectación
                                                <el-tooltip class="item"
                                                            content="Tipo de afectación predeterminada al registrar nuevo producto"
                                                            effect="dark"
                                                            placement="top-start">
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <el-select v-model="form.affectation_igv_type_id"
                                                       filterable
                                                       @change="submit">
                                                <el-option v-for="option in affectation_igv_types"
                                                           :key="option.id"
                                                           :label="option.description"
                                                           :value="option.id"></el-option>
                                            </el-select>
                                            <small v-if="errors.affectation_igv_type_id"
                                                   class="form-control-feedback"
                                                   v-text="errors.affectation_igv_type_id[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Impuesto incluido en registro de productos</label>
                                        <div :class="{'has-danger': errors.include_igv}"
                                             class="form-group">
                                            <el-switch v-model="form.include_igv"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.include_igv"
                                                   class="form-control-feedback"
                                                   v-text="errors.include_igv[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Caja General seleccionada por defecto</label>
                                        <div :class="{'has-danger': errors.destination_sale}"
                                             class="form-group">
                                            <el-switch v-model="form.destination_sale"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.destination_sale"
                                                   class="form-control-feedback"
                                                   v-text="errors.destination_sale[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <a class="text-center font-weight-bold text-info"
                                           href="#"
                                           @click.prevent="showDialogAllowanceCharge = true">[+ Aplicar cargos]</a>
                                        <el-tooltip
                                            class="item"
                                            content="Disponible en Ventas - Comprobante electrónico"
                                            effect="dark"
                                            placement="top-start">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Modificar Tipo de afectación (Gravado -
                                                                     Bonificación)
                                            <el-tooltip
                                                class="item"
                                                content="Disponible Nuevo CPE"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.change_free_affectation_igv}"
                                             class="form-group">
                                            <el-switch v-model="form.change_free_affectation_igv"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.change_free_affectation_igv"
                                                   class="form-control-feedback"
                                                   v-text="errors.change_free_affectation_igv[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Moneda predeterminada
                                            <el-tooltip
                                                class="item"
                                                content="Solo en Nota de venta y CPE"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.currency_type_id}"
                                             class="form-group">
                                            <el-select v-model="form.currency_type_id"
                                                       @change="submit"
                                                       filterable>
                                                <el-option v-for="option in config.currency_types"
                                                           :key="option.id"
                                                           :label="option.symbol+' - '+option.description"
                                                           :value="option.id"></el-option>
                                            </el-select>
                                            <small
                                                v-if="errors.currency_type_id"
                                                class="form-control-feedback"
                                                v-text="errors.currency_type_id[0]"></small>
                                        </div>
                                    </div>


                                    <div v-if="typeUser != 'integrator'"
                                         class="col-md-4 mt-4">
                                        <label class="control-label">
                                            Porcentaje retención IGV

                                            <el-tooltip
                                                class="item"
                                                content="Disponible Nuevo CPE"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>

                                        </label>
                                        <div :class="{'has-danger': errors.igv_retention_percentage}"
                                             class="form-group">
                                            <el-input-number v-model="form.igv_retention_percentage"
                                                             :min="0.01"
                                                             :max="999"
                                                             @change="submit"></el-input-number>
                                            <small v-if="errors.igv_retention_percentage"
                                                   class="form-control-feedback"
                                                   v-text="errors.igv_retention_percentage[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Nombre producto PDF para XML
                                            <el-tooltip
                                                class="item"
                                                content="Registra el campo nombre producto pdf en el XML - Disponible Nuevo CPE (Facturas/Boletas)"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.name_product_pdf_to_xml}"
                                             class="form-group">
                                            <el-switch v-model="form.name_product_pdf_to_xml"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.name_product_pdf_to_xml"
                                                   class="form-control-feedback"
                                                   v-text="errors.name_product_pdf_to_xml[0]"></small>
                                        </div>
                                    </div>

                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3"
                                         name="third">
                                <span slot="label"><h3>Visual</h3></span>
                                <div class="row">
                                    <!-- <div class="col-md-6 mt-4" v-if="typeUser != 'integrator'">
                                        <label class="control-label">Cuenta contable venta subtotal</label>
                                        <div class="form-group" :class="{'has-danger': errors.subtotal_account}">
                                            <el-input v-model="form.subtotal_account" width="50%"></el-input>
                                            <small class="form-control-feedback" v-if="errors.subtotal_account" v-text="errors.subtotal_account[0]"></small>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Opciones avanzadas en POS</label>
                                        <div :class="{'has-danger': errors.options_pos}"
                                             class="form-group">
                                            <el-switch v-model="form.options_pos"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.options_pos"
                                                   class="form-control-feedback"
                                                   v-text="errors.options_pos[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Restringir fecha de comprobante</label>
                                        <div :class="{'has-danger': errors.restrict_receipt_date}"
                                             class="form-group">
                                            <el-switch v-model="form.restrict_receipt_date"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.restrict_receipt_date"
                                                   class="form-control-feedback"
                                                   v-text="errors.restrict_receipt_date[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir generar comprobante de pago desde
                                                                     cotización a vendedores</label>
                                        <div :class="{'has-danger': errors.quotation_allow_seller_generate_sale}"
                                             class="form-group">
                                            <el-switch v-model="form.quotation_allow_seller_generate_sale"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.quotation_allow_seller_generate_sale"
                                                   class="form-control-feedback"
                                                   v-text="errors.quotation_allow_seller_generate_sale[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir editar precio unitario a
                                                                     vendedores</label>
                                        <div :class="{'has-danger': errors.allow_edit_unit_price_to_seller}"
                                             class="form-group">
                                            <el-switch v-model="form.allow_edit_unit_price_to_seller"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.allow_edit_unit_price_to_seller"
                                                   class="form-control-feedback"
                                                   v-text="errors.allow_edit_unit_price_to_seller[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir crear productos a vendedores</label>
                                        <div :class="{'has-danger': errors.seller_can_create_product}"
                                             class="form-group">
                                            <el-switch v-model="form.seller_can_create_product"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.seller_can_create_product"
                                                   class="form-control-feedback"
                                                   v-text="errors.seller_can_create_product[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir Ver el saldo en balance de finanzas a
                                                                     vendedores</label>
                                        <div :class="{'has-danger': errors.seller_can_view_balance}"
                                             class="form-group">
                                            <el-switch v-model="form.seller_can_view_balance"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.seller_can_view_balance"
                                                   class="form-control-feedback"
                                                   v-text="errors.seller_can_view_balance[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">
                                            Permite habilitar las acciones para vendedores
                                            <el-tooltip
                                                class="item"
                                                content="Disponible en Oportunidad de Venta y Pedidos"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>

                                        </label>
                                        <div :class="{'has-danger': errors.seller_can_generate_sale_opportunities}"
                                             class="form-group">
                                            <el-switch v-model="form.seller_can_generate_sale_opportunities"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.seller_can_generate_sale_opportunities"
                                                   class="form-control-feedback"
                                                   v-text="errors.seller_can_generate_sale_opportunities[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Productos de una ubicación</label>
                                        <div :class="{'has-danger': errors.product_only_location}"
                                             class="form-group">
                                            <el-switch v-model="form.product_only_location"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.product_only_location"
                                                   class="form-control-feedback"
                                                   v-text="errors.product_only_location[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Seleccionar boleta por defecto
                                            <el-tooltip class="item"
                                                        content="Disponible POS"
                                                        effect="dark"
                                                        placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.default_document_type_03}"
                                             class="form-group">
                                            <el-switch v-model="form.default_document_type_03"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="changeDefaultDocumentType03"></el-switch>
                                            <small v-if="errors.default_document_type_03"
                                                   class="form-control-feedback"
                                                   v-text="errors.default_document_type_03[0]"></small>
                                        </div>
                                    </div>
                                    <!-- Para elementos de farmacia -->
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Habilita elementos de farmacia
                                            <el-tooltip
                                                class="item"
                                                content="Añade Codigo DIGEMID en Empresa y Codigo DIGEMID para productos, junto con el registro salitario"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.is_pharmacy}"
                                             class="form-group">
                                            <el-switch v-model="form.is_pharmacy"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.is_pharmacy"
                                                   class="form-control-feedback"
                                                   v-text="errors.is_pharmacy[0]"></small>
                                        </div>
                                    </div>
                                    <!-- auto_send_dispatchs_to_sunat -->
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Enviar la guia de remision automaticamente a sunat
                                                <!--
                                                <el-tooltip class="item" effect="dark" placement="top-start">
                                                    <div slot="content">Al generar una guia basado en el documento, se
                                                                        actualizará el comprobante de pago
                                                    </div>
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                                -->
                                            </label>
                                            <div :class="{'has-danger': errors.auto_send_dispatchs_to_sunat}"
                                                 class="form-group">
                                                <el-switch v-model="form.auto_send_dispatchs_to_sunat"
                                                           active-text="Si"
                                                           inactive-text="No"
                                                           @change="submit"></el-switch>
                                                <small v-if="errors.auto_send_dispatchs_to_sunat"
                                                       class="form-control-feedback"
                                                       v-text="errors.auto_send_dispatchs_to_sunat[0]"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Aplicar precios por almacén
                                            <el-tooltip
                                                class="item"
                                                content="Disponible POS"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.active_warehouse_prices}"
                                             class="form-group">
                                            <el-switch v-model="form.active_warehouse_prices"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.active_warehouse_prices"
                                                   class="form-control-feedback"
                                                   v-text="errors.active_warehouse_prices[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Buscar producto por serie
                                            <el-tooltip
                                                class="item"
                                                content="Disponible Nuevo CPE"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.search_item_by_series}"
                                             class="form-group">
                                            <el-switch v-model="form.search_item_by_series"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.search_item_by_series"
                                                   class="form-control-feedback"
                                                   v-text="errors.search_item_by_series[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Seleccionar precio disponible - Lista de precios
                                            <el-tooltip
                                                class="item"
                                                content="Disponible POS"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.select_available_price_list}"
                                             class="form-group">
                                            <el-switch v-model="form.select_available_price_list"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.select_available_price_list"
                                                   class="form-control-feedback"
                                                   v-text="errors.select_available_price_list[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">
                                            Muestra campos opcionales para los Items a modo informativo
                                            <el-tooltip
                                                class="item"
                                                content="Disponible en CPE"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.show_extra_info_to_item}"
                                             class="form-group">
                                            <el-switch v-model="form.show_extra_info_to_item"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.show_extra_info_to_item"
                                                   class="form-control-feedback"
                                                   v-text="errors.show_extra_info_to_item[0]"></small>
                                        </div>
                                    </div>


                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Agrupar productos y cantidades - Generar CPE

                                            <el-tooltip
                                                class="item"
                                                content="Agrupar/Sumar productos y cantidades al generar cpe desde múltiples notas de venta"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.group_items_generate_document}"
                                             class="form-group">
                                            <el-switch v-model="form.group_items_generate_document"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.group_items_generate_document"
                                                   class="form-control-feedback"
                                                   v-text="errors.group_items_generate_document[0]"></small>
                                        </div>
                                    </div>


                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Mostrar el nombre del PDF

                                            <el-tooltip
                                                class="item"
                                                content="Muestra el nombre del producto que se ingresa en el pdf, en vez del nombre del producto. Solo para CPE y Cotización"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.show_pdf_name}"
                                             class="form-group">
                                            <el-switch v-model="form.show_pdf_name"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.show_pdf_name"
                                                   class="form-control-feedback"
                                                   v-text="errors.show_pdf_name[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir Colocar direccion de llegada en guía

                                            <el-tooltip
                                                class="item"
                                                content="En guías, cambia el selector a texto para poder introducir el valor. Maximo 100 caracteres"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.dispatches_address_text}"
                                             class="form-group">
                                            <el-switch v-model="form.dispatches_address_text"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.dispatches_address_text"
                                                   class="form-control-feedback"
                                                   v-text="errors.dispatches_address_text[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Asignar dirección de partida - guía
                                            <el-tooltip
                                                class="item"
                                                content="Se asigna la dirección de partida mediante la informacion registrada en establecimiento - Disponible en guías"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.set_address_by_establishment}"
                                             class="form-group">
                                            <el-switch v-model="form.set_address_by_establishment"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.set_address_by_establishment"
                                                   class="form-control-feedback"
                                                   v-text="errors.set_address_by_establishment[0]"></small>
                                        </div>
                                    </div>



                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Habilitar permiso para editar CPE
                                            <el-tooltip
                                                class="item"
                                                content="Habilitar asignación de permiso para editar comprobantes - Disponible en usuarios (permisos)"
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.permission_to_edit_cpe}"
                                             class="form-group">
                                            <el-switch v-model="form.permission_to_edit_cpe"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.permission_to_edit_cpe"
                                                   class="form-control-feedback"
                                                   v-text="errors.permission_to_edit_cpe[0]"></small>
                                        </div>
                                    </div>


                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Seleccionar nota de venta por defecto
                                            <el-tooltip class="item"
                                                        content="Disponible POS"
                                                        effect="dark"
                                                        placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.default_document_type_80}"
                                             class="form-group">
                                            <el-switch v-model="form.default_document_type_80"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="changeDefaultDocumentType80"></el-switch>
                                            <small v-if="errors.default_document_type_80"
                                                   class="form-control-feedback"
                                                   v-text="errors.default_document_type_80[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Habilitar busqueda con escáner de código de barras
                                            <el-tooltip class="item"
                                                        content="Disponible POS"
                                                        effect="dark"
                                                        placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.search_item_by_barcode}"
                                             class="form-group">
                                            <el-switch v-model="form.search_item_by_barcode"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.search_item_by_barcode"
                                                   class="form-control-feedback"
                                                   v-text="errors.search_item_by_barcode[0]"></small>
                                        </div>
                                    </div>

                                    <!--
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Mostrar item de solo el almacen de usuario
                                            <el-tooltip
                                                class="item"
                                                content="Se aplica el filtro de mostrar items con relacion al establecimiento del usuario."
                                                effect="dark"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.show_items_only_user_stablishment}"
                                             class="form-group">
                                            <el-switch v-model="form.show_items_only_user_stablishment"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.show_items_only_user_stablishment"
                                                   class="form-control-feedback"
                                                   v-text="errors.show_items_only_user_stablishment[0]"></small>
                                        </div>
                                    </div>
                                    -->

                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3"
                                         name="fourth">
                                <span slot="label"><h3>PDF</h3></span>
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Editar nombre de productos</label>
                                        <div :class="{'has-danger': errors.edit_name_product}"
                                             class="form-group">
                                            <el-switch v-model="form.edit_name_product"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.edit_name_product"
                                                   class="form-control-feedback"
                                                   v-text="errors.edit_name_product[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <a class="text-center font-weight-bold text-info"
                                           href="#"
                                           @click.prevent="showDialogTermsCondition = true">[+ Términos y condiciones -
                                                                                          Cotización]</a>
                                        <br>
                                        <br>
                                        <a class="text-center font-weight-bold text-info"
                                           href="#"
                                           @click.prevent="showDialogTermsConditionSales = true">[+ Términos y condiciones -
                                                                                          Ventas]</a>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Mostrar cotización en finanzas</label>
                                        <div :class="{'has-danger': errors.cotizaction_finance}"
                                             class="form-group">
                                            <el-switch v-model="form.cotizaction_finance"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.cotizaction_finance"
                                                   class="form-control-feedback"
                                                   v-text="errors.cotizaction_finance[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Mostrar leyenda en footer - pdf
                                            <el-tooltip class="item"
                                                        effect="dark"
                                                        placement="top-start">
                                                <div slot="content">Leyenda: Bienes transferidos y/o servicios prestados
                                                                    en la Amazonía para ser consumidos en la misma<br/>Disponible
                                                                    para facturas, boletas, notas y cotizaciones
                                                </div>
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div :class="{'has-danger': errors.legend_footer}"
                                             class="form-group">
                                            <el-switch v-model="form.legend_footer"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                            <small v-if="errors.legend_footer"
                                                   class="form-control-feedback"
                                                   v-text="errors.legend_footer[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">Imágen para encabezado - pdf
                                                <el-tooltip class="item"
                                                            content="Disponible para facturas y boletas en formato a4, usando la plantilla header_image_full_width"
                                                            effect="dark"
                                                            placement="top-start">
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <el-input v-model="form.header_image"
                                                      :readonly="true">
                                                <el-upload slot="append"
                                                           :headers="headers"
                                                           :on-success="successUpload"
                                                           :show-file-list="false"
                                                           action="/configurations/uploads">
                                                    <el-button icon="el-icon-upload"
                                                               type="primary"></el-button>
                                                </el-upload>
                                            </el-input>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">Mostrar ticket 58mm
                                                <el-tooltip class="item"
                                                            effect="dark"
                                                            placement="top-start">
                                                    <div slot="content">Disponible para Ventas (Facturas/Boletas/Notas
                                                                        de Crédito-Débito)
                                                    </div>
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <div :class="{'has-danger': errors.ticket_58}"
                                                 class="form-group">
                                                <el-switch v-model="form.ticket_58"
                                                           active-text="Si"
                                                           inactive-text="No"
                                                           @change="submit"></el-switch>
                                                <small v-if="errors.ticket_58"
                                                       class="form-control-feedback"
                                                       v-text="errors.ticket_58[0]"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- update_document_on_dispaches -->
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Actualizar documento al generar guía.
                                                <el-tooltip class="item"
                                                            effect="dark"
                                                            placement="top-start">
                                                    <div slot="content">Al generar una guia basado en el documento, se
                                                                        actualizará el comprobante de pago
                                                    </div>
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <div :class="{'has-danger': errors.update_document_on_dispaches}"
                                                 class="form-group">
                                                <el-switch v-model="form.update_document_on_dispaches"
                                                           active-text="Si"
                                                           inactive-text="No"
                                                           @change="submit"></el-switch>
                                                <small v-if="errors.update_document_on_dispaches"
                                                       class="form-control-feedback"
                                                       v-text="errors.update_document_on_dispaches[0]"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- item_name_pdf_description -->
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Usar la descripcion como nombre del producto PDF
                                                <el-tooltip class="item"
                                                            effect="dark"
                                                            placement="top-start">
                                                    <div slot="content">
                                                        En factura/boleta, cotizacion y nota de venta, se usará la descripcion como nombre del producto PDF por defecto.
                                                    </div>
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <div :class="{'has-danger': errors.item_name_pdf_description}"
                                                 class="form-group">
                                                <el-switch v-model="form.item_name_pdf_description"
                                                           active-text="Si"
                                                           inactive-text="No"
                                                           @change="submit"></el-switch>
                                                <small v-if="errors.item_name_pdf_description"
                                                       class="form-control-feedback"
                                                       v-text="errors.item_name_pdf_description[0]"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3"
                                         name="five">
                                <span slot="label"><h3>Finanzas</h3></span>
                                <div class="row">
                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <label>Aplicar penalidad a los pagos vencidos</label>
                                            <el-switch v-model="form.finances.apply_arrears"
                                                       active-text="Si"
                                                       inactive-text="No"
                                                       @change="submit"></el-switch>
                                        </div>
                                        <div v-if="form.finances.apply_arrears"
                                             class="form-group"
                                             style="max-width: 300px;">
                                            <label>Cantidad a aplicar por día</label>
                                            <el-input v-model="form.finances.arrears_amount"
                                                      class="input-with-select"
                                                      placeholder="Please input">
                                                <el-button slot="append"
                                                           @click="submit">
                                                    <i class="fa fa-save"></i>
                                                </el-button>
                                            </el-input>
                                        </div>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3"
                                         name="six">
                                <span slot="label"><h3>Datos</h3></span>
                                <div class="row">
                                    <div class="col-12 mt-4">
                                        <tenant-options-form></tenant-options-form>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3"
                                         name="seven">
                                <span slot="label"><h3>Compras</h3></span>
                                <tenant-configurations-form-purchases
                                    :errors="errors"
                                    @EmitChange="UpdateFormPurchase"
                                ></tenant-configurations-form-purchases>

                            </el-tab-pane>
                        </el-tabs>
                        <terms-condition :form="form"
                                         :showClose="false"
                                         :showDialog.sync="showDialogTermsCondition"></terms-condition>
                        <terms-condition-sale :form="form"
                                              :showClose="false"
                                              :showDialog.sync="showDialogTermsConditionSales"></terms-condition-sale>

                        <allowance-charge :form="form"
                                          :showClose="false"
                                          :showDialog.sync="showDialogAllowanceCharge"></allowance-charge>
                    </form>
                </template>
            </div>
        </div>
    </div>
</template>

<script>

import TermsCondition from '@views/quotations/partials/terms_condition.vue'
import TermsConditionSale from '@views/documents/partials/terms_condition.vue'
import AllowanceCharge from './partials/allowance_charge.vue'
import {mapActions, mapState} from "vuex";

export default {
    props: [
        'typeUser',
        'configuration',
    ],
    components: {TermsCondition, TermsConditionSale, AllowanceCharge},
    computed: {
        ...mapState([
            'config',
        ]),
    },
    data() {
        return {
            headers: headers_token,
            showDialogTermsCondition: false,
            showDialogTermsConditionSales: false,
            showDialogAllowanceCharge: false,
            loading_submit: false,
            resource: 'configurations',
            errors: {},
            form: {
                finances: {},
                visual: {},
                dispatches_address_text:false,
            },
            affectation_igv_types: [],
            placeholder: '',
            activeName: 'first'
        }
    },
    created() {
        this.$store.commit('setConfiguration',this.configuration)
        this.$store.commit('setTypeUser',this.typeUser)
        this.loadConfiguration()
        this.form = this.config;
    },
    mounted() {
        this.loadTables()
        this.initForm();
        this.$http.get(`/${this.resource}/record`).then(response => {
            if (response.data !== '') {
                this.form = response.data.data;
                this.$store.commit('setConfiguration', this.form)

            }
            // console.log(this.placeholder)
        });

        this.events()
    },
    methods: {

        ...mapActions([
            'loadConfiguration',
        ]),
        events() {

            this.$eventHub.$on('submitFormConfigurations', (form) => {
                this.form = form
                this.submit()
            })

        },
        successUpload(response, file, fileList) {
            if (response.success) {
                this.$message.success(response.message)
                this.getRecord()
                this.form[response.type] = response.name
            } else {
                this.$message({message: 'Error al subir el archivo', type: 'error'})
            }
        },
        async getRecord() {

            await this.$http.get(`/${this.resource}/record`).then(response => {
                if (response.data !== '') {
                    this.form = response.data.data;
                }
                // console.log(this.placeholder)
            });
        },
        async loadTables() {

            await this.$http.get(`/${this.resource}/tables`).then(response => {
                this.affectation_igv_types = response.data.affectation_igv_types
            })

        },
        initForm() {
            this.errors = {};
            this.form = {
                send_auto: true,
                stock: true,
                cron: true,
                id: null,
                sunat_alternate_server: false,
                subtotal_account: null,
                decimal_quantity: null,
                amount_plastic_bag_taxes: 0.1,
                colums_grid_item: 4,
                affectation_igv_type_id: '10',
                terms_condition: null,
                header_image: null,
                legend_footer: false,
                default_document_type_03: false,
                default_document_type_80: false,
                search_item_by_barcode: false,
                destination_sale: false,
                quotation_allow_seller_generate_sale: false,
                allow_edit_unit_price_to_seller: false,
                seller_can_create_product: false,
                seller_can_generate_sale_opportunities: false,
                seller_can_view_balance: true,
                finances: {},
                visual: {},
                ticket_58: false,
                update_document_on_dispaches: false,
                auto_send_dispatchs_to_sunat: true,
                is_pharmacy: false,
                active_warehouse_prices: false,
                search_item_by_series: false,
                change_free_affectation_igv: false,
                select_available_price_list: false,
                show_pdf_name: this.config.show_pdf_name,
                dispatches_address_text: this.config.dispatches_address_text,
                group_items_generate_document: false,
                enabled_global_igv_to_purchase: this.config.enabled_global_igv_to_purchase,
                set_address_by_establishment: false,
                permission_to_edit_cpe: false,
                name_product_pdf_to_xml:false,
            };
        },
        UpdateFormPurchase(e) {
            //Añadir la variable para cada item en compra. No es posible pasar elemento form por vuex
            this.form.enabled_global_igv_to_purchase = this.config.enabled_global_igv_to_purchase
            this.submit();
        },
        changeDefaultDocumentType03(){

            if(this.form.default_document_type_03){
                this.form.default_document_type_80 = false
            }
            this.submit()

        },
        changeDefaultDocumentType80(){

            if(this.form.default_document_type_80){
                this.form.default_document_type_03 = false
            }
            this.submit()
        },
        submit() {
            this.loading_submit = true;

            this.$http.post(`/${this.resource}`, this.form).then(response => {
                let data = response.data;
                if (data.success) {
                    this.$message.success(data.message);
                } else {
                    this.$message.error(data.message);
                }
                if (data !== undefined && data.configuration !== undefined) {
                    this.$store.commit('setConfiguration', data.configuration)
                }

            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    console.log(error);
                }
            }).then(() => {
                this.loading_submit = false;
            });
        },
        changeAmountPlasticBagTaxes() {
            this.loading_submit = true;

            this.$http.post(`/${this.resource}/icbper`, this.form).then(response => {
                if (response.data.success) {
                    this.$message.success(response.data.message);
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    console.log(error);
                }
            }).then(() => {
                this.loading_submit = false;
            });
        }
    }
}
</script>
