<template>
  <span :class="state.class">{{ state.description }}</span>
</template>

<script>
import {mapActions, mapState} from "vuex"

export default {
  props: ['id','description'],
  data() {
    return {
      resource: 'order-notes',
      state: {
        description: 'Registrado',
        class: ''
      },
      options: [
        {id:'01', description: 'Pendiente', class: 'text-warning'},
        {id:'03', description: 'Por Entregar', class: 'text-info'},
        {id:'05', description: 'Entregado', class: 'text-success'},
        {id:'11', description: 'Anulado', class: 'text-danger'}
      ]
    }
  },
  created() {
    this.$store.commit('setConfiguration', this.configuration)
    this.loadConfiguration()
  },
  mounted(){
    this.setDescription()
  },
  watch: {
    id: function(){ this.setDescription() }
  },
  computed:{
    ...mapState([
        'config',
    ]),
  },
  methods: {
    ...mapActions([
      'loadConfiguration',
    ]),
    setDescription(){
      if(this.config.order_node_advanced){
        this.state = this.options.find(e => e.id == this.id)
      } else {
        this.state.description = this.description
      }
    }
  },
}
</script>

<style>

</style>