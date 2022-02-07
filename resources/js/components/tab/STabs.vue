<template>
  <fragment>
    <div :class="['s__tabs', sticky, mb, bg]">
      <div class="s__tabs__tab">
        <ul>
          <li
            v-for="(tab, index) in tabs"
            :key="index"
            @click="selectTab(index)"
            :class="[index === selectedIndex && `isActive ${active}`]"
          >
            <span>{{ tab.label }}</span>
            <div
              :class="`label-badge ${tab.badge > 99 ? 'd-3' : 'd-2'}`"
              v-if="tab.badge"
            >
              <span>{{ tab.badge > 99 ? `99+` : `${tab.badge}` }}</span>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <slot></slot>
  </fragment>
</template>

<script>
import { Fragment } from "vue-fragment";
export default {
  name: "STabs",
  components: {
    Fragment,
  },
  props: {
    tabIndex: {
      type: Number,
      default: 0,
    },
    sticky: {
      type: String,
      default: "",
    },
    mb: {
      type: String,
      default: "",
    },
    bg: {
      type: String,
      default: "",
    },
    active: {
      type: String,
      default: "top",
    },
  },
  computed: {},
  created() {
    this.tabs = this.$children;
  },
  mounted() {
    this.selectTab(this.tabIndex);
  },
  data() {
    return {
      selectedIndex: 0,
      tabs: [],
    };
  },
  methods: {
    selectTab(index) {
      this.selectedIndex = index;
      this.tabs.forEach((tab, idx) => {
        tab.activeTab = idx === index;
      });
    },
  },
};
</script>

<style lang="scss" scoped>
$primary: #651fff;
$text: #26262693;
$active: #000000;
$white: #ffffff;
.s__tabs {
  width: 100%;
  &__tab {
    width: 100%;
    & > ul {
      width: 100%;
      padding-left: 0 !important;
      display: flex;
      justify-content: flex-start;
      align-items: flex-start;
      list-style-type: none;
      & > li {
        position: relative;
        cursor: pointer;
        font-size: 1.2rem;
        user-select: none;
        &:not(:last-child) {
          margin-right: 1rem;
        }
        color: $text;
        border-radius: 3px;
        display: flex;
        & > span {
          letter-spacing: 0.05rem;
          transition: color 0.2s ease-in;
        }
        &:hover > span {
          color: $active;
        }
      }
    }
  }
  &__content {
  }
}
.isActive {
  color: $primary !important;
  &.top::before {
    position: absolute;
    top: -0.5rem;
    left: 0;
    content: "";
    display: block;
    width: 50%;
    height: 3px;
    background: $primary;
    transition: all 0.3s ease-in-out;
    // animation: tab-active-animate 0.3s ease-in-out backwards;
  }
  &.bottom::after {
    position: absolute;
    bottom: -0.5rem;
    left: 0;
    content: "";
    display: block;
    width: 100%;
    height: 3px;
    background: $primary;
    transition: all 0.3s ease-in-out;
    // animation: tab-active-animate 0.3s ease-in-out backwards;
  }
}
@keyframes tab-active-animate {
  0% {
    transform: translateX(-3px);
  }
  100% {
    transform: translateX(0);
  }
}
.label-badge {
  &.d-3 {
    width: 1.9rem;
    height: 1.9rem;
  }
  &.d-2 {
    width: 1.7rem;
    height: 1.7rem;
  }
  margin-left: 0.5rem;
  border-radius: 99999px;
  background: $primary;
  color: $white;
  font-size: 1rem !important;
  display: flex;
  justify-content: center;
  align-items: center;

  &:span {
  }
}
</style>