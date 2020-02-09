<template>
  <div class="wrap">
    <router-link to="/" v-if="shouldDisplayTournamentsLink" class="pseudo-link bright">
      Все турниры
    </router-link>
    <router-link :to="tournamentLink" v-if="shouldDisplayStagesLink" class="pseudo-link bright">
      Все этапы
    </router-link>
  </div>
</template>

<script>
  export default {
    name: 'nav-bar',
    computed: {
      route: function() {
        return this.$route.name;
      },
      shouldDisplayTournamentsLink: function() {
        return this.route !== 'main';
      },
      shouldDisplayStagesLink: function() {
        return this.route === 'stage';
      },
      tournamentLink: function() {
        const id = this.$store.state.stages.byId[this.$route.params.id] &&
          this.$store.state.stages.byId[this.$route.params.id].tournament_id;

        return `/tournaments/${id}`;
      }
    }
  }
</script>

<style scoped>
  .wrap {
    padding: 1rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    justify-content: flex-start;
  }
</style>
