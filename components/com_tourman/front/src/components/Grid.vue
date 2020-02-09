<template>
  <div>
    <div class="grid">
      <template v-for="(phase, phaseNo) in loserPhases" v-if="stage.net_type === '2-1'">
        <div class="stage-column">
          <game
            v-for="game in phase"
            :game="game"
            :key="game.id"
            :hovered-player="hoveredPlayer"
            :hover-player="hoverPlayer"
            :unhover-player="unhoverPlayer"
          />
        </div>
        <div class="cross-stage-links" v-if="shouldLinkPhases(loserPhases, phaseNo) || phaseNo === loserPhases.length - 1">
          <template v-for="n in floor(phase.length)">
            <div class="gap x1"></div>
            <div class="vline"></div>
            <div class="gap x1"></div>
          </template>
        </div>
      </template>
      <template v-for="(phase, phaseNo) in winnerPlusOlympicPhases">
        <div class="stage-column">
          <game
            v-for="game in phase"
            :key="game.id"
            :game="game"
            :hover-player="hoverPlayer"
            :hovered-player="hoveredPlayer"
            :unhover-player="unhoverPlayer"
          />
        </div>
        <div class="cross-stage-links" v-if="shouldLinkPhases(winnerPlusOlympicPhases, phaseNo)">
          <template v-for="n in floor(phase.length / 2)">
            <div class="gap x1"></div>
            <div class="vline"></div>
            <div class="gap x1"></div>
          </template>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  import Game from './Game.vue';

  export default {
    name: 'grid',
    props: [
      'stage'
    ],
    components: {
      Game
    },
    data: function () {
      return {
        hoveredPlayer: null,
        shouldLowerBracketFloat: false,
        translateString: ''
      }
    },
    computed: {
      structure: function() {
        if (!this.stage) return {};

        const structure = {};

        this.stage.games.forEach(game => {
          if (!structure[game.phase]) {
            structure[game.phase] = [];
          }
          structure[game.phase].push(game);
        });

        return structure;
      },
      winnerPhases: function () {
        return Object.keys(this.structure)
          .filter(phase => phase.indexOf('w') === 0)
          .sort()
          .map(phase => this.structure[phase]);
      },
      loserPhases: function () {
        return Object.keys(this.structure)
          .filter(phase => phase.indexOf('l') === 0)
          .sort()
          .map(phase => this.structure[phase])
          .reverse();
      },
      olympicPhases: function () {
        return Object.keys(this.structure)
          .filter(phase => phase.indexOf('o') === 0)
          .sort()
          .map(phase => this.structure[phase]);
      },
      winnerPlusOlympicPhases: function () {
        return [...this.winnerPhases, ...this.olympicPhases];
      },
      lowerBracketPlayers: function () {
        const players = [];

        this.stage.games.forEach(({phase, pl1_id, pl2_id}) => {
          if (phase.indexOf('l') === 0) {
            if (!players.includes(pl1_id)) players.push(pl1_id);
            if (!players.includes(pl2_id)) players.push(pl2_id);
          }
        });

        return players;
      }
    },
    methods: {
      floor(n) {
        return Math.floor(n);
      },
      shouldLinkPhases(phases, n) {
        return phases[n + 1] && phases[n].length !== phases[n + 1].length;
      },
      hoverPlayer(id) {
        if (!Number(id)) return;
        this.hoveredPlayer = id;
      },
      unhoverPlayer() {
        this.hoveredPlayer = null;
        this.shouldLowerBracketFloat = false;
      }
    }
  }
</script>

<style>
  @keyframes glow {
    0% {
      box-shadow: 0 0 2px 1px #66c2ff;
    }

    50% {
      box-shadow: 0 0 5px 1px #66c2ff;
    }

    100% {
      box-shadow: 0 0 2px 1px #66c2ff;
    }
  }

  .grid {
    display: flex;
    position: relative;
    justify-content: flex-start;
    align-items: stretch;
    font-family: Helverica, Arial, sans-serif;
    color: #000;
    margin-bottom: 50px;
    font-size: 0.8rem;
    overflow-x: auto;
    transition: all .3s;
    z-index: 0;
  }

  .stage-column {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: stretch;
    margin: 0 5px;
  }

  .stage-column:not(:first-child) .match::before,
  .stage-column:not(:last-child) .match::after {
    position: absolute;
    content: ' ';
    top: 50%;
    height: 0;
    width: 5px;
    border-top: 2px gray solid;
    transition: opacity .2s;
  }

  .stage-column:not(:first-child) .match:nth-child(2n)::before,
  .stage-column:not(:last-child) .match:nth-child(2n)::after {
    margin-top: -2px;
  }

  .stage-column:not(:last-child) .match::after {
    right: -6px;
  }

  .stage-column:not(:first-child) .match::before {
    left: -6px;
  }

  .cross-stage-links {
    display: flex;
    flex-direction: column;
    justify-content: stretch;
  }

  .cross-stage-links .gap {
    width: 0;
    flex: 1;
  }

  .cross-stage-links .vline {
    flex: 2;
    width: 0;
    box-sizing: border-box;
    border-left: 2px gray solid;
  }
</style>
