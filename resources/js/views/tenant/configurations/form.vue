<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span> </li>
                <li><span class="text-muted">Avanzado</span></li>
            </ol>
        </div>
        <div class="card card-dashboard border">
            <div class="card-body">
                <template>
                    <form autocomplete="off">
                        <el-tabs v-model="activeName">
                            <el-tab-pane class="mb-3" name="first">
                                <span slot="label"><h3>Servicios</h3></span>
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Envío de comprobantes automático</label>
                                        <div class="form-group" :class="{'has-danger': errors.send_auto}">
                                            <el-switch v-model="form.send_auto" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.send_auto" v-text="errors.send_auto[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4" v-if="typeUser != 'integrator'">
                                        <label class="control-label">Crontab <small>Tareas Programadas</small></label>
                                        <div class="form-group" :class="{'has-danger': errors.cron}">
                                            <el-switch v-model="form.cron" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.cron" v-text="errors.cron[0]"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-4" v-if="typeUser != 'integrator'">
                                        <label class="control-label">Envío de comprobantes a servidor alterno de SUNAT</label>
                                        <div class="form-group" :class="{'has-danger': errors.sunat_alternate_server}">
                                            <el-switch v-model="form.sunat_alternate_server" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.sunat_alternate_server" v-text="errors.sunat_alternate_server[0]"></small>
                                        </div>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3" name="second">
                                <span slot="label"><h3>Contable</h3></span>
                                <div class="row">
                                    <div class="col-md-4 mt-4" v-if="typeUser != 'integrator'">
                                        <label class="control-label">Cantidad decimales POS</label>
                                        <div class="form-group" :class="{'has-danger': errors.decimal_quantity}">
                                            <el-input-number v-model="form.decimal_quantity" @change="submit" :min="2" :max="10"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors.decimal_quantity" v-text="errors.decimal_quantity[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 mt-4" v-if="typeUser != 'integrator'">
                                        <label class="control-label">Impuesto bolsa plástica</label>
                                        <div class="form-group" :class="{'has-danger': errors.amount_plastic_bag_taxes}">
                                            <el-input-number v-model="form.amount_plastic_bag_taxes" @change="changeAmountPlasticBagTaxes" :precision="2" :step="0.1" :max="0.5" :min="0.1"></el-input-number>
                                            <small class="form-control-feedback" v-if="errors.amount_plastic_bag_taxes" v-text="errors.amount_plastic_bag_taxes[0]"></small>
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
                                        <div class="form-group" :class="{'has-danger': errors.affectation_igv_type_id}">
                                            <label class="control-label">Tipo de afectación
                                                <el-tooltip class="item" effect="dark" content="Tipo de afectación predeterminada al registrar nuevo producto" placement="top-start">
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <el-select v-model="form.affectation_igv_type_id" @change="submit" filterable>
                                                <el-option v-for="option in affectation_igv_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                            </el-select>
                                            <small class="form-control-feedback" v-if="errors.affectation_igv_type_id" v-text="errors.affectation_igv_type_id[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Impuesto incluido en registro de productos</label>
                                        <div class="form-group" :class="{'has-danger': errors.include_igv}">
                                            <el-switch v-model="form.include_igv" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.include_igv" v-text="errors.include_igv[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Caja General seleccionada por defecto</label>
                                        <div class="form-group" :class="{'has-danger': errors.destination_sale}">
                                            <el-switch v-model="form.destination_sale" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.destination_sale" v-text="errors.destination_sale[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <a href="#" @click.prevent="showDialogAllowanceCharge = true" class="text-center font-weight-bold text-info">[+ Aplicar cargos]</a>
                                        <el-tooltip
                                            class="item"
                                            effect="dark"
                                            content="Disponible en Ventas - Comprobante electrónico"
                                            placement="top-start">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Modificar Tipo de afectación (Gravado - Bonificación)
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                        content="Disponible Nuevo CPE"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.change_free_affectation_igv}">
                                            <el-switch v-model="form.change_free_affectation_igv" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.change_free_affectation_igv" v-text="errors.change_free_affectation_igv[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Moneda predeterminada
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Solo en Nota de venta y CPE"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                            <el-select v-model="form.currency_type_id"   filterable>
                                                <el-option v-for="option in config.currency_types"
                                                           :key="option.id"
                                                           :value="option.id"
                                                           :label="option.symbol+' - '+option.description"></el-option>
                                            </el-select>
                                            <small
                                                class="form-control-feedback"
                                                v-if="errors.currency_type_id"
                                                v-text="errors.currency_type_id[0]"></small>
                                        </div>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3" name="third">
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
                                        <div class="form-group" :class="{'has-danger': errors.options_pos}">
                                            <el-switch v-model="form.options_pos" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.options_pos" v-text="errors.options_pos[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Restringir fecha de comprobante</label>
                                        <div class="form-group" :class="{'has-danger': errors.restrict_receipt_date}">
                                            <el-switch v-model="form.restrict_receipt_date" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.restrict_receipt_date" v-text="errors.restrict_receipt_date[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir generar comprobante de pago desde cotización a vendedores</label>
                                        <div class="form-group" :class="{'has-danger': errors.quotation_allow_seller_generate_sale}">
                                            <el-switch v-model="form.quotation_allow_seller_generate_sale" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.quotation_allow_seller_generate_sale" v-text="errors.quotation_allow_seller_generate_sale[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir editar precio unitario a vendedores</label>
                                        <div class="form-group" :class="{'has-danger': errors.allow_edit_unit_price_to_seller}">
                                            <el-switch v-model="form.allow_edit_unit_price_to_seller" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.allow_edit_unit_price_to_seller" v-text="errors.allow_edit_unit_price_to_seller[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir crear productos a vendedores</label>
                                        <div class="form-group" :class="{'has-danger': errors.seller_can_create_product}">
                                            <el-switch v-model="form.seller_can_create_product" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.seller_can_create_product" v-text="errors.seller_can_create_product[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Permitir Ver el saldo en balance de finanzas a vendedores</label>
                                        <div class="form-group" :class="{'has-danger': errors.seller_can_view_balance}">
                                            <el-switch v-model="form.seller_can_view_balance" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.seller_can_view_balance" v-text="errors.seller_can_view_balance[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">
                                            Permite habilitar las acciones para vendedores
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Disponible en Oportunidad de Venta y Pedidos"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>

                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.seller_can_generate_sale_opportunities}">
                                            <el-switch v-model="form.seller_can_generate_sale_opportunities" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.seller_can_generate_sale_opportunities" v-text="errors.seller_can_generate_sale_opportunities[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Productos de una ubicación</label>
                                        <div class="form-group" :class="{'has-danger': errors.product_only_location}">
                                            <el-switch v-model="form.product_only_location" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.product_only_location" v-text="errors.product_only_location[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Seleccionar boleta por defecto
                                            <el-tooltip class="item" effect="dark" content="Disponible POS" placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.default_document_type_03}">
                                            <el-switch v-model="form.default_document_type_03" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.default_document_type_03" v-text="errors.default_document_type_03[0]"></small>
                                        </div>
                                    </div>
                                    <!-- Para elementos de farmacia -->
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Habilita elementos de farmacia
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                        content="Añade Codigo DIGEMID en Empresa y Codigo DIGEMID para productos, junto con el registro salitario"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.is_pharmacy}">
                                            <el-switch v-model="form.is_pharmacy" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.is_pharmacy" v-text="errors.is_pharmacy[0]"></small>
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
                                                <el-switch v-model="form.auto_send_dispatchs_to_sunat" active-text="Si"
                                                           inactive-text="No" @change="submit"></el-switch>
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
                                                effect="dark"
                                                        content="Disponible POS"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.active_warehouse_prices}">
                                            <el-switch v-model="form.active_warehouse_prices" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.active_warehouse_prices" v-text="errors.active_warehouse_prices[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Buscar producto por serie
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                        content="Disponible Nuevo CPE"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.search_item_by_series}">
                                            <el-switch v-model="form.search_item_by_series" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.search_item_by_series" v-text="errors.search_item_by_series[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Seleccionar precio disponible - Lista de precios
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                        content="Disponible POS"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.select_available_price_list}">
                                            <el-switch v-model="form.select_available_price_list" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.select_available_price_list" v-text="errors.select_available_price_list[0]"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">
                                            Muestra campos opcionales para los Items a modo informativo
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                        content="Disponible en CPE"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.show_extra_info_to_item}">
                                            <el-switch v-model="form.show_extra_info_to_item" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.show_extra_info_to_item" v-text="errors.show_extra_info_to_item[0]"></small>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Agrupar productos y cantidades - Generar CPE

                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                        content="Agrupar/Sumar productos y cantidades al generar cpe desde múltiples notas de venta"
                                                placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.group_items_generate_document}">
                                            <el-switch v-model="form.group_items_generate_document" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.group_items_generate_document" v-text="errors.group_items_generate_document[0]"></small>
                                        </div>
                                    </div>
                                    
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3" name="fourth">
                                <span slot="label"><h3>PDF</h3></span>
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Editar nombre de productos</label>
                                        <div class="form-group" :class="{'has-danger': errors.edit_name_product}">
                                            <el-switch v-model="form.edit_name_product" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.edit_name_product" v-text="errors.edit_name_product[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <a href="#" @click.prevent="showDialogTermsCondition = true" class="text-center font-weight-bold text-info">[+ Términos y condiciones - Cotización]</a>
                                        <br>
                                        <br>
                                        <a href="#" @click.prevent="showDialogTermsConditionSales = true" class="text-center font-weight-bold text-info">[+ Términos y condiciones - Ventas]</a>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Mostrar cotización en finanzas</label>
                                        <div class="form-group" :class="{'has-danger': errors.cotizaction_finance}">
                                            <el-switch v-model="form.cotizaction_finance" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.cotizaction_finance" v-text="errors.cotizaction_finance[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label class="control-label">Mostrar leyenda en footer - pdf
                                            <el-tooltip class="item" effect="dark" placement="top-start">
                                                <div slot="content">Leyenda: Bienes transferidos y/o servicios prestados en la Amazonía para ser consumidos en la misma<br/>Disponible para facturas, boletas, notas y cotizaciones</div>
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                        </label>
                                        <div class="form-group" :class="{'has-danger': errors.legend_footer}">
                                            <el-switch v-model="form.legend_footer" active-text="Si" inactive-text="No" @change="submit"></el-switch>
                                            <small class="form-control-feedback" v-if="errors.legend_footer" v-text="errors.legend_footer[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">Imágen para encabezado - pdf
                                                <el-tooltip class="item" content="Disponible para facturas y boletas en formato a4, usando la plantilla header_image_full_width"
                                                            effect="dark"
                                                            placement="top-start">
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <el-input v-model="form.header_image" :readonly="true">
                                                <el-upload slot="append"
                                                           :headers="headers"
                                                           :on-success="successUpload"
                                                           :show-file-list="false"
                                                           action="/configurations/uploads">
                                                    <el-button icon="el-icon-upload" type="primary"></el-button>
                                                </el-upload>
                                            </el-input>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">Mostrar ticket 58mm
                                                <el-tooltip class="item" effect="dark" placement="top-start">
                                                    <div slot="content">Disponible para Ventas (Facturas/Boletas/Notas
                                                                        de Crédito-Débito)
                                                    </div>
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <div :class="{'has-danger': errors.ticket_58}" class="form-group">
                                                <el-switch v-model="form.ticket_58" active-text="Si" inactive-text="No"
                                                           @change="submit"></el-switch>
                                                <small v-if="errors.ticket_58" class="form-control-feedback"
                                                       v-text="errors.ticket_58[0]"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- update_document_on_dispaches -->
                                    <div class="col-md-6 mt-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Actualizar documento al generar guía.
                                                <el-tooltip class="item" effect="dark" placement="top-start">
                                                    <div slot="content">Al generar una guia basado en el documento, se
                                                                        actualizará el comprobante de pago
                                                    </div>
                                                    <i class="fa fa-info-circle"></i>
                                                </el-tooltip>
                                            </label>
                                            <div :class="{'has-danger': errors.update_document_on_dispaches}"
                                                 class="form-group">
                                                <el-switch v-model="form.update_document_on_dispaches" active-text="Si"
                                                           inactive-text="No" @change="submit"></el-switch>
                                                <small v-if="errors.update_document_on_dispaches"
                                                       class="form-control-feedback"
                                                       v-text="errors.update_document_on_dispaches[0]"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3" name="five">
                                <span slot="label"><h3>Finanzas</h3></span>
                                <div class="row">
                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <label>Aplicar penalidad a los pagos vencidos</label>
                                            <el-switch v-model="form.finances.apply_arrears" @change="submit" active-text="Si" inactive-text="No" ></el-switch>
                                        </div>
                                        <div class="form-group" v-if="form.finances.apply_arrears" style="max-width: 300px;">
                                            <label>Cantidad a aplicar por día</label>
                                            <el-input placeholder="Please input" v-model="form.finances.arrears_amount" class="input-with-select">
                                                <el-button slot="append" @click="submit">
                                                    <i class="fa fa-save"></i>
                                                </el-button>
                                            </el-input>
                                        </div>
                                    </div>
                                </div>
                            </el-tab-pane>
                            <el-tab-pane class="mb-3" name="six">
                                <span slot="label"><h3>Datos</h3></span>
                                <div class="row">
                                    <div class="col-12 mt-4">
                                        <tenant-options-form></tenant-options-form>
                                    </div>
                                </div>
                            </el-tab-pane>
                        </el-tabs>
                        <terms-condition :showDialog.sync="showDialogTermsCondition"
                            :form="form"
                            :showClose="false"></terms-condition>
                        <terms-condition-sale :showDialog.sync="showDialogTermsConditionSales"
                            :form="form"
                            :showClose="false"></terms-condition-sale>

                        <allowance-charge :showDialog.sync="showDialogAllowanceCharge"
                            :form="form"
                            :showClose="false"></allowance-charge>
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
        props:['typeUser'],
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
                    visual: {}
                },
                affectation_igv_types: [],
                placeholder:'',
                activeName: 'first'
            }
        },
        created() {
            this.loadConfiguration()
            this.form = this.config;
        },
        mounted() {
             this.loadTables()
             this.initForm();
             this.$http.get(`/${this.resource}/record`) .then(response => {
                if (response.data !== ''){
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
            events(){

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
                    this.$message({message:'Error al subir el archivo', type: 'error'})
                }
            },
            async getRecord(){

                await this.$http.get(`/${this.resource}/record`) .then(response => {
                    if (response.data !== ''){
                    this.form = response.data.data;
                    }
                    // console.log(this.placeholder)
                });
            },
            async loadTables(){

                await this.$http.get(`/${this.resource}/tables`) .then(response => {
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
                    subtotal_account:null,
                    decimal_quantity: null,
                    amount_plastic_bag_taxes: 0.1,
                    colums_grid_item: 4,
                    affectation_igv_type_id:'10',
                    terms_condition:null,
                    header_image: null,
                    legend_footer: false,
                    default_document_type_03: false,
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
                    group_items_generate_document: false
                };
            },
            submit() {
                this.loading_submit = true;

                this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
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
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            }
        }
    }
</script>
