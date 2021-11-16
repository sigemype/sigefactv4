<template>
    <div class="row">
        <div class="col-md-6 mt-4">
            <label class="control-label">
                Poder cambiar el IGV global de los items en la compra.
            </label>
            <div :class="{'has-danger': errors.enabled_global_igv_to_purchase}"
                 class="form-group">
                <el-switch
                    v-model="config.enabled_global_igv_to_purchase"
                    active-text="Si"
                    inactive-text="No"
                    @change="ChangeEnabledGlobalIgvToPurchase"></el-switch>
                <small v-if="errors.enabled_global_igv_to_purchase"
                       class="form-control-feedback"
                       v-text="errors.enabled_global_igv_to_purchase[0]"></small>
            </div>
        </div>

    </div>
</template>

<script>

import {mapActions, mapState} from "vuex";

export default {
    props: [
        'errors'
    ],
    data() {
        return {}
    },
    created() {
        this.loadConfiguration()
    },
    computed: {
        ...mapState([
            'config',
        ]),
    },
    methods: {
        ...mapActions([
            'loadConfiguration',
        ]),
        ChangeEnabledGlobalIgvToPurchase() {
            let conf = this.config.enabled_global_igv_to_purchase;
            // Para cada configuracion. Guarsdarla ebn el global.
            this.$store.commit('setEnabledGlobalIgvToPurchase', conf)
            this.$emit('EmitChange', conf)

        }
    }
}
</script>
