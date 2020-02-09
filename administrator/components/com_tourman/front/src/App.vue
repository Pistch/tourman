<template>
  <a-locale-provider :locale="ru_RU">
    <div id="app">
      <a-menu
        :value="route"
        mode="horizontal"
        @click="navigate"
      >
        <a-menu-item key="main">
          Главная
        </a-menu-item>
        <a-menu-item key="tournaments">
          <a-icon type="trophy" />
          Турниры
        </a-menu-item>
        <a-menu-item key="players">
          <a-icon type="team" />
          Игроки
        </a-menu-item>
        <a-menu-item key="service">
          <a-icon type="setting" />
          Обслуживание
        </a-menu-item>
      </a-menu>
      <router-view></router-view>
      <div class="gag" v-if="loading" @click.stop></div>
    </div>
  </a-locale-provider>
</template>

<script>
  import ru_RU from 'ant-design-vue/lib/locale-provider/ru_RU';

  export default {
    name: 'App',
    mounted: function() {
      this.$store.dispatch('getTournaments');
    },
    computed: {
      route: function() {
        return [this.$route.name];
      },
      loading: function() {
        return this.$store.state.interface.loading;
      }
    },
    data() {
      return {
        current: 'main',
        ru_RU
      }
    },
    methods: {
      navigate({key}) {
        console.log(key);
        this.$router.push({name: key});
      }
    }
  }
</script>

<style scoped>
  .nav {
    display: flex;
    gap: 30px;
  }

  .router-link-exact-active {
    color: black;
  }

  .gag {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.4);
    z-index: 10000;
  }
</style>
