import Vue from 'vue';
import VueRouter from 'vue-router';

import TournamentTable from '../pages/TournamentTable.vue';
import Tournament from '../pages/Tournament.vue';
import Stage from '../pages/Stage.vue';
// import Ratings from '../pages/Ratings.vue';
// import Player from '../pages/Player.vue';

Vue.use(VueRouter);

export const routes = [
  {
    path: '/',
    name: 'main',
    component: TournamentTable
  },
  {
    path: '/tournaments/:id',
    name: 'tournament',
    component: Tournament
  },
  {
    path: '/stages/:id',
    name: 'stage',
    component: Stage
  }
];

const router = new VueRouter({
  routes
});

export default router;
