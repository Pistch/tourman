<template>
  <div>
    <h2>Обслуживание</h2>
    <button @click="toggleRecalculationPopup">
      Пересчёт турнирных рейтингов
    </button>

    <rating-recalculator :visible="recalculationPopupVisible" />
  </div>
</template>

<script>
  import RatingRecalculator from '../components/RatingRecalculator.vue'

  export default {
    name: 'service',
    components: {
      RatingRecalculator
    },
    data: function() {
      return {
        recalculationPopupVisible: false
      };
    },
    methods: {
      toggleRecalculationPopup() {
        this.recalculationPopupVisible = !this.recalculationPopupVisible;
      }
    }
  }
</script>

<style scoped>
  .modal-content {

  }
</style>
