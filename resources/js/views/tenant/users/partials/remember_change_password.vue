<template>
</template>

<script>

    export default {
        props: [
            'configurationLastPasswordUpdate'
        ],
        data() {
            return {
                resource: 'system-activity-logs',
            }
        },
        created() {
            this.checkLastPasswordUpdate()
        },
        methods: {
            checkLastPasswordUpdate()
            {
                this.$http.post(`/${this.resource}/generals/check-last-password-update`, this.configurationLastPasswordUpdate)
                    .then(response => {
                        if(response.data.success)
                        {
                            this.$notify({
                                title: 'Recordatorio',
                                message: response.data.message,
                                type: 'warning',
                                duration: 4000
                            })
                        }
                    })
            },
        }
    }
</script>
