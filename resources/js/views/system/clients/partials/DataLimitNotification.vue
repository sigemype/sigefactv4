<template>
    <div :style="style_div">
        <template v-if="exceedsLimit">
            <el-popover
                :content="`El límite de ${entity_description} fue superado.`"
                placement="top-start"
                trigger="hover"
                width="220"
            >
                <label slot="reference" class="text-danger">
                    <strong>{{ quantity }}</strong>
                </label>
            </el-popover>
        </template>
        <template v-else>
            <label>
                <strong>{{ quantity }}</strong>
            </label>
        </template>
        /                                
        <template v-if="unlimited">
            <i class="fas fa-infinity"></i>
        </template>
        <template v-else>
            <strong>{{ max_quantity }}</strong>
        </template>
    </div>
</template>

<script>

    export default {
        props: {
            style_div: {
                type: String,
                required: false,
                default: ''
            },
            entity_description: {
                type: String,
                required: true
            },
            unlimited: {
                type: Boolean,
                required: true
            },
            quantity: {
                required: true
            },
            max_quantity: {
                required: true
            },
        },
        data() 
        {
            return {
            }
        },
        computed:
        {
            exceedsLimit()
            {
                return !this.unlimited && (parseFloat(this.quantity) > parseFloat(this.max_quantity))
            }
        },
        async created() 
        {
        },
        methods: 
        { 
        }
    }
</script>