<template>
  <div v-if="stage" @touchmove.stop @touchstart.stop @touchend.stop @pointermove.stop @pointerdown.stop @pointerup.stop @click.stop @mousedown.stop @mousemove.stop @mouseup.stop>
    <h2>{{stage.title}}</h2>
    <grid :stage="stage" v-if="stage.games" />
    <table v-if="results" class="results-table">
      <thead>
        <tr class="row">
          <td class="cell">Место</td>
          <td class="cell players">Игроки</td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(players, place) in results" class="row">
          <td class="cell">{{getPlaceRange(place)}}</td>
          <td class="cell players">
            <ul>
              <li v-for="player in players">{{player}}</li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  import api from '../api';
  import Grid from '../components/Grid.vue';

  const placesMap = {
    '2-1': {
      1: 1,
      2: 2,
      3: '3-4',
      5: '5-8',
      9: '9-12',
      13: '13-16',
      17: '17-24',
      25: '25-32',
    }
  };

  export default {
    name: 'Stage',
    components: {
      Grid
    },
    mounted: function() {
      this.id = this.$router.history.current.params.id;
      api.getStage(this.id)
        .then(data => {
          this.$store.commit('storeStage', data);
        });
    },
    data: function() {
      return {
        id: 0
      };
    },
    computed: {
      stage: function() {
        return this.$store.state.stages.byId[this.id];
      },
      results: function() {
        const resultsArray = this.$store.state.stages.byId[this.id].results;

        if (!resultsArray) {
          return {};
        }

        const resultsGrouped = {};

        resultsArray.forEach(result => {
          if (!resultsGrouped[result.place]) {
            resultsGrouped[result.place] = [];
          }

          resultsGrouped[result.place].push(result.user);
        });

        return resultsGrouped;
      }
    },
    methods: {
      getPlaceRange(place) {
        return placesMap[this.stage.net_type][place];
      }
    }
  }
</script>

<style scoped>
  .results-table {
    margin: 1rem;
    float: left;
  }

  .row {
    transition: .2s background-color;
  }

  .row:hover {
    background-color: rgba(255, 255, 255, .1);
  }

  .cell {
    padding: .2rem 3rem;
    border: 1px #FFF solid;
    color: #FFF;
  }

  .cell.players:hover {
    box-shadow: -3px 0 0 0 rgba(255, 255, 255, .1);
  }

</style>
