<template>
  <a-modal
    :visible="visible"
    @cancel="close"
    :title="modalTitle"
  >
    <div v-if="tournament">
      <label class="form-field">
        <span>Название:</span>
        <a-input
          size="large"
          name="title"
          :value="tournament.title"
          @change="handleGeneralFieldChange"
        />
      </label>

      <label class="form-field">
        <span>Описание:</span>
        <a-textarea
          autosize
          name="description"
          :value="tournament.description"
          @change="handleGeneralFieldChange"
        />
      </label>

      <label class="form-field">
        <span>Регламент:</span>
        <a-textarea
          autosize
          name="reglament"
          :value="tournament.reglament"
          @change="handleGeneralFieldChange"
        />
      </label>

      <label class="form-field">
        <span>Ссылка на логотип</span>
        <a-input
          name="logo"
          :value="tournament.logo"
          @change="handleGeneralFieldChange"
        />
      </label>

      <label class="form-field">
        <span>Дисциплина</span>
        <a-input
          name="discipline"
          :value="tournament.discipline"
          @change="handleGeneralFieldChange"
        />
      </label>

      <label class="form-field">
        <a-checkbox
          @change="handleCheckboxChange"
          name="is_rating"
          :checked="isRating"
        >
          Рейтинговый
        </a-checkbox>
      </label>

      <label class="form-field">
        <span>Даты проведения:</span>
        <a-range-picker
          :value="datesRange"
          :locale="localeSettings"
          @change="onRangeChange"
        />
      </label>
    </div>
    <template slot="footer">
      <a-button type="danger" @click="deleteTournament" v-if="tournament.id">Удалить</a-button>
      <a-button @click="close">Отменить</a-button>
      <a-button type="primary" icon="save" @click="submitTournament">Сохранить</a-button>
    </template>
  </a-modal>
</template>

<script>
  import moment from 'moment';
  import {localeSettings} from './Datepicker.vue';
  import {formatDate} from '../utils/format';

  export default {
    name: 'tournament-form',
    props: [
      'visible',
      'tournament'
    ],
    computed: {
      isRating: function () {
        return Boolean(Number(this.tournament.is_rating));
      },
      datesRange: function () {
        return (
          this.tournament &&
          [moment(this.tournament.start_date), moment(this.tournament.end_date)]
        );
      },
      modalTitle: function () {
        return this.tournament && this.tournament.id ? 'Редактирование турнира' : 'Создание турнира';
      }
    },
    data: function() {
      return {
        localeSettings
      };
    },
    methods: {
      close() {
        this.$emit('close');
      },
      handleCheckboxChange(event) {
        this.handleFieldChange(event.target.name, event.target.checked);
      },
      handleGeneralFieldChange(event) {
        this.handleFieldChange(event.target.name, event.target.value);
      },
      handleFieldChange(name, value) {
        this.$emit('change', {
          [name]: value
        });
      },
      deleteTournament() {
        this.$emit('delete');
      },
      submitTournament() {
        this.$emit('save');
      },
      onRangeChange(data) {
        const [start_date, end_date] = data;

        this.$emit('change', {
          end_date: formatDate(end_date),
          start_date: formatDate(start_date)
        });
      }
    }
  }
</script>

<style scoped>
  .form-field {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin: 10px;
  }
</style>
