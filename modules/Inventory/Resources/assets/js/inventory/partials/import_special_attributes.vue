<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" class="dialog-import">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-12 form-group" :class="{'has-danger': errors.warehouse_id}">
                        <label for="warehouse">Almacén</label>
                        <el-select v-model="form.warehouse_id">
                            <el-option v-for="w in warehouses" :key="w.id" :label="w.description" :value="w.id"></el-option>
                        </el-select>
                        <small class="form-control-feedback" v-if="errors.warehouse_id" v-text="errors.warehouse_id[0]"></small>
                    </div>
                    <div class="col-12 form-group" :class="{'has-danger': errors.file}">
                        <el-upload
                                ref="upload"
                                :headers="headers"
                                :action="resource"
                                :show-file-list="true"
                                :auto-upload="false"
                                :multiple="false"
                                :on-error="errorUpload"
                                :before-upload="onBeforeUpload"
                                :limit="1"
                                :data="form"
                                :on-success="successUpload">
                            <el-button slot="trigger" type="primary">Seleccione un archivo (xlsx)</el-button>
                        </el-upload>
                        <small class="form-control-feedback" v-if="errors.file" v-text="errors.file[0]"></small>
                    </div>
                    <div class="col-12 mt-4 mb-2">
                        <a class="text-dark mr-auto" :href="url_format_example" target="_new">
                            <span class="mr-2">Descargar formato de ejemplo para importar</span>
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-5">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Procesar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog', 'specialAttributeType'],
        data() {
            return {
                loading_submit: false,
                headers: headers_token,
                titleDialog: null,
                resource: null,
                errors: {},
                form: {},
                warehouses: [],
                url_format_example: null,
            }
        },
        async created() {
            this.initForm()
            await this.getTables()
        },
        methods: {
            onBeforeUpload(file) 
            {

            },
            async getTables() 
            {
                await this.$http.get('/items/import/tables').then(response => {
                    this.warehouses = response.data.warehouses
                })
            },
            initForm() 
            {
                this.errors = {}
                this.form = {
                    warehouse_id: null
                }
            },
            create() 
            {
                this.setData()
            },
            setData()
            {
                this.resource = `items/import-${this.specialAttributeType}`

                if(this.specialAttributeType === 'item-lots-group')
                {
                    this.titleDialog = 'Importar productos con lotes'
                    this.url_format_example = '/formats/movement_item_lots_group.xlsx'
                }
                else if(this.specialAttributeType === 'item-lots')
                {
                    this.titleDialog = 'Importar productos con series'
                    this.url_format_example = '/formats/movement_item_lots.xlsx'
                }

            },
            async submit() 
            {
                if(!this.form.warehouse_id) return this.$message.error('Seleccione un almacén para poder continuar')

                this.loading_submit = true
                await this.$refs.upload.submit()
                this.loading_submit = false
            },
            close() 
            {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            successUpload(response, file, fileList) 
            {
                if (response.success) 
                {
                    this.$message.success(response.message)
                    this.$eventHub.$emit('reloadData')
                    this.$refs.upload.clearFiles()
                    this.close()
                }
                else 
                {
                    this.$message({message:response.message, type: 'error'})
                }
            },
            errorUpload(error) 
            {
                console.log(error)
            }
        }
    }
</script>
