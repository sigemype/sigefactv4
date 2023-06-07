<template>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Descripción</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in form.items">
                        <td class="text-center"> {{ index + 1}} </td>
                        <td> 
                            {{ row.item.description }}

                            <template v-if="fnIsRestrictedForSale(row.item, form.document_type_id)">
                                <span class="text-danger mt-1 mb-2 d-block">Restringido para venta en CPE</span>
                            </template>

                        </td>
                        <td class="text-center"> {{ row.quantity }}</td>
                        <td class="text-center">
                            <template v-if="fnIsRestrictedForSale(row.item, form.document_type_id)">
                                <button type="button"
                                        class="btn waves-effect waves-light btn-xs btn-danger mt-2"
                                        @click.prevent="clickRemoveItem(index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

import { functions, fnRestrictSaleItemsCpe, operationsForDiscounts } from '@mixins/functions'
import { sumAmountDiscountsNoBaseByItem } from '@helpers/functions'

export default {
    mixins: [
        fnRestrictSaleItemsCpe,
        operationsForDiscounts,
        functions
    ],
    props: {
        form: {
            required: true,
        },
        configuration: {
            required: true,
        },
        globalDiscountTypes: {
            required: false,
            default: [],
        }
    },
    computed:
    {
    },
    data() 
    {
        return {
            config: {}
        }
    },
    async created() 
    {
        this.config = this.configuration
        this.global_discount_types = this.globalDiscountTypes
        await this.getPercentageIgv()
        await this.setConfigGlobalDiscountType()
        await this.setDataDiscounts()
    },
    methods: 
    {
        clickRemoveItem(index) 
        {
            this.form.items.splice(index, 1)
            this.calculateTotal()
        },
        async calculateTotal() 
        {
            let total_discount = 0
            let total_charge = 0
            let total_exportation = 0
            let total_taxed = 0
            let total_exonerated = 0
            let total_unaffected = 0
            let total_free = 0
            let total_igv = 0
            let total_value = 0
            let total = 0
            this.total_discount_no_base = 0

            let total_igv_free = 0

            this.form.items.forEach((row) => {
                total_discount += parseFloat(row.total_discount)
                total_charge += parseFloat(row.total_charge)

                if (row.affectation_igv_type_id === '10') {
                    total_taxed += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '20'  // 20,Exonerado - Operación Onerosa
                    || row.affectation_igv_type_id === '21' // 21,Exonerado – Transferencia Gratuita
                ) {
                    total_exonerated += parseFloat(row.total_value)
                }
                if (
                    row.affectation_igv_type_id === '30'  // 30,Inafecto - Operación Onerosa
                    || row.affectation_igv_type_id === '31'  // 31,Inafecto – Retiro por Bonificación
                    || row.affectation_igv_type_id === '32'  // 32,Inafecto – Retiro
                    || row.affectation_igv_type_id === '33'  // 33,Inafecto – Retiro por Muestras Médicas
                    || row.affectation_igv_type_id === '34'  // 34,Inafecto - Retiro por Convenio Colectivo
                    || row.affectation_igv_type_id === '35'  // 35,Inafecto – Retiro por premio
                    || row.affectation_igv_type_id === '36' // 36,Inafecto - Retiro por publicidad
                    || row.affectation_igv_type_id === '37'  // 37,Inafecto - Transferencia gratuita
                ) {
                    total_unaffected += parseFloat(row.total_value)
                }

                if (row.affectation_igv_type_id === '40') {
                    total_exportation += parseFloat(row.total_value)
                }
                
                if (['10',
                    '20', '21',
                    '30', '31', '32', '33', '34', '35', '36',
                    '40'].indexOf(row.affectation_igv_type_id) < 0) {
                    total_free += parseFloat(row.total_value)
                }
                if (['10',
                    '20', '21',
                    '30', '31', '32', '33', '34', '35', '36',
                    '40'].indexOf(row.affectation_igv_type_id) > -1) {
                    total_igv += parseFloat(row.total_igv)
                    total += parseFloat(row.total)
                }


                if (['11', '12', '13', '14', '15', '16'].includes(row.affectation_igv_type_id)) {

                    let unit_value = row.total_value / row.quantity
                    let total_value_partial = unit_value * row.quantity
                    row.total_taxes = row.total_value - total_value_partial

                    row.total_igv = total_value_partial * (row.percentage_igv / 100)
                    row.total_base_igv = total_value_partial
                    total_value -= row.total_value

                    total_igv_free += row.total_igv
                }

                total_value += parseFloat(row.total_value)

                this.total_discount_no_base += sumAmountDiscountsNoBaseByItem(row)

            });

            this.form.total_igv_free = _.round(total_igv_free, 2)

            this.form.total_discount = _.round(total_discount, 2)
            this.form.total_exportation = _.round(total_exportation, 2)
            this.form.total_taxed = _.round(total_taxed, 2)
            this.form.total_exonerated = _.round(total_exonerated, 2)
            this.form.total_unaffected = _.round(total_unaffected, 2)
            this.form.total_free = _.round(total_free, 2)
            this.form.total_igv = _.round(total_igv, 2)
            this.form.total_value = _.round(total_value, 2)
            this.form.total_taxes = _.round(total_igv, 2)

            this.form.subtotal = _.round(total, 2)
            this.form.total = _.round(total - this.total_discount_no_base, 2)

            this.discountGlobal()
        },
        prepareDataGlobalDiscount(data)
        {
            const discounts = data.discounts ? Object.values(data.discounts) : []

            if(discounts.length === 1)
            {
                if(discounts[0].is_amount !== undefined && discounts[0].is_amount !== null)
                {
                    this.is_amount = discounts[0].is_amount
                }

                this.total_global_discount = this.is_amount ?  discounts[0].amount : (discounts[0].factor * 100)

            }

            return discounts
        },
        setDataDiscounts()
        {
            this.form.discounts = this.prepareDataGlobalDiscount(this.form)
        },

    },
}
</script>
