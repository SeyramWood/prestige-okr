<template>
  <v-snackbar v-model="value" bottom :timeout="-1">
    <slot></slot>
    <template v-slot:action="{ attrs }">
      <v-btn color="pink" text v-bind="attrs" @click="close"> Close </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
export default {
  name: "SSnackbar",
  props: {
    value: {
      type: Boolean,
      default: false,
    },
  },
  created() {
    this.$watch(
      () => this.value,
      (val) => {
        if (val) {
          setTimeout(() => {
            this.close();
          }, 6000);
        }
      }
    );
  },
  data() {
    return {
      snackbar: false,
    };
  },
  methods: {
    close() {
      this.$emit("input", false);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>