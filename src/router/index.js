import Vue from 'vue'
import Router from 'vue-router'

// Components
import Dashboard from '@/components/Dashboard'
import Channel from '@/components/Channel'
import Favs from '@/components/Favs'

// Use the router
Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: Dashboard
    },
    {
      path: '/channel/:index/:title',
      name: 'Channel',
      component: Channel,
      props: true
    },
    {
      path: '/favorites',
      name: 'Favs',
      component: Favs
    }
  ]
})
