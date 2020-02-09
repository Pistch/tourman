<template>
  <div v-if="stage">
    <div class="playersListsWrap">
      <div>
        <table class="playersTable">
          <thead>
            <tr>
              <td>№</td>
              <td>ФИО</td>
              <td>Гандикап</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!registeredUsers.length">
              <td></td>
              <td colspan="3">
                Пока никто не зарегистрирован
              </td>
            </tr>
            <tr v-for="(user, index) in registeredUsers">
              <td>{{index + 1}}</td>
              <td>
                {{user.name}}
              </td>
              <td>
                <a-rate :value="getHandicap(user.handicap)" @change="setUserHandicap(user.id, $event)" allowClear>
                  <a-icon slot="character" type="crown" />
                </a-rate>
              </td>
              <td>
                <a-button shape="circle" icon="delete" @click.stop="unregisterUser(user.id)" />
              </td>
            </tr>
            <tr v-if="!isFull">
              <td>
                <a-icon type="plus" />
              </td>
              <td>
                <a-auto-complete
                  :value="inputValue"
                  :dataSource="suggestions"
                  style="width: 250px; margin-top: 10px;"
                  @select="pickUser"
                  @search="findUser"
                  placeholder="Имя игрока"
                >
                  <a-icon slot="suffix" type="search" class="certain-category-icon" />
                </a-auto-complete>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="right-panel">
        <div class="affix-conainer">
          <a-affix :offsetTop="100">
            <a-card>
              <div class="affix-content">
                <a-progress type="circle" :percent="percentage" :format="getPercentageString"/>
                <span>игроков</span>
                <div class="save-button-container">
                  <a-button type="primary" icon="save" size="large" @click="finishRegistration">Завершить регистрацию</a-button>
                </div>
              </div>
            </a-card>
          </a-affix>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {mapState} from 'vuex';
  import api from './api';

  import Suggest from './Suggest.vue';

  export default {
    name: 'new-stage',
    components: {
      Suggest
    },
    data: function () {
      return {
        suggestions: [],
        inputValue: ''
      };
    },
    computed: {
      ...mapState({
        stage: state => state.interface.selectedStage
      }),
      seeds: function() {
        return Number(this.stage.net_size);
      },
      registeredUsers: function() {
        return this.stage.registrations || [];
      },
      placesLeft: function() {
        return this.seeds - this.registeredUsers.length;
      },
      percentage: function() {
        return 100 * (this.registeredUsers.length / this.seeds);
      },
      isFull: function() {
        return !this.placesLeft;
      }
    },
    methods: {
      getHandicap(val) {
        return Number(val) || 0;
      },
      getPercentageString: function() {
        return `${this.registeredUsers.length} / ${this.seeds}`;
      },
      updateStage(data) {
        this.$store.commit('editSelectedStage', data);
      },
      updateRegisteredUsers(rawData) {
        this.updateStage({
          registrations: Array.isArray(rawData) ? rawData : Object.values(rawData)
        });
      },
      findUser(value) {
        this.inputValue = value.trim();

        if (!this.inputValue) {
          return;
        }

        api.suggestUser(this.inputValue)
          .then(d => {
            this.suggestions = Object.values(d).map(i => ({text: i.name, value: JSON.stringify(i)}));
          });
      },
      pickUser(u) {
        const user = JSON.parse(u);

        this.suggestions = [];
        this.inputValue = '';

        if (
          !this.registeredUsers.find(pickedUser => user.id === pickedUser.id)
        ) {
          this.$store.commit('startLoading');
          api.submitTournamentRegistrationUsers([user], this.stage.id)
            .then(users => {
              this.$store.commit('stopLoading');
              this.updateRegisteredUsers(users);
              this.pickedUsers = [];
            });
        }
      },
      unregisterUser(userId) {
        this.$store.commit('startLoading');
        return api.unregisterPlayerFromStage(userId, this.stage.id)
          .then(users => {
            this.$store.commit('stopLoading');
            this.updateRegisteredUsers(users);
          });
      },
      setUserHandicap(user, value) {
        this.$store.commit('startLoading');
        api.setPlayerStageHandicap({
          player_id: user,
          stage_id: this.stage.id,
          tournament_id: this.stage.tournament_id,
          value
        })
          .then(users => {
            this.$store.commit('stopLoading');
            this.updateRegisteredUsers(users);
          });
      },
      finishRegistration() {
        if (confirm('Вы уверены? Это действие необратимо, больше зарегистрировать игроков будет нельзя!')) {
          this.$store.commit('startLoading');
          api.closeRegistration(this.stage.id)
            .then(() => {
              this.$store.commit('stopLoading');
              this.$store.dispatch('selectStage', this.stage.id)
            });
        }
      }
    }
  }
</script>

<style scoped>
  .playersListsWrap {
    display: flex;
  }

  .playersTable {
    width: 550px;
  }

  .playersTable thead {
    background: #efefef;
  }

  .playersTable td {
    padding: .2rem .5rem;
  }

  .right-panel {
    flex: 1;
    margin-left: 7rem;
  }

  .affix-conainer {
    display: flex;
  }

  .affix-content {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .save-button-container {
    padding: 2rem;
  }
</style>
