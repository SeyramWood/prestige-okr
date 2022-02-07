<template>
  <div v-if="reRender">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "SAccordions",
  props: {
    defaultIndex: {
      default: "",
    },
  },
  computed: {
    getDefaultIndex() {
      return this.defaultIndex;
    },
  },
  mounted() {
    this.$watch(
      () => this.SAccordion.count,
      (n) => {
        this.reRender = false;
        this.reRender = true;
      }
    );
  },
  data() {
    return {
      SAccordion: {
        count: 0,
        active: null,
      },
      reRender: true,
    };
  },
  provide() {
    return {
      SAccordion: this.SAccordion,
      defaultIndex: this.getDefaultIndex,
    };
  },
};
</script>

<style lang="scss" scoped>
</style>