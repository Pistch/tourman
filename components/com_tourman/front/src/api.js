class Api {
  constructor(params = {}) {
    this.basePath = params.basePath;
  }

  makePath(params) {
    return this.basePath + Object.keys(params)
      .map(paramName => `${encodeURIComponent(paramName)}=${encodeURIComponent(params[paramName])}`).join('&');
  }

  get(path, options) {
    return fetch(path, options)
      .then(r => r.json())
      .catch(e => {
        console.error(e);
        // TODO: обработка ошибок
        return {};
      })
  }

  getTournaments() {
    return this.get(this.makePath({task: 'get-tournaments'}));
  }

  getTournamentStages(id) {
    return this.get(this.makePath({task: 'get-tournament', tournament: id}));
  }

  getTournamentRatings(id) {
    return this.get(this.makePath({task: 'get-tournament-rating', tournament: id}));
  }

  getStage(id) {
    return this.get(this.makePath({task: 'get-stage', stage: id}));
  }
}

export default new Api({
  basePath: 'https://bcpolygon.ru/index.php?option=com_tourman&'
});
