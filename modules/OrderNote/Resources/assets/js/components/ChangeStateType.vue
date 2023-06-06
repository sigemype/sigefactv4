<template>
  <div>
    <template v-if="state != '11'">
      <template v-for="(state_type, index) in state_types">
        <button :key="index" @click="send(state_type.id)"
                type="button"
                class="btn waves-effect waves-light btn-xs"
                :class="state_type.class"
                v-if="state_type.id != state">
          {{ state_type.description }}
        </button>
      </template>
    </template>
  </div>
</template>

<script>
export default {
  props: ['state', 'id'],
  data() {
    return {
      resource: 'order-notes',
      form: {},
      state_types: [
        {id:'01', description: 'Pendiente', class: 'btn-warning'},
        {id:'03', description: 'Por Entregar', class: 'btn-info'},
        {id:'05', description: 'Entregado', class: 'btn-success'}
      ],
    }
  },
  created() {
    this.initForm()
  },
  methods: {
    initForm() {
      this.form.id = this.id
      this.form.state_type_id = this.state
    },
    send(id) {
      this.form.id = this.id
      this.form.state_type_id = id

      this.$http.post(`${this.resource}/update/state`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message)
          } else {
            this.$message.error('No se guardaron los cambios')
          }
        })
        .catch(error => {
          console.log(error)
        })

      this.reload()
    },
    reload(){
      this.$eventHub.$emit('reloadData')
    }
  }
}
</script>