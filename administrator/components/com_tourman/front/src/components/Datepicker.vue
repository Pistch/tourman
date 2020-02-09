<template>
    <div class="datepicker-container">
      <label v-if="withTime">
        <strong class="label">
          Часы:
        </strong>
        <select :value="hour" ref="hour" @change="change" class="select">
          <option v-for="n in 25" :value="n">{{leadingZero(n)}}</option>
        </select>
      </label>
      <label v-if="withTime">
        <strong class="label">
          Минуты:
        </strong>
        <select :value="minutes" ref="minutes" @change="change" class="select">
          <option v-for="n in 61" :value="n">{{leadingZero(n)}}</option>
        </select>
      </label>
      <label>
        <strong class="label">
          День:
        </strong>
        <select :value="date" ref="date" @change="change" class="select">
          <option v-for="n in 31" :value="n">{{leadingZero(n)}}</option>
        </select>
      </label>
      <label>
        <strong class="label">
          Месяц:
        </strong>
        <select :value="month" ref="month" @change="change" class="select">
          <option v-for="n in 12" :value="n">{{leadingZero(n)}}</option>
        </select>
      </label>
      <label>
        <strong class="label">
          Год:
        </strong>
        <select :value="year" ref="year" @change="change" class="select">
          <option v-for="n in 40" :value="2005 + n">{{2005 + n}}</option>
        </select>
      </label>
    </div>
</template>

<script>
  import moment from 'moment';

  import {leadingZero} from '../utils/format';

  // Воздействует глобально, нужно для рендеринга дейтпикера
  moment.locale('ru');

  export const localeSettings = {
    "lang": {
      "placeholder": "Выберите дату",
      "rangePlaceholder": ["Дата начала", "Дата конца"],
      "today": "Сегодня",
      "now": "Сейчас",
      "backToToday": "Назад к сегодняшнему дню",
      "ok": "ОК",
      "clear": "Очистить",
      "month": "Месяц",
      "year": "Год",
      "timeSelect": "Выберите время",
      "dateSelect": "Выберите день",
      "monthSelect": "Выберите месяц",
      "yearSelect": "Выберите год",
      "decadeSelect": "Выберите десятилетие",
      "yearFormat": "YYYY",
      "dateFormat": "DD.MM.YYYY",
      "dayFormat": "DD",
      "dateTimeFormat": "DD.MM.YYYY HH:mm:ss",
      "monthFormat": "MMMM",
      "monthBeforeYear": true,
      "previousMonth": "Предыдущий месяц (PageUp)",
      "nextMonth": "Следующий месяц (PageDown)",
      "previousYear": "Предыдущий год (Control + left)",
      "nextYear": "Следующий год (Control + right)",
      "previousDecade": "Предыдущее десятилетие",
      "nextDecade": "Следующее десятилетие",
      "previousCentury": "Предыдущий век",
      "nextCentury": "Следующий век"
    },
    "timePickerLocale": {
      "placeholder": "Выберите время"
    }
  };

  export default {
    name: 'datepicker',
    props: [
      'value',
      'with-time'
    ],
    mounted: function () {
      this.$nextTick(this.change.bind(this));
    },
    computed: {
      _value: function() {
        return new Date(this.value);
      },
      date: function () {
        return this._value.getDate();
      },
      month: function () {
        return this._value.getMonth() + 1;
      },
      year: function () {
        return this._value.getFullYear();
      },
      hour: function () {
        return this._value.getHours();
      },
      minutes: function () {
        return this._value.getMinutes();
      }
    },
    data: function() {
      return {
        localeSettings
      }
    },
    methods: {
      leadingZero,
      change() {
        let result = [
          this.$refs.year.value,
          this.leadingZero(this.$refs.month.value),
          this.leadingZero(this.$refs.date.value)
        ].join('-');

        if (this.withTime) {
          result += ` ${[
            this.leadingZero(this.$refs.hour.value),
            this.leadingZero(this.$refs.minutes.value),
            '00'
          ].join(':')}`;
        }

        this.$emit('change', result);
      }
    }
  }
</script>

<style scoped>
  .datepicker-container {
    display: flex;
  }

  select.select {
    width: auto;
  }

  strong.label {
    width: 90%;
  }

</style>
