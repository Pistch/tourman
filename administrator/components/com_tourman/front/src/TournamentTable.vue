<template>
  <div>
    <div>
      <div class="entity">
        <span class="entity-label">Турнир:</span>
        <a-select
          style="width: 400px;"
          @change="handleTournamentSelection"
          :value="selectedTournament && selectedTournament.id"
        >
          <div slot="dropdownRender" slot-scope="tournament">
            <v-nodes :vnodes="tournament"/>
            <a-divider style="margin: 4px 0;" />
            <div style="padding: 8px; cursor: pointer;" @click="prepareTournamentCreation">
              <a-icon type="plus" /> Создать новый
            </div>
          </div>
          <a-select-option
            v-for="tournament in tournaments"
            :value="tournament.id"
            :key="tournament.id"
          >
            {{tournament.title}}
          </a-select-option>
        </a-select>
        <a-button shape="circle" icon="edit" v-if="selectedTournament" @click="toggleTournamentPopup" />

        <div v-if="!selectedTournament">
          <button class="pseudo-link" v-for="ong in ongoingTournaments" @click="selectTournament(ong.id)">{{ong.title}}</button>
        </div>
      </div>

      <div v-if="selectedTournament && selectedTournament.id" class="entity">
        <span class="entity-label">Этап:</span>
        <a-select
          style="width: 400px;"
          @change="handleStageSelection"
          :value="selectedStage && selectedStage.id"
        >
          <div slot="dropdownRender" slot-scope="stage">
            <v-nodes :vnodes="stage"/>
            <a-divider style="margin: 4px 0;" />
            <div style="padding: 8px; cursor: pointer;" @click="prepareStageCreation">
              <a-icon type="plus" /> Создать новый
            </div>
          </div>
          <a-select-option
            v-for="stage in selectedTournamentStages"
            :value="stage.id"
            :key="stage.id"
          >
            {{stage.title}}
          </a-select-option>
        </a-select>
        <a-button shape="circle" icon="edit" v-if="selectedStage" @click="toggleStagePopup" />

        <div v-if="selectedTournament && selectedStage">
          <button
            class="pseudo-link"
            @click="selectPreviousStage"
            :disabled="stagePosition < 1"
          >
            ← Предыдущий
          </button>
          <button
            class="pseudo-link"
            @click="selectNextStage"
            :disabled="stagePosition >= selectedTournamentStages.length - 1"
          >
            Следующий →
          </button>
        </div>
      </div>
    </div>

    <tournament-form
      :visible="isEditingTournament"
      :tournament="isEditingTournament && selectedTournament"
      @close="handleTounamentFormClose"
      @change="updateSelectedTournament"
      @save="submitTournament"
      @delete="deleteTournament"
    />

    <stage-form
      :visible="isEditingStage"
      :stage="isEditingStage && selectedStage"
      v-if="selectedTournament"
      @close="handleStageFormClose"
      @change="updateSelectedStage"
      @save="submitStage"
      @delete="deleteStage"
    />

    <dued-games />
  </div>
</template>

<script>
  import {mapState} from 'vuex';

  import DuedGames from './components/DuedGames.vue';
  import StageForm from './components/StageForm.vue';
  import TournamentForm from './components/TournamentForm.vue';

  export default {
    name: 'tournament-table',
    components: {
      StageForm,
      TournamentForm,
      DuedGames,
      VNodes: {
        functional: true,
        render: (h, ctx) => ctx.props.vnodes
      }
    },
    data: function () {
      return {
        isEditingTournament: false,
        isEditingStage: false,
        selectedTournamentId: null,
        selectedStageId: null
      };
    },
    computed: {
      ...mapState({
        selectedTournament: state => state.interface.selectedTournament,
        selectedStage: state => state.interface.selectedStage,
        ongoingTournaments: state =>
          state.tournaments.ongoing.map(id => state.tournaments.byId[id]),
        tournaments: state => state.tournaments.byOrder.map(id => state.tournaments.byId[id]),
        selectedTournamentStages: state => (
          state.interface.selectedTournament && state.interface.selectedTournament.id
            ? (
              Array.isArray(state.stages.byTournament[state.interface.selectedTournament.id]) &&
              state.stages.byTournament[state.interface.selectedTournament.id].map(stageId => state.stages.byId[stageId]) ||
              []
            ) : []
        )
      }),
      stagePosition: function () {
        return this.selectedTournamentStages.findIndex(stage => stage.id === this.selectedStage.id);
      }
    },
    methods: {
      handleTournamentSelection(id) {
        this.selectTournament(id);
      },
      selectTournament(id) {
        if (!id || this.selectedTournament && this.selectedTournament.id === id) {
          return;
        }

        this.$store.dispatch('selectTournament', id);
      },
      handleStageSelection(id) {
        this.selectStage(id);
      },
      selectStage(id) {
        if (this.selectedStage && this.selectedStage.id === id) {
          return;
        }

        this.$store.dispatch('selectStage', id);
      },
      selectPreviousStage() {
        if (this.stagePosition > 0) {
          this.selectStage(this.selectedTournamentStages[this.stagePosition - 1].id);
        }
      },
      selectNextStage() {
        if (this.stagePosition < this.selectedTournamentStages.length - 1) {
          this.selectStage(this.selectedTournamentStages[this.stagePosition + 1].id);
        }
      },

      prepareTournamentCreation() {
        if (this.selectedTournament && this.selectedTournament.id) {
          this.selectedTournamentId = this.selectedTournament.id;
        }

        this.$store.commit('selectTournament');
        this.toggleTournamentPopup();
      },
      cancelTournamentCreation() {
        if (this.selectedTournamentId) {
          this.$store.commit('selectTournament', this.selectedTournamentId);
          this.selectedTournamentId = null;
        } else {
          this.$store.commit('deselectTournament');
        }

        this.toggleTournamentPopup();
      },

      prepareStageCreation() {
        if (this.selectedStage && this.selectedStage.id) {
          this.selectedStageId = this.selectedStage.id;
        }

        this.$store.commit('selectStage');
        this.toggleStagePopup();
      },
      cancelStageCreation() {
        if (this.selectedStageId) {
          this.$store.commit('selectStage', this.selectedStageId);
          this.selectedStageId = null;
        } else {
          this.$store.commit('deselectStage');
        }

        this.toggleStagePopup();
      },

      updateSelectedTournament(data) {
        this.$store.commit('editSelectedTournament', data);
      },
      updateSelectedStage(data) {
        this.$store.commit('editSelectedStage', data);
      },

      submitTournament() {
        this.$store.dispatch('saveTournament')
          .then(() => this.toggleTournamentPopup());
      },
      submitStage() {
        this.$store.dispatch('saveStage')
          .then(() => this.toggleStagePopup());
      },

      handleTounamentFormClose() {
        if (this.selectedTournament.id) {
          this.toggleTournamentPopup();
        } else {
          this.cancelTournamentCreation()
        }
      },
      handleStageFormClose() {
        if (this.selectedStage.id) {
          this.toggleStagePopup();
        } else {
          this.cancelStageCreation()
        }
      },

      toggleTournamentPopup() {
        this.isEditingTournament = !this.isEditingTournament;
      },
      toggleStagePopup() {
        this.isEditingStage = !this.isEditingStage;
      },

      deleteTournament() {
        if (!confirm('Вы уверены? Это действие необратимо!')) {
          return;
        }

        this.toggleTournamentPopup();
        this.$store.dispatch('deleteTournament');
      },
      deleteStage() {
        if (!confirm('Вы уверены? Это действие необратимо!')) {
          return;
        }

        this.toggleStagePopup();
        this.$store.dispatch('deleteStage');
      }
    }
  }
</script>

<style scoped>
  .entity {
    padding: 1rem;
  }

  .entity-label {
    box-sizing: border-box;
    width: 100px;
    padding: 1rem;
  }

  .pseudo-link {
    border: none;
    outline: none;
    background: none;
    border-radius: 0;
    color: #777;
    border-bottom: 1px #777 dashed;
    padding: .1em 0;
    margin-right: 10px;
    transition: .2s color;
  }

  .pseudo-link:disabled {
    color: #ddd;
    border: none;
  }

  .pseudo-link:disabled:hover {
    color: #ddd;
    border: none;
  }

  .pseudo-link:hover {
    color: #22a;
    border-bottom-color: #22a;
  }
</style>
