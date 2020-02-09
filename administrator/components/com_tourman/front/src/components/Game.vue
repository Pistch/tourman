<template>
  <game-representation
    :game="game"
    :is-done="isGameDone"
    @click="focusGame"
    :is-focused="isFocused"
    :is-simple="isSimple"
    :hover-player="hoverPlayer"
    :hovered-player="hoveredPlayer"
    :unhover-player="unhoverPlayer"
  >
    <game-settings
      v-if="gameHasPlayers && !isSimple"
      :visible="isFocused"
      :game="game"
      :set-score="submitScore"
      :start-match="startMatch"
      :finalize-match="finalizeMatch"
      @close="unfocusGame"
      :reset-game="resetGame"
    />
  </game-representation>
</template>

<script>
  import GameRepresentation from './GameRepresentation.vue';
  import GameSettings from './GameSettings.vue';

  const noop = () => {};

  export default {
    name: 'game',
    components: {GameRepresentation, GameSettings},
    props: {
      game: {
        type: Object
      },
      'hover-player': {
        type: Function,
        default: noop
      },
      'hovered-player': {
        type: String
      },
      'unhover-player': {
        type: Function,
        default: noop
      },
      'set-match-score': {
        type: Function,
        default: noop
      },
      'start-match': {
        type: Function,
        default: noop
      },
      'reset-game': {
        type: Function,
        default: noop
      },
      'make-match-done': {
        type: Function,
        default: noop
      },
      'is-focused': {
        type: Boolean
      },
      'is-simple': {
        type: Boolean
      }
    },
    computed: {
      isGameDone: function() {
        return this.game.done || this.game.status === 'FINISHED';
      },
      gameHasPlayers: function() {
        return Number(this.game.pl1_id) || Number(this.game.pl2_id);
      }
    },
    methods: {
      unfocusGame() {
        if (this.isFocused) {
          this.$emit('focused', null);
        }
      },
      focusGame() {
        this.$emit('focused', this.isFocused ? null : this.game.id);
      },
      submitScore(pl1_score, pl2_score) {
        if (this.isGameDone) {
            return;
        }

        this.setMatchScore(this.game.id, Number(pl1_score), Number(pl2_score));
      },
      finalizeMatch(id, placement) {
        return this.makeMatchDone(id || this.game.id, placement)
          .then(this.focusGame);
      },
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
      number(value) {
        return Number(value);
      }
    }
  }
</script>

<style scoped>
</style>
