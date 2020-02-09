<template>
  <div :class="['timer-any', size, cn()]" :data-counter="this.propToUpdate">
    {{timer()}}
  </div>
</template>

<script>
  import moment from 'moment';

  import {leadingZero} from '../utils/format';

  const MINUTE = 60;
  const HOUR = MINUTE * 60;
  const LITTLE = MINUTE * 10;

  export default {
    name: 'timer',
    props: [
      'due',
      'size'
    ],
    mounted: function() {
      this.counterTimer = setInterval(this.update, 999);
    },
    beforeDestroy: function() {
      clearInterval(this.counterTimer);
    },
    data: function() {
      return {
        propToUpdate: null
      };
    },
    methods: {
      update() {
        this.propToUpdate = Math.random();
      },

      timer: function() {
        let diff = moment(this.due).diff(moment(), 'seconds');

        if (diff <= 0) {
          return '00:00:00';
        }
        const hours = leadingZero(Math.floor(diff / HOUR));
        diff = diff % HOUR;

        const minutes = leadingZero(Math.floor(diff / MINUTE));
        diff = diff % MINUTE;

        const seconds = leadingZero(Math.floor(diff));

        return `${hours}:${minutes}:${seconds}`;
      },
      cn: function () {
        const diff = moment(this.due).diff(moment(), 'seconds');

        if (diff <= 0) {
          return 'no-more';
        } else if (diff < LITTLE) {
          return 'little';
        } else {
          return 'much';
        }
      }
    }
  };
</script>

<style scoped>
  .timer-any {
    font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
  }

  .l {
    font-size: 24px;
  }

  .much:not(.compact) {
    color: darkgreen;
  }

  .little:not(.compact) {
    color: darkorange;
  }

  .no-more:not(.compact) {
    color: darkred;
  }

  .compact {
    font-size: 0;
    width: 10px;
    height: 10px;
  }

  .much.compact {
    background-color: darkgreen;
  }

  .little.compact {
    background-color: darkorange;
  }

  .no-more.compact {
    background-color: darkred;
  }
</style>
