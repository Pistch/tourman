<template>
  <modal :visible="active" @close="cancel">
    <div>
      <h3>Добавить игрока</h3>
      <label>
        <div>Имя</div>
        <input v-model="name" />
      </label>
      <label>
        <div>Email</div>
        <input v-model="email" />
      </label>
      <button @click="ready">Готово</button>
    </div>
  </modal>
</template>

<script>
  import Modal from '../Modal.vue';

  export default {
    name: 'new-player',
    props: ['submit', 'active'],
    components: {Modal},
    data: function () {
      return {
        name: '',
        email: ''
      };
    },
    methods: {
      ready() {
        const {name, email} = this;

        this.submit({name, email});
      },

      cancel() {
        this.$emit('close');
      }
    }
  }
</script>

<style scoped>

</style>
