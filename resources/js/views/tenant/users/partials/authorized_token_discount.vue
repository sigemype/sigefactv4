<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
        width="30%"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12" v-if="form.token">
                        <div class="form-group">
                            <label class="control-label">Token</label>
                            <el-input v-model="form.token"></el-input>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-3">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit" v-if="form.token == null">Generar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog'],
        data() {
            return {
                loading_submit: false,
                headers: null,
                resource: 'authorized-discount-users',
                errors: {},
                form: {},
                titleDialog: 'Generar token de autorización'
            }
        },
        created() {
            this.initForm()
        },
        methods: {
            create()
            {

            },
            close()
            {
                this.$emit("update:showDialog", false)
                this.initForm()
            },
            initForm() 
            {
                this.errors = {}

                this.form = {
                    token: null,
                }
            },
            submit()
            {
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {

                        if (response.data.success) 
                        {
                            this.form = response.data.data
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
        }
    }
</script>
