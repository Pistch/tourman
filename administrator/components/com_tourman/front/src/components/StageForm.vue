<template>
  <a-modal
    :visible="visible"
    @cancel="close"
    :title="modalTitle"
  >
    <div v-if="stage">
      <label class="form-field">
        <span>Название:</span>
        <a-input
          size="large"
          name="title"
          :value="stage.title"
          @change="fieldChange"
        />
      </label>

      <label class="form-field" v-if="stage.status == 1">
        <span>Размер сетки:</span>
        <a-radio-group
          name="net_size"
          buttonStyle="solid"
          :value="stage.net_size"
          @change="fieldChange"
        >
          <a-radio-button value="16">16</a-radio-button>
          <a-radio-button value="32">32</a-radio-button>
          <a-radio-button value="64">64</a-radio-button>
          <a-radio-button value="128">128</a-radio-button>
          <a-radio-button value="256">256</a-radio-button>
        </a-radio-group>
      </label>

      <label class="form-field" v-if="stage.status == 1">
        <span>Тип сетки:</span>
        <a-radio-group
          name="net_type"
          buttonStyle="solid"
          :value="stage.net_type"
          @change="fieldChange"
        >
          <a-radio-button value="2-0">2-0</a-radio-button>
          <a-radio-button value="2-1">2-1</a-radio-button>
        </a-radio-group>
      </label>

      <label class="form-field">
        <span>Дисциплина</span>
        <a-input
          name="discipline"
          :value="stage.discipline"
          @change="fieldChange"
        />
      </label>

      <label class="form-field" v-if="stage.status == 1">
        <span>Вступительный взнос:</span>
        <a-input-number
          name="entry_fee"
          :value="stage.entry_fee"
          @change="feeChange"
        />
      </label>

      <label class="form-field">
        <span>Начало:</span>
        <a-date-picker
          :value="dateStart"
          @change="changeStageStart"
          showTime
          :locale="localeSettings"
        />
        <span>Конец:</span>
        <a-date-picker
          :value="dateEnd"
          @change="changeStageEnd"
          showTime
          :locale="localeSettings"
        />
      </label>
    </div>
    <template slot="footer">
      <a-button type="danger" @click="deleteStage" v-if="stage.id">Удалить</a-button>
      <a-button @click="close">Отменить</a-button>
      <a-button type="primary" icon="save" @click="submitStage">Сохранить</a-button>
    </template>
  </a-modal>
</template>

<script>
  import moment from 'moment';
  import {formatDateTime} from '../utils/format';
  import {localeSettings} from './Datepicker.vue';

  export default {
    name: 'stage-form',
    props: [
      'visible',
      'stage'
    ],
    data: function() {
      return {
        localeSettings
      };
    },
    computed: {
      modalTitle: function () {
        return this.stage && this.stage.id ? 'Редактирование этапа' : 'Создание этапа';
      },
      dateStart: function () {
        return this.stage && moment(this.stage.start_date);
      },
      dateEnd: function () {
        return this.stage && moment(this.stage.end_date);
      },
    },
    methods: {
      close() {
        this.$emit('close');
      },
      fieldChange(e) {
        this.$emit('change', {
          [e.target.name]: e.target.value
        });
      },
      feeChange(val) {
        this.$emit('change', {
          entry_fee: val
        });
      },
      changeStageStart(value) {
        this.$emit('change', {
          start_date: formatDateTime(value)
        });
      },
      changeStageEnd(value) {
        this.$emit('change', {
          end_date: formatDateTime(value)
        });
      },
      submitStage() {
        this.$emit('save');
      },
      deleteStage() {
        this.$emit('delete');
      },
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
