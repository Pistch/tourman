<template>
  <div v-if="games.length" class="dued-wrap">
    <template v-if="!isCompactMode">
      <table>
        <tr>
          <td>
            <a-button shape="circle" icon="down" @click="switchMode" />
          </td>
          <td class="dued-row-item">Стол</td>
          <td class="dued-row-item">Осталось</td>
        </tr>
        <tr v-for="game in games" :key="game.id">
          <td>
            <game :game="game" is-simple />
          </td>
          <td class="dued-row-item">
            <div class="table-no full">{{game.table}}</div>
          </td>
          <td class="dued-row-item">
            <timer :due="game.due_time" size="l" />
          </td>
        </tr>
      </table>
    </template>

    <div v-if="isCompactMode" class="dued-compact">
      <a-button shape="circle" icon="up" size="small" @click="switchMode" />
      <div class="dued-title">Текущие игры:</div>
      <div class="dued-compact-table">
        <div v-for="game in games" :key="game.id" class="dued-compact-table-item">
          <div class="table-no compact">{{game.table}}</div>
          <timer :due="game.due_time" size="compact" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import Timer from './Timer.vue';
  import Game from './Game.vue';

  export default {
    name: 'dued-games',
    components: {Timer, Game},
    mounted: function() {
      this.updateState();
    },
    data: function() {
      return {
        timer: null,
        isCompactMode: true
      };
    },
    computed:{
      games: function() {
        return this.$store.state.duedGames.games;
      }
    },
    methods: {
      updateState() {
        this.$store.dispatch('getDuedGames');
        this.timer = setTimeout(this.updateState, 60 * 1000);
      },
      switchMode() {
        this.isCompactMode = !this.isCompactMode;
      }
    },
    beforeDestroy() {
      clearTimeout(this.timer);
    }
  };
</script>

<style scoped>
  .dued-wrap {
    position: fixed;
    z-index: 1000;
    bottom: 0;
    right: 0;
    background: #fff;
    padding: 18px 18px 36px;
    box-shadow: 0 0 10px 2px rgba(0,0,0,0.2);
  }

  .dued-row-item {
    font-size: 24px;
    padding-left: 16px;
  }

  .table-no.full {
    width: 36px;
    height: 36px;
    border-radius: 14px;
    background: #777;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .dued-compact {
    display: flex;
    align-items: center;
  }

  .dued-title {
    font-size: 18px;
    margin: 0 16px;
  }

  .dued-compact-table {
    display: flex;
  }

  .dued-compact-table-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 4px;
  }

  .table-no.compact {
    font-size: 12px;
  }
</style>
