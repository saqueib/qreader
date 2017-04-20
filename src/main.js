import Vue from 'vue'
import element from 'element-ui'
import router from './router'
import store from './store'

// Use plugins
Vue.use(element)

// Components
import App from './App'

// Configs
Vue.config.devtools = true
Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
