<template>
  <div class="switcher_wrap">
    <div
      v-for="item in items"
      @click="change(item.value)"
      :class="['item', item.value === value ? 'selected' : '']"
    >
      {{item.text}}
    </div>
  </div>
</template>

<script>
  export default {
    name: 'switcher',
    props: ['value', 'items'],
    methods: {
      change(value) {
        if (this.value !== value) {
          this.$emit('change', value);
        }
      }
    }
  }
</script>

<style scoped>
  .switcher_wrap {
    border-radius: 4px;
    padding: 3px;
    background: #ccc;
    box-shadow: inset 1px 1px rgba(0,0,0,.2);
    display: inline-block;
  }

  .item {
    user-select: none;
    display: inline-block;
    padding: .2rem .5rem;
    border-radius: 3px;
    transition: all .2s;
    cursor: pointer;
  }

  .item + .item {
    margin-left: .2rem;
  }

  .item:hover:not(.selected) {
    background: #ddd;
  }

  .item.selected {
    background: #fff;
    box-shadow: 1px 1px rgba(0,0,0,.2);
  }
</style>
