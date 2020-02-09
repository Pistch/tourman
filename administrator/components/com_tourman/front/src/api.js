class Api {
  constructor() {
    this.basePath = '/administrator/index.php?option=com_tourman&';
  }

  makePath(params) {
    return this.basePath + Object.keys(params)
      .map(paramName => `${encodeURIComponent(paramName)}=${encodeURIComponent(params[paramName])}`).join('&');
  }

  request(pathOptions, requestOptions) {
    return fetch(this.makePath(pathOptions), requestOptions)
      .then(r => r.json())
      .catch(e => {
        console.error(e);

        return e;
      })
  }

  get(pathOptions = {}, requestOptions = {}) {
    return this.request(pathOptions, requestOptions);
  }

  post(pathOptions = {}, requestOptions = {}) {
    const body = new FormData();

    if (requestOptions.body) {
      Object.keys(requestOptions.body).forEach(key => {
        body.append(key, requestOptions.body[key]);
      });
    }

    return this.request(pathOptions, {
      ...requestOptions,
      method: 'POST',
      body
    });
  }

  loadStage(stageId) {
    return this.get({task: 'get-stage', stage: stageId});
  }

  getTournaments() {
    return this.get({task: 'get-tournaments'});
  }

  getDuedGames() {
    return this.get({task: 'get-dued-games'});
  }

  getTournamentStages(id) {
    return this.get({task: 'get-tournament', tournament: id});
  }

  setMatchScore(id, pl1_score, pl2_score) {
    const body = {
      id,
      pl1_score,
      pl2_score
    };

    return this.post({task: 'set-match-score'}, {body});
  }

  startMatch(id, table, dueTime) {
    const body = {
      due_time: dueTime,
      id
    };

    if (table) {
      body.table = table;
    }

    return this.post({task: 'start-match'}, {body});
  }

  finalizeMatch(matchId, winnerPhasePlacement) {
    const body = {matchId};

    if (winnerPhasePlacement !== undefined) {
      body.winnerPhasePlacement = winnerPhasePlacement;
    }

    return this.post({task: 'finalize-match'}, {body});
  }

  submitTournamentData(body) {
    return this.post({task: 'upsert-tournament'}, {body});
  }

  deleteTournament(body) {
    return this.post({task: 'remove-tournament'}, {body});
  }

  submitTournamentStageData(body) {
    return this.post({task: 'upsert-stage'}, {body});
  }

  deleteStage(body) {
    return this.post({task: 'remove-stage'}, {body});
  }

  suggestUser(input) {
    return this.get({
      task: 'suggest-user',
      q: input
    });
  }

  upsertPlayer(body) {
    return this.post({task: 'upsert-user'}, {body});
  }

  submitTournamentRegistrationUsers(users, stageId) {
    const body = {
      userIds: JSON.stringify(users.map(u => u.id)),
      stageId
    };

    return this.post({task: 'register-players-to-stage'}, {body});
  }

  unregisterPlayerFromStage(userId, stageId) {
    const body = {userId, stageId};

    return this.post({task: 'unregister-player-from-stage'}, {body});
  }

  closeRegistration(stageId) {
    const body = {stageId};

    return this.post({task: 'close-registration'}, {body});
  }

  closeStage(stageId) {
    const body = {stageId};

    return this.post({task: 'close-stage'}, {body});
  }

  recalculateRatingsByPeriod(from, to) {
    const body = {from, to};

    return this.post({task: 'recalculate-rating-by-period'}, {body});
  }

  swapPlayer(stage_id, wrong_player_id, right_player_id) {
    const body = {stage_id, wrong_player_id, right_player_id};

    return this.post({task: 'swap-player'}, {body});
  }

  resetGame(game_id) {
    const body = {game_id};

    return this.post({task: 'reset-game'}, {body});
  }

  setPlayerStageHandicap(params) {
    return this.post({task: 'set-player-stage-handicap'}, {body: params});
  }
}

export default new Api();
