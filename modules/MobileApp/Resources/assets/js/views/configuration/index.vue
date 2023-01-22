<template>
    <div v-loading="loading">
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> App 2.0</span></li>
                <li class="active"><span> Configuración</span></li>
            </ol>
            <div class="right-wrapper pull-right"></div>
        </div>
        <div class="row">
            <div class="col-md-7">

                <div class="row">
                    <div class="short-div col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>Configuración gráfica</h4>
                                <el-form ref="form" :model="form" label-width="145px" size="mini">
                                    <el-tabs tab-position="left" style="min-height: 200px;" @tab-click="handleClick">
                                        <el-tab-pane label="Color de tema">
                                            <el-radio-group class="pt-2" v-model="form.theme_color" @change="changeThemePrimary()">
                                                <div class="row">
                                                    <div class="col-sm-6 col-lg-3">
                                                        <el-radio label="blue">Azul</el-radio>
                                                        <div style="width: 40px; height: 40px; background-color: #4A2CB3;"></div>
                                                    </div>
                                                    <div class="col-sm-6 col-lg-3">
                                                        <el-radio label="red">Rojo</el-radio>
                                                        <div style="width: 40px; height: 40px; background-color: #cb2027;"></div>
                                                    </div>
                                                    <div class="col-sm-6 col-lg-3 mt-sm-3 mt-lg-0">
                                                        <el-radio label="dark">Oscuro</el-radio>
                                                        <div style="width: 40px; height: 40px; background-color: #1A1F1D;"></div>
                                                    </div>
                                                    <div class="col-sm-6 col-lg-3 mt-sm-3 mt-lg-0">
                                                        <el-radio label="premium" disabled>Premium</el-radio>
                                                        <div style="width: 40px; height: 40px; background-color: #00B19A;"></div>
                                                    </div>
                                                </div>
                                            </el-radio-group>
                                        </el-tab-pane>
                                        <el-tab-pane label="Color de cajas">
                                            <el-radio-group class="pt-2" v-model="form.card_color" @change="changeThemeCards()">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <el-radio style="display:block;" label="multicolored">Multicolor</el-radio>
                                                        <div :class="'box_color_tint_'+form.theme_color" style="width: 30px; height: 30px; display: inline-block;"></div>
                                                        <div :class="'box_color_'+form.theme_color" style="width: 30px; height: 30px; display: inline-block;"></div>
                                                        <div :class="'box_color_shade_'+form.theme_color" style="width: 30px; height: 30px; display: inline-block;"></div>
                                                    </div>
                                                    <div class="col-6">
                                                        <el-radio label="unicolor">Unicolor</el-radio>
                                                        <div :class="'box_color_'+form.theme_color" style="width: 20px; height: 20px;"></div>
                                                    </div>
                                                </div>
                                            </el-radio-group>
                                        </el-tab-pane>
                                        <el-tab-pane label="Encabezado">
                                            <el-radio-group class="pt-2" v-model="form.header_waves" @change="changeHeaderWaves()">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <el-radio :label="0" style="display:block">Plano</el-radio>
                                                        <div style="width: 150px; height: 40px; background-color: #4A2CB3;"></div>
                                                    </div>
                                                    <div class="col-6">
                                                        <el-radio :label="1" style="display:block;">Ondulado</el-radio>
                                                        <div style="width: 150px; height: 10px; background-color: #4A2CB3;"></div>
                                                        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 180" preserveAspectRatio="none" style="width: 150px;height: 120px;">
                                                            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" style="fill:#4A2CB3;"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </el-radio-group>
                                        </el-tab-pane>
                                        <el-tab-pane label="Modo">
                                            <el-radio-group class="pt-2" v-model="form.app_mode" @change="changeMode()">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <el-radio label="default" style="display:block">Standard</el-radio>
                                                    </div>
                                                    <div class="col-6">
                                                        <el-radio label="pos" style="display:block;">Punto de venta</el-radio>
                                                    </div>
                                                </div>
                                            </el-radio-group>
                                        </el-tab-pane>
                                        <el-tab-pane label="Pack Premium">
                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <!-- <el-tooltip
                                                        class="item"
                                                        content="Usa WhatsApp Cloud Api (Configuración/Empresa) - Disponible en el modo Punto de venta"
                                                        effect="dark"
                                                        placement="top-start">
                                                        <el-checkbox v-model="form.direct_send_documents_whatsapp">Envío directo de comprobantes por WhatsApp</el-checkbox>
                                                    </el-tooltip> -->
                                                    <ul class="list-unstyled">
                                                        <li class="">
                                                            <i class="fas fa-check fa-fw me-4 text-success"></i>
                                                            Envío directo de Tickets PDF por WhatsApp
                                                        </li>
                                                        <li class="">
                                                            <i class="fas fa-check fa-fw me-4 text-success"></i>
                                                            Mejor calidad de impresión para tickets
                                                        </li>
                                                        <li class="">
                                                            <i class="fas fa-check fa-fw me-4 text-success"></i>
                                                            Base de datos de productos de Plaza Vea
                                                        </li>
                                                        <li class="">
                                                            <i class="fas fa-check fa-fw me-4 text-success"></i>
                                                            Color de tema extra disponible
                                                        </li>
                                                        <li class="">
                                                            <i class="fas fa-check fa-fw me-4 text-success"></i>
                                                            Publicación en Play Store
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p>Para mayor información comunícate con tu administrador o distribuidor</p>
                                        </el-tab-pane>
                                        <!-- <el-tab-pane label="Tipo de operación">
                                            <el-radio-group class="pt-2" v-model="form.operation_type">
                                                <el-radio :label="1">Facturación</el-radio>
                                                <el-radio :label="2">POS</el-radio>
                                            </el-radio-group>
                                        </el-tab-pane> -->
                                    </el-tabs>
                                    <div class="form-actions text-right mt-4">
                                        <el-button type="primary" @click.prevent="submit" :loading="loading_submit">Guardar</el-button>
                                    </div>
                                </el-form>
                            </div>
                        </div>
                    </div>
                    <div class="short-div col-md-12">
                        <tenant-mobile-app-permissions></tenant-mobile-app-permissions>
                    </div>
                </div>

            </div>
            <div class="col-md-5">
                <video class="iphone-x" v-if="show_premium" autoplay="" loop="" muted="" playsinline="" width="350" style="height:620px">
                    <source src="https://facturaloperu.com/video/FacturaloPeru-APPPremium.mp4" type="video/mp4">
                </video>
                <iframe v-else v-loading="loading_iframe" :src="source_iframe" frameborder="0" height="750" ref="appIframe" style="z-index: 999;min-width: 350px;" class="iphone-x">
                    <i>Speaker</i>
                    <b>Camera</b>
                </iframe>
            </div>

        </div>
    </div>
</template>

<style scoped lang="scss">
.iphone-x {
  position: relative;
  margin: 25px auto;
  min-width: 330px;
  height: 750px;
  background-color: #ccc;
  border-radius: 40px;
  box-shadow: 0px 0px 0px 7px #1f1f1f, 0px 0px 0px 13px #191919,
    0px 0px 0px 14px #111;

  &:before,
  &:after {
    content: "";
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  // frontal camera/speaker frame
  &:before {
    top: 0px;
    width: 56%;
    height: 30px;
    background-color: #1f1f1f;
    border-radius: 0px 0px 40px 40px;
  }

  // speaker
  i {
    top: 0px;
    left: 50%;
    transform: translate(-50%, 6px);
    height: 8px;
    width: 15%;
    background-color: #101010;
    border-radius: 8px;
    box-shadow: inset 0px -3px 3px 0px rgba(256, 256, 256, 0.2);
  }

  // camera
  b {
    left: 10%;
    top: 0px;
    transform: translate(180px, 4px);
    width: 12px;
    height: 12px;
    background-color: #101010;
    border-radius: 12px;
    box-shadow: inset 0px -3px 2px 0px rgba(256, 256, 256, 0.2);

    &:after {
      content: "";
      position: absolute;
      background-color: #2d4d76;
      width: 6px;
      height: 6px;
      top: 2px;
      left: 2px;
      top: 3px;
      left: 3px;
      display: block;
      border-radius: 4px;
      box-shadow: inset 0px -2px 2px rgba(0, 0, 0, 0.5);
    }
  }
}
.box_color_shade_dark {
    background-color: #000000;
}
.box_color_tint_dark {
    background-color: #141414;
}
.box_color_dark {
    background-color: #1A1F1D;
}
.box_color_shade_blue {
    background-color: #331B83;
}
.box_color_tint_blue {
    background-color: #4A2CB3;
}
.box_color_blue {
    background-color: #3827B4;
}
.box_color_shade_red {
    background-color: #CC0D00;
}
.box_color_tint_red {
    background-color: #A30B00;
}
.box_color_red {
    background-color: #cb2027;
}
</style>

<script>
    export default {
        data() {
            return {
                form: {},
                domain: window.location.origin,
                path_app: window.location.origin + '/live-app',
                path_premium: window.location.origin + '/live-app/premium',
                source_iframe: '',
                loading_submit: false,
                resource: 'app-configurations',
                loading: false,
                change_mode: false,
                show_premium: false,
                loading_iframe: true,
            }
        },
        async created(){
            await this.initForm()
            await this.getRecord()
        },
        mounted(){
            this.checkConfiguration()
        },
        methods: {
            handleClick(tab, event) {
                if(tab.index == 4) {
                    this.show_premium = true
                } else {
                    this.show_premium = false
                }
                // console.log(tab, event);
            },
            async getRecord(){

                this.loading = true

                await this.$http.get(`/${this.resource}/record`)
                        .then(response => {
                            this.form = response.data.data
                            // console.log(response.data.data)
                        })
                        .then(()=>{
                            this.loading = false
                        })

            },
            initForm(){

                this.form = {
                    theme_color: 'blue',
                    card_color: 'multicolored',
                    header_waves: false,
                    app_mode: 'default',
                    direct_send_documents_whatsapp: false,
                    // operation_type: 1,
                    // permissions: {},
                }
                this.source_iframe = this.path_app

            },
            changeMode()
            {
                this.change_mode = true
            },
            changeThemePrimary() {

                let iframe = this.$refs.appIframe
                let doc = iframe.contentDocument

                switch (this.form.theme_color) {
                    case 'red':
                        doc.head.querySelectorAll('[href="' + this.domain + '/liveapp/assets/skin-dark.css"]').forEach(el => el.remove())
                        doc.head.innerHTML = doc.head.innerHTML + '<link href="' + this.domain + '/liveapp/assets/skin-red.css" rel="stylesheet">'
                        break
                    case 'dark':
                        doc.head.querySelectorAll('[href="' + this.domain + '/liveapp/assets/skin-red.css"]').forEach(el => el.remove())
                        doc.head.innerHTML = doc.head.innerHTML + '<link href="' + this.domain + '/liveapp/assets/skin-dark.css" rel="stylesheet">'
                        break
                    default:
                        doc.head.querySelectorAll('[href="' + this.domain + '/liveapp/assets/skin-dark.css"]').forEach(el => el.remove())
                        doc.head.querySelectorAll('[href="' + this.domain + '/liveapp/assets/skin-red.css"]').forEach(el => el.remove())
                        break
                }

            },
            changeThemeCards() {
                let iframe = this.$refs.appIframe
                let doc = iframe.contentDocument

                switch (this.form.card_color) {
                    case 'unicolor':
                        doc.head.innerHTML = doc.head.innerHTML + '<link href="' + this.domain + '/liveapp/assets/cards.css" rel="stylesheet">'
                        break
                    default:
                        doc.head.querySelectorAll('[href="' + this.domain + '/liveapp/assets/cards.css"]').forEach(el => el.remove())
                        break
                }
            },
            changeHeaderWaves() {
                let iframe = this.$refs.appIframe
                let doc = iframe.contentDocument
                let waves = doc.body.querySelectorAll('div.waves')

                switch (this.form.header_waves) {
                    case 0:
                        waves.forEach(el => {
                            el.parentNode.classList.remove('display-flex')
                            el.parentNode.classList.add('display-none')
                        })
                        break
                    default:
                        waves.forEach(el => {
                            el.parentNode.classList.add('display-flex')
                            el.parentNode.classList.remove('display-none')
                        })
                        break
                }
            },
            async checkConfiguration(){

                const iframe = this.$refs.appIframe
                const context = this

                await iframe.addEventListener('load', function() {
                    context.changeThemePrimary()
                    context.changeThemeCards()
                    context.changeHeaderWaves()
                })

            },
            async submit() {

                this.loading_submit = true
                await this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {

                            this.$message.success(response.data.message)
                            // if(this.change_mode) location.reload() //reinicia el modo pos, pero valida la configuracion con la url que se inicia, por ejemplo demo, no del proyecto local

                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            }
        }
    }
</script>
