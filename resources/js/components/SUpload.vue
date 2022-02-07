<template>
  <label class="upload__btn">
    <span class="upload__btn__content">
      <template v-if="loading">
        <svg viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient
              x1="8.042%"
              y1="0%"
              x2="65.682%"
              y2="23.865%"
              id="a"
            >
              <stop stop-color="#fff" stop-opacity="0" offset="0%" />
              <stop stop-color="#fff" stop-opacity=".631" offset="63.146%" />
              <stop stop-color="#fff" offset="100%" />
            </linearGradient>
          </defs>
          <g fill="none" fill-rule="evenodd">
            <g transform="translate(1 1)">
              <path
                d="M36 18c0-9.94-8.06-18-18-18"
                id="Oval-2"
                stroke="url(#a)"
                stroke-width="2"
              >
                <animateTransform
                  attributeName="transform"
                  type="rotate"
                  from="0 18 18"
                  to="360 18 18"
                  dur="0.9s"
                  repeatCount="indefinite"
                />
              </path>
              <circle fill="#fff" cx="36" cy="18" r="1">
                <animateTransform
                  attributeName="transform"
                  type="rotate"
                  from="0 18 18"
                  to="360 18 18"
                  dur="0.9s"
                  repeatCount="indefinite"
                />
              </circle>
            </g>
          </g>
        </svg>
      </template>
      <template v-else>
        <span class="mr-1">Upload</span>
        <v-icon> mdi-cloud-upload</v-icon>
      </template>
    </span>
    <input type="file" @change="change($event)" />
  </label>
</template>

<script>
export default {
  name: "SUpload",
  props: {
    value: null,
    loading: Boolean,
  },
  mounted() {
    // this.$watch(
    //   () => this.loading,
    //   (n, o) => {
    //     console.log(n, o);
    //   }
    // );
  },
  data() {
    return {
      snackbar: false,
    };
  },
  methods: {
    change(e) {
      this.$emit("selectFile", e.target.files[0]);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>