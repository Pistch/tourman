import Vue from 'vue';
import Vuex from 'vuex';
import moment from 'moment';

import {formatDateTime, formatDate} from './utils/format';
import api from './api';

export function cloneDeep(obj) {
  if (!(obj instanceof Object)) {
    return obj;
  }

  const result = {};

  Object.keys(obj).forEach(keyName => {
    if (obj[keyName] instanceof Object) {
      if (Array.isArray(obj[keyName])) {
        result[keyName] = obj[keyName].map(cloneDeep);
      } else {
        result[keyName] = cloneDeep(obj[keyName]);
      }
    } else {
      result[keyName] = obj[keyName];
    }
  });

  return result;
}

Vue.use(Vuex);

const now = moment();
export const TOURNAMENT_TEMPLATE = {
  title: '',
  description: '',
  reglament: '',
  discipline: '',
  logo: '',
  net_type: '2-1',
  is_rating: false,
  start_date: formatDate(now.clone()),
  end_date: formatDate(now.clone().add(7, 'days'))
};
export const STAGE_TEMPLATE = {
  title: '',
  net_size: '16',
  net_type: '2-1',
  discipline: '',
  start_date: formatDateTime(now.clone()),
  end_date: formatDateTime(now.clone().add(7, 'days')),
  entry_fee: 0,
  status: 1
};

const store = new Vuex.Store({
  state: {
    tournaments: {
      byId: {},
      byOrder: [],
      ongoing: []
    },
    stages: {
      byId: {},
      byTournament: {}
    },
    users: {

    },
    interface: {
      selectedTournament: null,
      selectedStage: null,
      loading: false
    },
    duedGames: {
      games: []
    }
  },
  mutations: {
    startLoading(state) {
      state.interface.loading = true;
    },
    stopLoading(state) {
      state.interface.loading = false;
    },
    storeTournaments(state, payload) {
      const byOrder = [],
        ongoing = [],
        byId = {};

      (Array.isArray(payload) ? payload.forEach() : Object.values(payload))
        .forEach(tournament => {
          if (Date.parse(tournament.end_date) >= Date.now()) {
            ongoing.push(tournament.id);
          }

          byOrder.push(tournament.id);
          byId[tournament.id] = tournament;
        });

      state.tournaments.byOrder = byOrder.reverse();
      state.tournaments.ongoing = ongoing;
      state.tournaments.byId = byId;
    },
    storeTournament(state, tournament) {
      state.tournaments.byId = {
        ...state.tournaments.byId,
        [tournament.id]: tournament
      };

      if (!state.tournaments.byOrder.includes(tournament.id)) {
        state.tournaments.byOrder = state.tournaments.byOrder.concat(tournament.id);
      }

      if (Array.isArray(tournament.stages)) {
        const stagesById = {};
        const stagesByTournament = [];

        tournament.stages.forEach(stage => {
          stagesById[stage.id] = stage;
          stagesByTournament.push(stage.id);
        });

        state.stages.byId = {
          ...state.stages.byId,
          ...stagesById
        };

        state.stages.byTournament = {
          ...state.stages.byTournament,
          [tournament.id]: stagesByTournament
        }
      } else {
        state.stages.byTournament = {
          ...state.stages.byTournament,
          [tournament.id]: []
        }
      }
    },
    storeStage(state, stage) {
      state.stages.byId = {
        ...state.stages.byId,
        [stage.id]: stage
      };

      if (!state.stages.byTournament[stage.tournament_id]) {
        state.stages.byTournament = {
          ...state.stages.byTournament,
          [stage.tournament_id]: [stage.id]
        }
      }

      if (!state.stages.byTournament[stage.tournament_id].includes(stage.id)) {
        state.stages.byTournament = {
          ...state.stages.byTournament,
          [stage.tournament_id]: state.stages.byTournament[stage.tournament_id].concat(stage.id)
        }
      }
    },
    selectTournament(state, tournamentId) {
      if (tournamentId) {
        if (state.tournaments.byId[tournamentId]) {
          state.interface.selectedTournament = cloneDeep(state.tournaments.byId[tournamentId] || {});
        }
      } else {
        state.interface.selectedTournament = cloneDeep(TOURNAMENT_TEMPLATE);
      }
    },
    deselectTournament(state) {
      state.interface.selectedTournament = null;
    },
    selectStage(state, stageId) {
      if (stageId) {
        if (state.stages.byId[stageId]) {
          state.interface.selectedStage = cloneDeep(state.stages.byId[stageId] || {});
        }
      } else {
        state.interface.selectedStage = {
          ...cloneDeep(STAGE_TEMPLATE),
          tournament_id: state.interface.selectedTournament.id
        };
      }
    },
    deselectStage(state) {
      state.interface.selectedStage = null;
    },
    editSelectedTournament(state, data) {
      state.interface.selectedTournament = {
        ...state.interface.selectedTournament,
        ...data
      };
    },
    editSelectedStage(state, data) {
      state.interface.selectedStage = {
        ...state.interface.selectedStage,
        ...data
      }
    },
    getDuedGamesSuccess(state, data = []) {
      state.duedGames.games = data;
    }
  },
  actions: {
    getTournaments({commit}) {
      commit('startLoading');
      return api.getTournaments()
        .then(tournaments => {
          commit('storeTournaments', tournaments);
          commit('stopLoading');
          return tournaments;
        });
    },
    selectTournament({dispatch, commit}, id) {
      commit('startLoading');
      return dispatch('getTournament', id)
        .then(tournament => {
          commit('selectTournament', id);

          if (tournament && tournament.stages && tournament.stages.length) {
            dispatch('selectStage', tournament.stages[tournament.stages.length - 1].id);
          }
        });
    },
    reloadStage({dispatch, state}) {
      dispatch('selectStage', state.interface.selectedStage.id);
    },
    selectStage({dispatch, commit}, id) {
      commit('selectStage', id);
      return dispatch('getStage', id)
        .then(() => commit('selectStage', id));
    },
    getTournament({commit}, id) {
      commit('startLoading');
      return api.getTournamentStages(id)
        .then(tournament => {
          commit('storeTournament', tournament);
          commit('stopLoading');
          return tournament;
        });
    },
    getStage({commit}, stageId) {
      commit('startLoading');
      return api.loadStage(stageId)
        .then(stage => {
          commit('storeStage', stage);
          commit('stopLoading');
          return stage;
        });
    },
    saveTournament({dispatch, commit, state}) {
      commit('startLoading');
      return api.submitTournamentData({
        ...state.interface.selectedTournament,
        is_rating: Number(state.interface.selectedTournament.is_rating)
      })
        .then(({id}) => {
          commit('stopLoading');
          dispatch('getTournament', id);
        })
        .then(({id}) => commit('selectTournament', id));
    },
    deleteTournament({dispatch, commit, state}) {
      commit('startLoading');
      return api.deleteTournament(state.interface.selectedTournament)
        .then((result) => {
          commit('stopLoading');

          if (result.error) {
            alert(result.error);
            return
          }

          commit('deselectTournament');
          dispatch('getTournaments');
        });
    },
    saveStage({dispatch, commit, state}) {
      commit('startLoading');
      return api.submitTournamentStageData(state.interface.selectedStage)
        .then((stage) => {
          commit('storeStage', stage);
          commit('stopLoading');
          commit('selectStage', stage.id);
        });
    },
    deleteStage({dispatch, commit, state}) {
      commit('startLoading');
      return api.deleteStage(state.interface.selectedStage)
        .then((result) => {
          commit('stopLoading');

          if (result.error) {
            alert(result.error);
            return
          }

          commit('deselectStage');
          dispatch(
            'getTournament',
            state.interface.selectedTournament &&
            state.interface.selectedTournament.id
          );
        });
    },
    closeStage({commit, dispatch, state}) {
      commit('startLoading');
      const stageId = state.interface.selectedStage.id;
      return api.closeStage(stageId)
        .then(() => {
          commit('stopLoading');
          dispatch('reloadStage');
        });
    },
    getDuedGames({commit}) {
      api.getDuedGames()
        .then(games => {
          commit('getDuedGamesSuccess', Array.isArray(games) ? games : []);
        });
    }
  }
});

export default store;
