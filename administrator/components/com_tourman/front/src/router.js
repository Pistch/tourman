import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Main from './pages/Main.vue';
import Service from './pages/Service.vue';
import Players from './pages/Players.vue';
import Tournaments from './pages/Tournaments.vue';

const routes = [
  {
    path: '/',
    name: 'main',
    component: Main
  },
  {
    path: '/tournaments',
    name: 'tournaments',
    component: Tournaments
  },
  {
    path: '/players',
    name: 'players',
    component: Players
  },
  {
    path: '/service',
    name: 'service',
    component: Service
  }
];

export default new VueRouter({
  routes
});
