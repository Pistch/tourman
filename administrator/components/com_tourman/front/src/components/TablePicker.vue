<template>

  <svg width="400" viewBox="0 0 1297 896" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
      <g id="rooms" transform="translate(0.208971, 0.000000)" fill="#FFFEF7" stroke="#979797" stroke-width="3">
        <path d="M798.291029,1.50066114 L798.291029,894.5 L1266.21678,894.5 L1266.21678,695.5 L967.291029,695.5 L967.291029,680.905566 L1294.37846,680.905566 L1294.37846,1.71926981 L798.291029,1.50066114 Z" id="Rectangle"></path>
        <path d="M1.5,1.71993066 L1.5,469.23955 L206.212458,469.23955 L207.712458,469.23955 L207.712458,813.5 L604.291029,813.5 L604.291029,167.237177 L351.291029,167.237177 L351.291029,1.71993066 L1.5,1.71993066 Z" id="Rectangle"></path>
      </g>

      <g @click="selectTable(1)" :class="['table', selected === 1 && 'selected']" id="t1">
        <rect id="Rectangle-Copy-7" stroke="#979797" fill="#3C7148" x="857.5" y="28.5" width="63" height="124"></rect>
        <text id="1" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="878.6" y="104">1</tspan>
        </text>
      </g>
      <g @click="selectTable(2)" :class="['table', selected === 2 && 'selected']" id="t2">
        <rect id="Rectangle-Copy-8" stroke="#979797" fill="#3C7148" x="994.5" y="28.5" width="63" height="124"></rect>
        <text id="2" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1016.6" y="101">2</tspan>
        </text>
      </g>
      <g @click="selectTable(3)" :class="['table', selected === 3 && 'selected']" id="t3">
        <rect id="Rectangle-Copy-9" stroke="#979797" fill="#3C7148" x="1154.5" y="28.5" width="63" height="124"></rect>
        <text id="3" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1175.6" y="101">3</tspan>
        </text>
      </g>
      <g @click="selectTable(4)" :class="['table', selected === 4 && 'selected']" id="t4">
        <rect id="Rectangle-Copy-11" stroke="#979797" fill="#3C7148" x="857.5" y="217.5" width="63" height="124"></rect>
        <text id="4" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="878.6" y="290">4</tspan>
        </text>
      </g>
      <g @click="selectTable(5)" :class="['table', selected === 5 && 'selected']" id="t5">
        <rect id="Rectangle-Copy-10" stroke="#979797" fill="#3C7148" x="994.5" y="217.5" width="63" height="124"></rect>
        <text id="5" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1015.6" y="290">5</tspan>
        </text>
      </g>
      <g @click="selectTable(6)" :class="['table', selected === 6 && 'selected']" id="t6">
        <rect id="Rectangle-Copy-19" stroke="#979797" fill="#3C7148" transform="translate(1186.500000, 280.000000) rotate(90.000000) translate(-1186.500000, -280.000000) " x="1155" y="218" width="63" height="124"></rect>
        <text id="6" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1175.6" y="292">6</tspan>
        </text>
      </g>
      <g @click="selectTable(7)" :class="['table', selected === 7 && 'selected']" id="t7">
        <rect id="Rectangle-Copy-13" stroke="#979797" fill="#3C7148" x="857.5" y="406.5" width="63" height="124"></rect>
        <text id="7" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="879.6" y="480">7</tspan>
        </text>
      </g>
      <g @click="selectTable(8)" :class="['table', selected === 8 && 'selected']" id="t8">
        <rect id="Rectangle-Copy-12" stroke="#979797" fill="#3C7148" x="994.5" y="406.5" width="63" height="124"></rect>
        <text id="8" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1015.6" y="480">8</tspan>
        </text>
      </g>
      <g @click="selectTable(9)" :class="['table', selected === 9 && 'selected']" id="t9">
        <rect id="Rectangle-Copy-14" stroke="#979797" fill="#3C7148" x="1154.5" y="406.5" width="63" height="124"></rect>
        <text id="9" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1175.6" y="480">9</tspan>
        </text>
      </g>
      <g @click="selectTable(12)" :class="['table', selected === 12 && 'selected']" id="t12">
        <rect id="Rectangle-Copy-17" stroke="#979797" fill="#3C7148" transform="translate(963.500000, 626.000000) rotate(90.000000) translate(-963.500000, -626.000000) " x="932" y="564" width="63" height="124"></rect>
        <text id="12" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="943.7" y="637">12</tspan>
        </text>
      </g>
      <g @click="selectTable(14)" :class="['table', selected === 14 && 'selected']" id="t14">
        <rect id="Rectangle-Copy-18" stroke="#979797" fill="#3C7148" transform="translate(1186.500000, 626.000000) rotate(90.000000) translate(-1186.500000, -626.000000) " x="1155" y="564" width="63" height="124"></rect>
        <text id="14" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="1166.7" y="639">14</tspan>
        </text>
      </g>
      <g @click="selectTable(15)" :class="['table', selected === 15 && 'selected']" id="t15">
        <rect id="Rectangle-Copy-16" stroke="#979797" fill="#5AA6C9" x="841.5" y="719.5" width="63" height="124"></rect>
        <text id="15" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="853.7" y="792">15</tspan>
        </text>
      </g>
      <g @click="selectTable(16)" :class="['table', selected === 16 && 'selected']" id="t16">
        <rect id="Rectangle-Copy-15" stroke="#979797" fill="#5AA6C9" x="937.5" y="719.5" width="63" height="124"></rect>
        <text id="16" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="948.7" y="792">16</tspan>
        </text>
      </g>
      <g @click="selectTable(20)" :class="['table', selected === 20 && 'selected']" id="t20">
        <rect id="Rectangle-Copy" stroke="#979797" fill="#5EC436" x="32.5" y="306.5" width="63" height="124"></rect>
        <text id="20" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="44.2" y="380">20</tspan>
        </text>
      </g>
      <g @click="selectTable(21)" :class="['table', selected === 21 && 'selected']" id="t21">
        <rect id="Rectangle" stroke="#979797" fill="#5EC436" x="30.5" y="145.5" width="63" height="124"></rect>
        <text id="21" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="42.2" y="217">21</tspan>
        </text>
      </g>
      <g @click="selectTable(22)" :class="['table', selected === 22 && 'selected']" id="t22">
        <rect id="Rectangle-Copy-25" stroke="#979797" fill="#5EC436" transform="translate(92.500000, 59.000000) rotate(90.000000) translate(-92.500000, -59.000000) " x="61" y="-3" width="63" height="124"></rect>
        <text id="22" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="72.7" y="72">22</tspan>
        </text>
      </g>
      <g @click="selectTable(23)" :class="['table', selected === 23 && 'selected']" id="t23">
        <rect id="Rectangle-Copy-3" stroke="#979797" fill="#5EC436" x="258.5" y="624.5" width="63" height="124"></rect>
        <text id="23" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="270.2" y="700">23</tspan>
        </text>
      </g>
      <g @click="selectTable(24)" :class="['table', selected === 24 && 'selected']" id="t24">
        <rect id="Rectangle-Copy-2" stroke="#979797" fill="#5EC436" x="258.5" y="444.5" width="63" height="124"></rect>
        <text id="24" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="270.2" y="517">24</tspan>
        </text>
      </g>
      <g @click="selectTable(25)" :class="['table', selected === 25 && 'selected']" id="t25">
        <rect id="Rectangle-Copy-22" stroke="#979797" fill="#5EC436" transform="translate(290.500000, 357.000000) rotate(90.000000) translate(-290.500000, -357.000000) " x="259" y="295" width="63" height="124"></rect>
        <text id="25" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="270.7" y="370">25</tspan>
        </text>
      </g>
      <g @click="selectTable(26)" :class="['table', selected === 26 && 'selected']" id="t26">
        <rect id="Rectangle-Copy-23" stroke="#979797" fill="#5EC436" transform="translate(290.500000, 238.000000) rotate(90.000000) translate(-290.500000, -238.000000) " x="259" y="176" width="63" height="124"></rect>
        <text id="26" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="270.7" y="252">26</tspan>
        </text>
      </g>
      <g @click="selectTable(27)" :class="['table', selected === 27 && 'selected']" id="t27">
        <rect id="Rectangle-Copy-4" stroke="#979797" fill="#5EC436" x="258.5" y="29.5" width="63" height="124"></rect>
        <text id="27" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="270.2" y="102">27</tspan>
        </text>
      </g>
      <g @click="selectTable(28)" :class="['table', selected === 28 && 'selected']" id="t28">
        <rect id="Rectangle-Copy-20" stroke="#979797" fill="#5AA6C9" transform="translate(513.500000, 749.000000) rotate(90.000000) translate(-513.500000, -749.000000) " x="482" y="687" width="63" height="124"></rect>
        <text id="28" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="493.7" y="762">28</tspan>
        </text>
      </g>
      <g @click="selectTable(29)" :class="['table', selected === 29 && 'selected']" id="t29">
        <rect id="Rectangle-Copy-21" stroke="#979797" fill="#5AA6C9" transform="translate(513.500000, 662.000000) rotate(90.000000) translate(-513.500000, -662.000000) " x="482" y="600" width="63" height="124"></rect>
        <text id="29" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="493.7" y="675">29</tspan>
        </text>
      </g>
      <g @click="selectTable(32)" :class="['table', selected === 32 && 'selected']" id="t32">
        <rect id="Rectangle-Copy-6" stroke="#979797" fill="#3C7148" x="481.5" y="469.5" width="63" height="124"></rect>
        <text id="32" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="493.2" y="542">32</tspan>
        </text>
      </g>
      <g @click="selectTable(33)" :class="['table', selected === 33 && 'selected']" id="t33">
        <rect id="Rectangle-Copy-5" stroke="#979797" fill="#3C7148" x="481.5" y="306.5" width="63" height="124"></rect>
        <text id="33" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="493.2" y="380">33</tspan>
        </text>
      </g>
      <g @click="selectTable(34)" :class="['table', selected === 34 && 'selected']" id="t34">
        <rect id="Rectangle-Copy-24" stroke="#979797" fill="#3C7148" transform="translate(513.500000, 239.000000) rotate(90.000000) translate(-513.500000, -239.000000) " x="482" y="177" width="63" height="124"></rect>
        <text id="34" font-size="32" font-weight="normal" fill="#FFFFFF">
          <tspan x="493.7" y="251">34</tspan>
        </text>
      </g>
      <g id="tennis">
        <rect id="Rectangle-Copy-26" stroke="#979797" fill="#E3E3E3" x="1033.5" y="719.5" width="63" height="124"></rect>
        <rect id="Rectangle-Copy-27" stroke="#979797" fill="#E3E3E3" x="1110.5" y="719.5" width="63" height="124"></rect>
        <rect id="Rectangle-Copy-28" stroke="#979797" fill="#E3E3E3" x="1187.5" y="719.5" width="63" height="124"></rect>
      </g>
    </g>
  </svg>
</template>

<script>
  export default {
    name: 'table-picker',
    props: ['selected'],
    methods: {
      selectTable(table) {
        this.$emit('select', table);
      }
    }
  };
</script>

<style scoped>
  .table {
    cursor: pointer;
    user-select: none;
  }

  .table:not(.selected):hover {
    opacity: .8;
    transition: .2s opacity;
  }

  .selected {
    filter: brightness(0.6);
  }
</style>
