<template>
  <div
    :class="[
      'gag',
      visible ? 'invisible' : '',
      visible && shouldBeVisible ? 'visible' : ''
    ]"
    @click="close"
    ref="_gag"
  >
    <div class="modal" @click.stop>
      <div class="modal-content" v-if="visible">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Modal',
    props: ['visible'],
    data: function () {
      return {
        shouldBeVisible: false,
        closing: false
      };
    },
    created: function () {
      if (!document.getElementById('gag-placement')) {
        const gagPlacement = document.createElement('div');
        gagPlacement.id = 'gag-placement';

        document.body.appendChild(gagPlacement);
      }
    },
    updated: function () {
      if (this.visible && !this.shouldBeVisible && !this.closing) {
        document.getElementById('gag-placement').appendChild(this.$refs._gag);
        this.$nextTick(() => {
          this.shouldBeVisible = true;
        })
      }
    },
    methods: {
      close() {
        this.$emit('close');
        this.closing = true;
        this.shouldBeVisible = false;
        setTimeout(() => {
          this.closing = false;
          document.getElementById('gag-placement').removeChild(this.$refs._gag);
        }, 210);
      }
    }
  }
</script>

<style scoped>
  .gag {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    z-index: 1;

    display: flex;
    justify-content: center;
    align-items: center;

    opacity: 0;
    visibility: hidden;
    background: rgba(0, 0, 0, 0.7);

    transition: .2s opacity;
  }

  .gag.invisible {
    opacity: 0;
    visibility: visible;
  }

  .gag.visible {
    opacity: 1 !important;
    visibility: visible;
  }

  .modal {
    background: #fff;
    box-shadow: 3px 3px 10px 2px #000;
    max-height: 90%;
    max-width: 90%;
  }

  .modal-content {
    padding: 2rem;
    max-height: 500px;
    max-width: 90%;
    overflow: auto;
  }
</style>
