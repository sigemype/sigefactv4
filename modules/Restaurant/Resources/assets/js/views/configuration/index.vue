<template>
    <div>
      <div class="page-header pr-0">
        <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
        <ol class="breadcrumbs">
          <li class="active"><span>Configuración</span></li>
          <li><span class="text-muted">Restaurante</span></li>
        </ol>
      </div>
      <template>
        <form autocomplete="off">
          <el-tabs v-model="activeName" type="border-card" class="rounded">
            <el-tab-pane class="mb-3"  name="first">
              <span slot="label">Visual</span>
              <div class="row">
                <div class="col-sm-6 col-md-4 mt-4">
                  <label class="control-label">
                    Habilitar menu POS
                  </label>
                  <div :class="{'has-danger': errors.menu_pos}"
                        class="form-group">
                    <el-switch v-model="form.menu_pos"
                                active-text="Si"
                                inactive-text="No"
                                @change="submit"></el-switch>
                    <small v-if="errors.menu_pos"
                            class="form-control-feedback"
                            v-text="errors.menu_pos[0]"></small>
                  </div>
                </div>
                <div class="col-sm-6 col-md-4 mt-4">
                  <label class="control-label">
                    Habilitar menu Mesas
                  </label>
                  <div :class="{'has-danger': errors.menu_tables}"
                        class="form-group">
                    <el-switch v-model="form.menu_tables"
                                active-text="Si"
                                inactive-text="No"
                                @change="submit"></el-switch>
                    <small v-if="errors.menu_tables"
                            class="form-control-feedback"
                            v-text="errors.menu_tables[0]"></small>
                  </div>
                </div>
                <div class="col-sm-6 col-md-4 mt-4">
                  <label class="control-label">
                    Habilitar menu Pedidos
                  </label>
                  <div :class="{'has-danger': errors.menu_order}"
                        class="form-group">
                    <el-switch v-model="form.menu_order"
                               active-text="Si"
                               inactive-text="No"
                               @change="submit"></el-switch>
                    <small v-if="errors.menu_order"
                           class="form-control-feedback"
                           v-text="errors.menu_order[0]"></small>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 mt-4">
                  <div :class="{'has-danger': errors.first_menu}"
                        class="form-group">
                    <label class="control-label">Menu inicial
                    </label>
                    <el-select v-model="form.first_menu"
                                filterable
                                @change="submit">
                      <el-option v-for="option in menu_list"
                                  :key="option.id"
                                  :label="option.description"
                                  :value="option.name"></el-option>
                    </el-select>
                    <small v-if="errors.first_menu"
                            class="form-control-feedback"
                            v-text="errors.first_menu[0]"></small>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 mt-4">
                  <div :class="{'has-danger': errors.tables_quantity}"
                        class="form-group">
                    <label class="control-label">Cantidad de mesas
                    </label>
                    <el-slider
                      v-model="form.tables_quantity"
                      :step="1"
                      max="20"
                      show-stops
                      @change="submit">
                    </el-slider>
                    <small v-if="errors.tables_quantity"
                            class="form-control-feedback"
                            v-text="errors.tables_quantity[0]"></small>
                  </div>
                </div>
              </div>
            </el-tab-pane>
          </el-tabs>
        </form>
      </template>
    </div>
</template>

<style>
.el-tabs__header,
.el-tabs__nav-wrap {
    border-top-right-radius: 5px;
    border-top-left-radius: 5px ;
}
</style>

<script>

export default {
    data() {
      return {
        resource: 'restaurant',
        errors: {},
        form: {
          menu_pos: true,
          menu_order: true,
          menu_tables: true,
          first_menu: 'POS',
          tables_quantity: 15
        },
        activeName: 'first',
        menu_list: [
          {id: 1, description: 'POS', name: 'POS'},
          {id: 2, description: 'Mesas', name: 'TABLES'},
          {id: 3, description: 'Pedidos', name: 'ORDER'},
        ]
      }
    },
    mounted() {
      this.$http.get(`/${this.resource}/configuration/record`).then(response => {
        if (response.data !== '') {
          this.form = response.data.data;
        }
      });
    },
    methods: {
      submit() {
        this.$http.post(`/${this.resource}/configuration`, this.form).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
          if (data !== undefined && data.configuration !== undefined) {
            this.form = data.configuration
          }
        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        }).then(() => {
          // this.loading_submit = false;
        });
      }
    }
}
</script>
