<template>
  <div class="match">
    <div
      :class="[
              'player-row',
              hoveredPlayer === game.pl1_id ? 'hovered-player' : '',
              getCN(true)
            ]"
      :data-player="game.pl1_id"
      @mouseenter="hoverPlayer(game.pl1_id)"
      @mouseleave="unhoverPlayer"
    >
      <span class="player-name">
        {{game.user1}}
        <span v-if="game.user1 && shouldShowHandicap(game.user1_handicap)" class="handicap">
          ({{game.user1_handicap}})
        </span>
      </span>
      <span class="score">{{game.pl1_score}}</span>
    </div>
    <div
      :class="[
                'player-row',
                hoveredPlayer === game.pl2_id ? 'hovered-player' : '',
                getCN(false)
              ]"
      :data-player="game.pl2_id"
      @mouseenter="hoverPlayer(game.pl2_id)"
      @mouseleave="unhoverPlayer"
    >
      <span class="player-name">
        {{game.user2}}
        <span v-if="game.user2 && shouldShowHandicap(game.user2_handicap)" class="handicap">
          ({{game.user2_handicap}})
        </span>
      </span>
      <span class="score">{{game.pl2_score}}</span>
    </div>
  </div>
</template>

<script>
  export default {
    name: "Game",
    props: [
      'game',
      'hover-player',
      'hovered-player',
      'unhover-player'
    ],
    data: function () {
      return {};
    },
    methods: {
      getCN(isFirst) {
        const pl1_score = Number(this.game.pl1_score);
        const pl2_score = Number(this.game.pl2_score);

        if (pl1_score === pl2_score) {
          return '';
        } else if (pl1_score > pl2_score) {
          return isFirst ? 'winner' : 'loser';
        } else {
          return isFirst ? 'loser' : 'winner';
        }
      },
      shouldShowHandicap(val) {
        return Boolean(Number(val));
      }
    }
  }
</script>

<style>
  .match {
    position: relative;
    border-radius: 2px;
    border: 1px gray solid;
    margin: 4px 0;
    background: #fff;
    z-index: 1;
    transition: all .3s;
  }

  .player-row {
    display: flex;
    justify-content: stretch;
    align-items: stretch;
    cursor: pointer;
    background: rgba(0,0,0,0.05);
    transition: .15s background ease-in-out;
    white-space: nowrap;
  }

  .player-row:first-child {
    border-bottom: 1px gray solid;
  }

  .hovered-player {
    animation: glow 3s ease-in infinite both;
  }

  .hovered-player .player-name {
    background: rgba(0,0,255,0.1);
  }

  .winner.hovered-player .score {
    background: rgba(0,255,0,0.3);
  }

  .hovered-player.loser .score {
    background: rgba(255,0,0,0.3);
  }

  .hovered-player:not(.winner):not(.loser) .score {
    background: #fff;
  }

  .player-name, .score {
    padding: 4px 7px;
    display: block;
    transition: .15s background ease-in-out;
  }

  .player-name {
    flex-grow: 1;
  }

  .handicap {
    font-size: 10px;
  }

  .score {
    flex-grow: 0;
    border-left: 1px gray solid;
  }

  .winner .score,
  .winner .player-name {
    font-weight: bold;
  }
</style>
