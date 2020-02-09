<template>
  <div class="container">
    <div class="circle circle1"></div>
    <div class="circle circle2"></div>
    <div class="circle circle3"></div>
    <div class="circle circle4"></div>
  </div>
</template>

<script>
  export default {
    name: "loader"
  }
</script>

<style scoped>
  @keyframes bounce {
    0% { transform: translateY(1.3rem); }
    100% { transform: translateY(0); }
  }

  .container {
    display: inline-block;
    margin: 2rem;
  }

  .circle {
    display: inline-block;
    background-color: #777;
    width: 8px;
    height: 8px;
    border-radius: 4px;
    margin: 0 .1rem;
  }

  .circle1 {
    animation: bounce .6s cubic-bezier(.1,.25,.1,1) 0s infinite alternate both;
  }

  .circle2 {
    animation: bounce .6s cubic-bezier(.1,.25,.1,1) .15s infinite alternate both;
  }

  .circle3 {
    animation: bounce .6s cubic-bezier(.1,.25,.1,1) .3s infinite alternate both;
  }

  .circle4 {
    animation: bounce .6s cubic-bezier(.1,.25,.1,1) .45s infinite alternate both;
  }
</style>
