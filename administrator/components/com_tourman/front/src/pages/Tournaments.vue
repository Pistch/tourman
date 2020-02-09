<template>
  <div>
    <tournament-table />

    <grid
      :stage="selectedStage"
      v-if="selectedStage && Number(selectedStage.status) !== 1"
    />
    <new-stage v-if="selectedStage && Number(selectedStage.status) === 1" />
  </div>
</template>

<script>
import Grid from '../Grid.vue';
import NewStage from '../NewStage.vue';
import TournamentTable from '../TournamentTable.vue';

export default {
  name: 'tournaments',
  components: {
    Grid,
    NewStage,
    TournamentTable
  },
  computed: {
    selectedStage: function() {
      return this.$store.state.interface.selectedStage;
    }
  }
}
</script>

<style>
  h1, h2 {
    font-family: Helvetica, Arial, sans-serif;
  }
</style>
