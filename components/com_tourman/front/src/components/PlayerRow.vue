<template>
  <div
    :class="[
              'player-row',
              hoveredPlayer === game.pl1_id ? 'hovered-player' : '',
              game.pl1_score > game.pl2_score ? 'winner' : '',
              game.pl2_score > game.pl1_score ? 'loser' : ''
            ]"
    :data-player="game.pl1_id"
    @mouseenter="hoverPlayer(game.pl1_id)"
    @mouseleave="unhoverPlayer"
  >
    <span class="player-name">{{game.user1}}</span>
    <span class="score">{{game.pl1_score}}</span>
  </div>
</template>

<script>
  export default {
    name: "PlayerRow",
    props: [
      'hoverPlayer',
      'unhoverPlayer',
      'id'
    ],
  }
</script>

<style scoped>

</style>
