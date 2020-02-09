<template>
  <div>
    <h2>
      Игроки
      <button @click="startCreatingPlayer"> + </button>
    </h2>
    <div>
      <suggest
        :items="suggestions"
        label="Найти игрока:"
        @input="findPlayer"
        placeholder="Начните набирать имя игрока"
        @pick="pickPlayer"
      />

      <player-card v-if="player" :player="player" />
      <new-player :active="creatingPlayer" :submit="upsertPlayer" @close="stopCreatingPlayer" />
    </div>
  </div>
</template>

<script>
  import api from '../api';

  import Suggest from '../Suggest.vue';
  import PlayerCard from '../components/PlayerCard.vue';
  import NewPlayer from '../components/NewPlayer.vue';

  export default {
    name: 'players',
    components: {
      Suggest,
      PlayerCard,
      NewPlayer
    },
    data: function() {
      return {
        suggestions: [],
        player: null,
        newPlayer: {},
        creatingPlayer: false
      }
    },
    methods: {
      findPlayer(e) {
        this.inputValue = e.target.value.trim();

        if (!this.inputValue) {
          return;
        }

        api.suggestUser(e.target.value)
          .then(d => {
            this.suggestions = Object.values(d).map(i => ({...i, display: i.name}));
          });
      },
      pickPlayer(player) {
        this.player = player;
        this.suggestions = [];
      },
      startCreatingPlayer() {
        this.creatingPlayer = true;
      },
      stopCreatingPlayer() {
        this.creatingPlayer = false;
      },
      upsertPlayer(player) {
        this.$store.commit('startLoading');
        api.upsertPlayer(player)
          .then(player => {
            this.pickPlayer(player);
            this.$store.commit('stopLoading');
            this.stopCreatingPlayer();
          })
          .catch(() => {
            this.$store.commit('stopLoading');
          })
      }
    }
  }
</script>

<style scoped>

</style>
