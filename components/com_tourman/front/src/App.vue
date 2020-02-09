<template>
  <div id="app">
    <nav-bar />
    <div :class="['wrapper', wrapperClassName]">
      <router-view v-if="appReady" />
    </div>
  </div>
</template>

<script>
  import api from './api';
  import NavBar from './router/NavBar.vue';

  const routesOrder = ['main', 'tournament', 'stage'];

  export default {
    name: 'app',
    components: {
      NavBar
    },
    mounted: function() {
      this.getTournaments()
        .then(() => {
          this.appReady = true;
        });
    },
    watch: {
      '$route': function(to, from) {
        const toDepth = routesOrder.indexOf(to.name);
        const fromDepth = routesOrder.indexOf(from.name);

        this.wrapperClassName = toDepth < fromDepth ? 'transition_cn_right' : 'transition_cn_left';
        setTimeout(() => this.dropWrapperCN(), 20);
      }
    },
    data () {
      return {
        appReady: false,
        wrapperClassName: ''
      }
    },
    computed: {},
    methods: {
      getTournaments() {
        return api.getTournaments()
          .then(data => this.$store.commit('storeTournaments', data));
      },

      dropWrapperCN() {
        this.wrapperClassName = '';
      }
    }
  }
</script>

<style>
  .wrapper {
    transition: .3s all;
  }

  .transition_cn_left {
    transition: none;
    opacity: .5;
    transform: translateX(40%);
  }

  .transition_cn_right {
    transition: none;
    opacity: .5;
    transform: translateX(-40%);
  }

  body {
    background-color: #465568;
    font-family: Helvetica, Arial, sans-serif;
  }

  #app {
    color: #000;
    font-family: Helvetica, Arial, sans-serif;
  }

  #app h1, #app h2 {
    font-family: Helvetica, Arial, sans-serif;
    color: #fff;
  }

  .card-material {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    margin: 1rem;
    border: 1px #aaa solid;
    background-color: #ffffff;
    box-shadow: 0 0 7px 1px rgba(0,0,0,.5);
    transition: all .2s;
    color: #666666;
    text-decoration: none;
    flex-basis: 27%;
    flex-shrink: 0;
    cursor: pointer;
  }

  @media screen and (max-width: 768px) {
    .card-material {
      flex-basis: 100%;
    }
  }

  .card-material img {
    height: 300px;
    flex-shrink: 0;
  }

  .card-material:hover {
    box-shadow: 2px 2px 7px 3px rgba(0,0,0,.5);
    transform: translate(-2px, -2px);
  }

  .card-material .call-to-action {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 4rem;
    border: none;
    outline: none;
    border-radius: .3rem;
    width: 100%;
    background: #312f38;
    color: #ffffff;
    font-size: 1.5rem;
    text-decoration: none;
    transition: all .2s;
  }

  .card-material .call-to-action:hover {
    opacity: 0.9;
  }

  .pseudo-link {
    display: inline-block;
    text-decoration: none;
    padding: .1rem;
    transition: .1s all;
    cursor: pointer;
  }

  .pseudo-link + .pseudo-link {
    margin-left: .6rem;
  }

  .pseudo-link.bright {
    border-bottom: 1px #ccc dashed;
    color: #ccc;
  }

  .pseudo-link.bright:hover, .pseudo-link.bright:active {
    color: #999;
    border-bottom-color: #999;
  }

  .pseudo-link.dark {
    border-bottom: 1px #333 dashed;
    color: #333;
  }

  .pseudo-link.dark:hover, .pseudo-link.dark:active {
    color: #999;
    border-bottom-color: #999;
  }
</style>
