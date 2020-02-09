<template>
  <a-modal
    :visible="visible"
    @cancel="close"
    title="Выберите игру, в которую переходит победитель"
  >
    <div>
      <game-representation
        v-for="game in games"
        @click="changePlacement"
        :class-name="getCN(game)"
        :key="game.id"
        :game="game"
        is-simple
      />
    </div>
    <template slot="footer">
      <a-button @click="close">Отменить</a-button>
      <a-button
        type="primary"
        icon="save"
        @click="finish"
        :disabled="placement === -1"
      >Сохранить</a-button>
    </template>
  </a-modal>
</template>

<script>
  import GameRepresentation from './GameRepresentation.vue';

  export default {
    name: 'winner-placement-prompt',
    props: ['visible', 'finalize-match', 'game'],
    components: {
      GameRepresentation
    },
    mounted: function() {
      this.placement = this.games.findIndex(game => !game.disabled);
    },
    data: function () {
      return {
        placement: 0
      };
    },
    computed: {
      games: function () {
        const stage = this.$store.state.interface.selectedStage;

        return stage.games
          .filter(game => game.phase === 'o0')
          .sort((g1, g2) => g1.phase_placement - g2.phase_placement)
          .map(g => ({
            ...g,
            disabled: Number(g.pl2_id) !== 0
          }));
      }
    },
    methods: {
      close() {
        this.$emit('close');
      },
      changePlacement(game) {
        if (game.disabled) {
          return;
        }

        this.placement = Number(game.phase_placement);
      },
      finish() {
        this.finalizeMatch(this.game.id, this.placement)
          .then(() => this.$emit('close'));
      },
      getCN(game) {
        let cn = 'game';

        if (this.placement === Number(game.phase_placement)) {
          cn += ' selected';
        }

        if (game.disabled) {
          cn += ' disabled';
        }

        return cn;
      }
    }
  };
</script>

<style scoped>
  .game {
    box-shadow: 0 0 0 0 dodgerblue;
    transition: .2s box-shadow, .2s outline-width;
  }

  .game:not(.disabled):hover {
    box-shadow: 0 0 7px 1px dodgerblue;
  }

  .disabled {
    filter: contrast(70%);
  }

  .selected {
    position: relative;
  }

  .selected::after {
    position: absolute;
    content: ' ';
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    box-shadow: inset 0 0 1px 1px green;
  }
</style>
