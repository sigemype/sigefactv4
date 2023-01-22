<template>
    <el-dialog :title="titleDialog" :visible="showDialog"   @close="close" @open="create"   append-to-body top="7vh" width="30%">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <el-upload
                            ref="upload_images"
                            list-type="picture-card"
                            :file-list="file_list"
                            :headers="headers"
                            :action="`/general-upload-temp-image`"
                            :on-success="onSuccess"
                            :on-remove="onRemove"
                            :on-exceed="onExceed"
                            :limit="4">
                            <i class="el-icon-plus"></i>
                        </el-upload>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2 mt-3">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" @click.prevent="clickSubmit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>


    export default {
        props:['showDialog'],
        data() {
            return {
                recordId: null,
                titleDialog: 'Agregar imágenes',
                headers: headers_token,
                resource: 'configurations',
                file_list: [],
                loading_submit: false,
            }
        },
        created() {
        },
        methods: {
            onExceed()
            {
                this.$message.error('No puede cargar más de 4 imagénes.')
            },
            clearData()
            {
                if(this.$refs.upload_images) this.$refs.upload_images.clearFiles()
                this.file_list = []
            },
            onRemove(file)
            {
                this.removeFile(file.uid)
            },
            onSuccess(response, file, fileList)
            {
                if(response.success)
                {
                    const data = { ...response.data }
                    data.url = data.temp_image

                    this.file_list.push(data)
                }
                else 
                {
                    this.removeFile(file.uid)
                    this.removeCurrentFileList(fileList, file.uid) //elimina la imagen de la vista por error o archivo no permitido 
                    this.$message.error(response.message)
                }
            },
            removeCurrentFileList(fileList, uid)
            {
                _.remove(fileList, {uid : uid})
            },
            removeFile(uid)
            {
                _.remove(this.file_list, (row) => {
                    return row.uid === uid
                })
            },
            cleanFileList()
            {
                this.file_list = []
            },
            clickSubmit()
            {
                if(this.file_list.length == 0) return this.$message.error('Debe cargar al menos una imágen')

                this.loading_submit = true
                this.$http.post(`/${this.resource}/pdf-footer-images`, {images : this.file_list}).then(response => {

                        const data = response.data
                        
                        if (data.success) 
                        {
                            this.$message.success(data.message)
                        } 
                        else 
                        {
                            this.$message.error(data.message)
                        }

                    })
                    .catch(error => {
                        console.log(error)
                    })
                    .then(() => {
                        this.loading_submit = false
                        this.close()
                    })
            },
            create()
            {
                this.getImages()
            },
            getImages()
            {
                this.$http.get(`/${this.resource}/get-pdf-footer-images`)
                    .then(response => {
                        this.file_list = response.data
                    })
            },
            close() 
            {
                this.clearData()
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>
