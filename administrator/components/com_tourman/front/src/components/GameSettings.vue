<template>
  <a-modal
    :visible="visible"
    @cancel="close"
    title="Игра"
  >
    <div>
      <div v-if="game.status === 'NOT_STARTED'">
        <div class="game-settings-label">Стол:</div>
        <table-picker
          @select="selectTable"
          :selected="selectedTable"
        />
        <div class="game-settings-label">Продолжительность: {{formatDuration()}}</div>
        <a-slider
          :min="15"
          :max="180"
          v-model="duration"
          dots
          :step="15"
          :tip-formatter="formatDuration"
        />
        <a-button
          type="primary"
          @click="startThisMatch"
          icon="check-circle"
        >
          Начать игру
        </a-button>
      </div>

      <div class="flex-row" v-if="game.status === 'STARTED' || game.status === 'INVALID'">
        <div class="column">
          <div class="flex-row">
            <div class="player-name">{{game.user1}}</div>

            <a-button
              size="small"
              type="dashed"
              shape="circle"
              @click="setPlayer1Score(0)"
            />

            <a-rate
              :value="number(game.pl1_score)"
              @change="setPlayer1Score"
              :count="7"
            >
              <a-icon type="check-circle" slot="character" />
            </a-rate>
          </div>

          <div class="flex-row">
            <div class="player-name">{{game.user2}}</div>

            <a-button
              size="small"
              type="dashed"
              shape="circle"
              @click="setPlayer2Score(0)"
            />

            <a-rate
              :value="number(game.pl2_score)"
              @change="setPlayer2Score"
              :count="7"
            >
              <a-icon type="check-circle" slot="character" />
            </a-rate>
          </div>
        </div>

        <winner-placement-prompt
          v-if="game.phase === 'l3'"
          :visible="winnerPromptVisible"
          :finalize-match="finalizeMatch"
          :game="game"
          @close="toggleWinnerPrompt"
        />
      </div>

      <div v-if="game.status === 'FINISHED'">
        <div>Игра завершена</div>
        <a-button type="danger" @click="resetThisGame">В игре ошибка!</a-button>
      </div>
    </div>


    <template slot="footer">
      <a-button @click="close">Отменить</a-button>
      <a-button
        v-if="game.status === 'STARTED' || game.status === 'INVALID'"
        type="primary"
        icon="check-circle"
        @click="tryFinalizeMatch"
      >
        Сохранить
      </a-button>
    </template>
  </a-modal>
</template>

<script>
  import moment from 'moment';

  import {formatDateTime, leadingZero} from '../utils/format';
  import TablePicker from './TablePicker.vue';
  import WinnerPlacementPrompt from './WinnerPlacementPrompt.vue';

  function getDueTime(state) {
    const dueTime = moment().add(state.duration, 'minutes');

    return formatDateTime(dueTime);
  }

  export default {
    name: 'game-settings',
    components: {
      WinnerPlacementPrompt,
      TablePicker
    },
    props: [
      'set-score',
      'start-match',
      'finalize-match',
      'reset-game',
      'game',
      'is-done',
      'visible'
    ],
    data: function() {
      return {
        selectedTable: null,
        duration: 90,
        winnerPromptVisible: false
      }
    },
    methods: {
      selectTable(tableNo) {
        if (this.selectedTable === tableNo) {
          this.selectedTable = null;
          return;
        }

        this.selectedTable = tableNo;
      },
      startThisMatch() {
        if (!this.selectedTable) {
          alert('Нужно выбрать стол!');
          return;
        }

        this.startMatch(this.game.id, this.selectedTable, getDueTime(this))
      },
      setPlayer1Score(value) {
        this.setScore(value, this.game.pl2_score);
      },
      setPlayer2Score(value) {
        this.setScore(this.game.pl1_score, value);
      },
      tryFinalizeMatch() {
        if (this.game.phase === 'l3') {
          this.close();
          this.toggleWinnerPrompt();

          return;
        }

        if (confirm('Вы уверены? Отменить эту операцию нельзя!')) {
          this.finalizeMatch(this.game.id);
        }
      },
      toggleWinnerPrompt() {
        this.winnerPromptVisible = !this.winnerPromptVisible;
      },
      resetThisGame() {
        this.resetGame(this.game.id);
      },
      number(value) {
        return Number(value);
      },
      formatDuration: function(value) {
        const duration = value || this.duration;

        if (duration < 60) {
          return `00:${leadingZero(duration)}`
        }

        const hours = Math.floor(duration / 60);
        return `${leadingZero(hours)}:${leadingZero(duration - hours * 60)}`;
      },
      close() {
        this.$emit('close');
      }
    }
  }
</script>

<style scoped>
  .column {
    display: flex;
    flex-direction: column;
  }

  .flex-row {
    display: flex;
    align-items: center;
  }

  .flex-row .ant-btn {
    margin: 0 10px -5px 0;
  }

  .game-settings-label {
    color: black;
    font-size: 18px;
  }

  .player-name {
    width: 200px;
    overflow: hidden;
    flex-shrink: 0;
  }
</style>
