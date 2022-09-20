<template>

        <div class="row" :loading="loading">
            <div class="col-lg-12">
                <div class="row">
                        <div class="col-md-12">
                            <div style="margin:3px" class="table-responsive">
                                <h5 class="separator-title">
                                    Productos
                                </h5>
                                <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Cantidad</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in items" :key="index">
                                        <td class="text-center">{{ setDescriptionOfItem(row.item) }}
                                        {{
                                        row.item.presentation.hasOwnProperty('description') ?
                                        row.item.presentation.description : ''
                                        }}</td>
                                        <td class="text-center">{{row.quantity}}</td>
                                        <td class="series-table-actions text-right">
                                            
                                            <template v-if="row.item.lots_enabled">
                                                <button class="btn waves-effect waves-light btn-xs btn-primary" @click.prevent="openDialogLotsGroup(index, row)">
                                                    <i class="el-icon-check"></i> Lotes
                                                </button>
                                            </template>

                                            <button  type="button" class="btn waves-effect waves-light btn-xs btn-success" @click.prevent="openDialogLots(row.item.lots, row.item_id)">
                                                    <i class="el-icon-check"></i> Series
                                            </button>
                                            <button  type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="openSelectWarehouses(row, index)">
                                                <i class="el-icon-search"></i> Stock
                                            </button>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                </div>
            </div>

            <!-- <lots :showDialog.sync="showDialogLots" :lots="lots"></lots> -->

            <select-lots-form
                :showDialog.sync="showDialogLots"
                :lots="lots"
                :itemId="item_id"
                @addRowSelectLot="addRowSelectLot">
            </select-lots-form>

            <select-warehouses
                :showDialog.sync="showSelectWarehouses"
                :warehouses="selectWarehouses"
                :item_index="item_index">
            </select-warehouses>

            <select-lots-group
                :lots_group="lots_group"
                :quantity="lots_group_quantity"
                :showDialog.sync="showDialogLotsGroup"
                @addRowLotGroup="addRowLotGroup">
            </select-lots-group>

        </div>

</template>

<script>
    // import Lots from "./lots.vue";
    import SelectLotsForm from '../../documents/partials/lots.vue'
    import SelectWarehouses from './select_warehouses.vue'
    import {showNamePdfOfDescription} from '../../../../helpers/functions'
    import SelectLotsGroup from '../../documents/partials/lots_group.vue'

    export default {
        props:['items','config'],
        components:{SelectLotsForm, SelectWarehouses, SelectLotsGroup},

        data()
        {
            return{
                showSelectWarehouses: false,
                selectWarehouses: [],
                showDialogLots: false,
                lots:[],
                item_id: null,
                item_index: -1,
                showDialogLotsGroup:false,
                lots_group_quantity:0,
                lots_group: [],
                current_index_item: -1,
                loading: false,
            }
        },
        created(){
            // console.log(this.items)
            
            this.$eventHub.$on('selectWarehouseId', (data) => {
                // console.log(data)
                this.items[data.index].warehouse_id = data.warehouse_id
            })

        },
        methods:{
            addRowLotGroup(lots_selecteds){
                this.items[this.current_index_item].IdLoteSelected = lots_selecteds
                this.current_index_item = -1
            },
            async openDialogLotsGroup(index, row){
                
                await this.getLotsGroup(row.item_id)
                await this.regularizeCompromiseQuantity(row)
                this.current_index_item = index
                this.lots_group_quantity = row.quantity
                this.showDialogLotsGroup = true

            },
            regularizeCompromiseQuantity(row){

                if(row.IdLoteSelected)
                {
                    this.lots_group.forEach(l_group => {
                        
                        const lot = _.find(row.IdLoteSelected, {id : l_group.id})

                        if(lot) l_group.compromise_quantity = lot.compromise_quantity 

                    })
                }

            },
            async getLotsGroup(item_id){

                this.loading = true

                await this.$http.get(`/item-lots-group/available-data/${item_id}`)
                    .then((response) => {
                        this.lots_group = response.data
                    })
                    .then(()=>{
                        this.loading = false
                    })

            },
            // verifyWarehouse(row){

            //     let warehouse = _.find(this.selectWarehouses, {warehouse_id : row.warehouse_id, checked : true})
            //     return warehouse ? true : false

            // },
            // checkedWarehouse(){

            //     _.forEach(this.selectWarehouses, row => _.set(row, 'checked', this.verifyWarehouse(row)))

            // },
            async openSelectWarehouses(row, index) {

                if(!row.consulted_stock){

                    await this.$http.get(`/quotations/item-warehouses/${row.item_id}`)
                        .then((response) => {
                            
                            // this.selectWarehouses = response.data
                            row.item.warehouses = response.data
                            row.consulted_stock = true
                            // this.checkedWarehouse()
                            // row.item.warehouses = this.selectWarehouses
    
                        });

                }

                this.selectWarehouses = row.item.warehouses
                this.item_index = index
                this.showSelectWarehouses = true

            }, 
            openDialogLots(lt, item_id)
            {
                this.item_id = item_id
                this.showDialogLots = true
                this.lots = lt
            },
            addRowSelectLot(){

            },
            setDescriptionOfItem(item) {
                return showNamePdfOfDescription(item, this.config.show_pdf_name)
            },

        }
    }
</script>
 
