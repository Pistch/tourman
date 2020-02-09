import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    tournaments: {
      byId: {},
      byOrder: [],
      relevant: []
    },
    stages: {
      byId: {},
      byTournament: {}
    },
    players: {
      byId: {}
    }
  },
  mutations: {
    storeTournaments(state, payload) {
      state.tournaments.byId = {
        ...state.tournaments.byId,
        ...payload
      };

      state.tournaments.byOrder = Object.keys(payload).map(Number).sort((n1, n2) => n2 - n1);

      const relevantTournaments = [];
      const now = Date.now();

      state.tournaments.byOrder.forEach(id => {
        if (Date.parse(payload[id].end_date) >= now) {
          relevantTournaments.push(id);
        }
      });

      state.tournaments.relevant = relevantTournaments;
    },
    storeStages(state, {id, stages}) {
      state.stages.byTournament = {
        ...state.stages.byTournament,
        [id]: stages.map(s => s.id).map(Number).sort((n1, n2) => n1 - n2)
      };

      stages.forEach(stage => (state.stages.byId[stage.id] = stage));
    },
    storeStage(state, stage) {
      state.stages.byId = {
        ...state.stages.byId,
        [stage.id]: stage
      };
    },
    storeTournamentRating(state, {tournamentId, ratings}) {
      state.tournaments.byId = {
        ...state.tournaments.byId,
        [tournamentId]: {
          ...state.tournaments.byId[tournamentId],
          ratings
        }
      };
    }
  }
});

export default store;
