<template>
  <div class="s__drawer" v-show="value">
    <aside class="s__drawer__content">
      <slot></slot>
    </aside>
  </div>
</template>

<script>
import { Fragment } from "vue-fragment";
export default {
  name: "SDrawer",
  components: { Fragment },

  props: {
    value: {
      required: true,
    },
  },
  computed: {
    getValue() {
      return this.value;
    },
  },
  mounted() {
    this.$watch(
      () => this.getValue,
      (v) => {
        const el = document.getElementsByTagName("body")[0];
        if (v) {
          el.classList.add("drawer-open");
        } else {
          el.classList.remove("drawer-open");
        }
      }
    );
  },
  data() {
    return {
      open: this.value,
    };
  },
  methods: {
    close() {
      this.$emit("input", !this.value);
    },
  },
};
</script>
<style lang="scss" scoped>
</style>