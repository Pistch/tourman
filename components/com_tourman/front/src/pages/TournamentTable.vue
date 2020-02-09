<template>
  <div>
    <h2 class="controls">Турниры</h2>
    <div class="controls">
      <span class="controls__label">Показывать:</span>
      <switcher
        :value="isOutdatedShown"
        :items="[
          {value: true, text: 'Все'},
          {value: false, text: 'Только текущие'}
        ]"
        @change="handleShowOutdatedChange"
      />
    </div>
    <div class="wrap">
      <div v-if="!isOutdatedShown && !tournaments.length" class="empty">
        К сожалению, в данный момент турниров не проводится.
        <span class="pseudo-link bright" @click="handleShowOutdatedClick">Смотреть прошедшие</span>
      </div>
      <router-link
        v-for="tournament in tournaments"
        :to="`/tournaments/${tournament.id}`"
        class="card-material"
        :key="tournament.id"
      >
        <img
          :src="tournament.logo || '/images/tournaments/no_logo.jpg'"
          :alt="tournament.title"
          class="logo"
        />
        <h5>
          {{tournament.title}}
        </h5>

        <p class="description">
          {{tournament.description}}
        </p>

        <div class="call-to-action">
          Открыть
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
  import Switcher from '../components/Switcher.vue';

  export default {
    name: 'tournament-table',
    components: {Switcher},
    data: function() {
      return {
        isOutdatedShown: false
      };
    },
    computed: {
      tournaments: function() {
        const {state} = this.$store;

        return state.tournaments[this.isOutdatedShown ? 'byOrder' : 'relevant'].map(
          id => state.tournaments.byId[id]
        )
      }
    },
    methods: {
      handleShowOutdatedChange(value) {
        this.isOutdatedShown = value;
      },
      handleShowOutdatedClick() {
        this.isOutdatedShown = true;
      }
    }
  }
</script>

<style scoped>
  .wrap {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-items: stretch;
  }

  .empty {
    margin: 70px;
    color: #ccc;
  }

  .controls {
    padding: 0 2rem;
  }

  .controls__label {
    color: #fff;
    margin-right: 10px;
  }
</style>
