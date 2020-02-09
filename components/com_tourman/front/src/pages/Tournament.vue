<template>
  <div class="wrap">
    <h2 class="title">{{tournament.title}}</h2>
    <div class="row">
      <div class="logo" v-if="tournament.logo">
        <img
          :src="tournament.logo"
          :alt="tournament.title"
        />
      </div>
      <div>
        <p class="date">{{getDatesRange(tournament)}}</p>
        <p>{{tournament.description}}</p>
        <template v-if="!complexReglament">
          <p v-if="tournament.reglament" class="pseudo-link bright" @click="toggleReglament">
            Регламент
          </p>
          <p v-if="tournament.reglament && reglamentShown">
            {{tournament.reglament}}
          </p>
        </template>
        <a class="pseudo-link bright" :href="tournament.reglament" target="_blank" v-if="complexReglament">
          Регламент
        </a>
      </div>

    </div>

    <section class="tabs">
      <switcher
        v-if="hasRatings"
        :value="currentView"
        :items="[
          {value: 'stages', text: 'Этапы'},
          {value: 'ratings', text: 'Рейтинги'}
        ]"
        @change="changeCurrentView"
      />

      <div :class="['tab', currentView === 'stages' ? 'active' : '']">
        <div v-if="!shouldDisplayStages" class="empty">
          Нет этапов
        </div>
        <ul v-if="shouldDisplayStages" class="stages">
          <li v-for="stage in stages" class="stage">
            <router-link
              :to="`/stages/${stage.id}`"
              :key="stage.id"
              class="pseudo-link dark"
            >
              {{stage.title}}
            </router-link>
            <span class="date">
          ({{getDatesRange(stage, true)}})
        </span>
          </li>
        </ul>
      </div>
      <div :class="['tab', currentView === 'ratings' ? 'active' : '']" v-if="hasRatings">
        <loader v-if="!tournament.ratings" />
        <ol v-if="tournament.ratings && tournament.ratings.length">
          <li v-for="ratingRecord in tournament.ratings">
            <strong>{{ratingRecord.points}}</strong> очков - {{ratingRecord.user_name}}
          </li>
        </ol>

        <div v-if="tournament.ratings && !tournament.ratings.length" class="empty">
          Пока нет рейтингов...
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import {mapState} from 'vuex';

  import api from '../api';
  import getDatesRange from '../helpers/getDatesRange';
  import Loader from '../components/Loader.vue';
  import Switcher from '../components/Switcher.vue';

  export default {
    name: 'tournament',
    components: {
      Switcher,
      Loader
    },
    mounted: function() {
      this.id = this.$router.history.current.params.id;
      api.getTournamentStages(this.id)
        .then(data => {
          this.$store.commit('storeStages', {
            id: this.id,
            stages: data.stages
          });
        });
    },
    data: function() {
      return {
        id: 0,
        reglamentShown: false,
        currentView: 'stages'
      };
    },
    computed: {
      ...mapState({
        tournament: function(state) {
          return state.tournaments.byId[this.id] || {};
        },
        stages: function(state) {
          const {stages} = state;

          return stages.byTournament[this.id] &&
            stages.byTournament[this.id].map(id => stages.byId[id]);
        },
        complexReglament: function () {
          return this.tournament.reglament && /^https:\/\//.test(this.tournament.reglament);
        }
      }),
      shouldDisplayStages: function() {
        return Array.isArray(this.stages) && this.stages.length > 0;
      },
      hasRatings: function () {
        return Boolean(this.tournament.is_rating);
      }
    },
    methods: {
      getDatesRange,
      goToTournamentTable() {
        this.$router.history.replace('/');
      },
      toggleReglament() {
        this.reglamentShown = !this.reglamentShown;
      },
      changeCurrentView(viewName) {
        this.currentView = viewName;
        if (viewName === 'ratings' && !this.tournament.ratings) {
          api.getTournamentRatings(this.tournament.id)
            .then(ratings => {
              this.$store.commit('storeTournamentRating', {
                tournamentId: this.tournament.id,
                ratings
              });
            });
        }
      }
    }
  }
</script>

<style scoped>
  .wrap {
    min-height: 300px;
    padding: .5rem;
    background: #FFF;
    border-radius: 10px;
  }

  @media screen and (min-width: 990px) {
    .wrap {
      margin: 0 1.5rem;
      padding: 1.5rem;
    }
  }

  .date {
    color: #999;
    font-size: 12px;
  }

  .title {
    color: #000 !important;
  }

  .row {
    display: flex;
    flex-wrap: wrap;
    margin: 10px 0;
  }

  .logo {
    flex-basis: 30%;
    flex-grow: 0;
    margin-right: 40px;
  }

  @media screen and (max-width: 600px) {
    .logo {
      flex-basis: 80%;
    }
  }

  .logo img {
    width: 100%;
  }

  @keyframes appear {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .tabs {
    padding: 1rem 2rem;
  }

  @media screen and (max-width: 768px) {
    .tabs {
      padding: .3rem 0;
    }
  }

  .tab {
    display: none;
  }

  .tab.active {
    display: block;
    animation: .5s appear;
  }

  .stages {
    padding: 0;
  }

  .stage {
    margin: 5px 0;
    list-style-type: none;
  }

  .empty {
    color: #777;
    margin: 70px 0;
  }

  @media screen and (min-width: 768px) {
    .empty {
      margin: 70px;
    }
  }
</style>
