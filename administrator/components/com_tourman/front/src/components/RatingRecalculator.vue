<template>
  <modal :visible="visible">
    <div class="modal-content">
      <div v-show="byPeriod">
        <div class="table">
          <div>
            <h4>
              C:
            </h4>
            <datepicker :value="from" @change="changeStartDate" />
          </div>
          <div>
            <h4>
              До:
            </h4>
            <datepicker :value="to" @change="changeEndDate" />
          </div>
        </div>
        <button @click="recalculateByPeriod">Пересчитать</button>
      </div>

      <div v-show="!byPeriod">

      </div>
    </div>
  </modal>
</template>

<script>
  import api from '../api';

  import Datepicker from './Datepicker.vue';
  import Modal from '../Modal.vue';

  export default {
    name: 'rating-recalculator',
    components: {
      Modal,
      Datepicker
    },
    props: ['visible'],
    data: function() {
      return {
        from: Date.now(),
        to: Date.now(),
        byPeriod: true
      };
    },
    methods: {
      changeStartDate(value) {
        this.from = value;
      },
      changeEndDate(value) {
        this.to = value;
      },

      recalculateByPeriod() {
        this.$store.commit('startLoading');
        api.recalculateRatingsByPeriod(this.from, this.to)
          .then(data => {
            this.$store.commit('stopLoading');
          });
      },

      recalculateByTournament() {

      }
    }
  }
</script>

<style scoped>
  .table {
    display: flex;
    justify-content: center;
    gap: 30px;
  }

  .modal-content {
    padding: 20px;
  }
</style>
