<template>
  <div class="wrap">
    <label>
      <div>{{label || 'Добавить игрока:'}}</div>
      <input
        v-model="inputValue"
        @input="getSuggestions"
        :placeholder="placeholder"
        class="input"
      />
    </label>
    <ul v-if="items && items.length" class="suggestionsContainer">
      <li v-for="suggestion in items" @click="pickSuggestion(suggestion)" class="suggestion">
        {{suggestion.display}}
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: "suggest",
    props: [
      'items',
      'placeholder',
      'label'
    ],
    data: function() {
      return {
        inputValue: ''
      };
    },
    methods: {
      getSuggestions(event) {
        this.$emit('input', event);
      },
      pickSuggestion(suggestion) {
        this.inputValue = '';
        this.$emit('pick', suggestion);
      }
    }
  }
</script>

<style scoped>
  .wrap {
    position: relative;
    z-index: 2;
  }

  .input {
    border: 1px gray solid;
    border-radius: 2px;
    outline: 2px transparent solid;
    transition: .2s outline-color;
  }

  .input:focus {
    outline-color: #EEF;
  }

  .suggestionsContainer {
    position: absolute;
    top: 100%;
    left: 0;
    border: 1px gray solid;
    box-shadow: 2px 2px 5px 1px rgba(0,0,0,0.1);
    background: #FFF;
    list-style: none;
    margin: 0;
    padding: .3rem;
    z-index: 2;
  }

  .suggestion {
    padding: .1rem;
    cursor: pointer;
    user-select: none;
    transition: .1s background-color;
  }

  .suggestion:hover {
    background-color: #EEF;
  }

</style>
