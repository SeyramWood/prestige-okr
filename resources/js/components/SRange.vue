<template>
  <div :class="['range__container']">
    <input
      type="range"
      :min="minValue"
      :max="maxValue"
      :value="value"
      @input="input($event)"
      @change="change($event)"
      ref="range"
    />
    <span :class="[getColor]">{{
      `${value === minValue ? 0 : parseInt((value / maxValue) * 100)}${unit}`
    }}</span>
  </div>
</template>

<script>
export default {
  name: "SRange",
  props: {
    color: {
      require: true,
      type: String,
    },
    status: {
      require: true,
      type: String,
    },
    min: {
      type: Number,
      default: 0,
    },
    max: {
      type: Number,
      default: 100,
    },
    unit: {
      type: String,
      default: "",
    },
    value: "",
  },
  computed: {
    getColor() {
      return this.pStatus;
    },
  },
  beforeMount() {
    this.$nextTick(() => {
      this.maxValue = this.max;
      this.minValue = this.min;
      this.pStatus = this.status;
    });
  },
  created() {
    this.$watch(
      () => this.value,
      (val) => {
        this.$nextTick(() => {
          this.pStatus = this.getStatus(val, this.minValue, this.maxValue);
          this.updateBackground(this.target);
        });
      }
    );
  },
  data() {
    return {
      minValue: null,
      maxValue: null,
      target: null,
      pStatus: "",
    };
  },
  methods: {
    input(e) {
      this.target = e.target;
      this.$emit(
        "input",
        parseInt(e.target.value) === this.min ? 0 : e.target.value
      );
    },
    change(e) {
      this.target = e.target;
      this.$emit("change", e);
    },

    updateBackground(el) {
      if (el) {
        el.nextElementSibling.style = this.generateBackground(el);
      }
    },
    generateBackground(el) {
      if (el.value === this.minValue) {
        return;
      }
      let percentage =
        ((el.value - this.minValue) / (this.maxValue - this.minValue)) * 100;
      switch (this.getColor) {
        case "cm":
          return `background: linear-gradient(to right, #4caf50, #4caf50 ${percentage}%, #4caf4f65 ${percentage}%, #4caf4f65 100%)`;
        case "ot":
          return `background: linear-gradient(to right, #256caf, #256caf ${percentage}%, #256caf81 ${percentage}%, #256caf81 100%)`;
        case "bd":
          return `background: linear-gradient(to right, #fdc408, #fdc408 ${percentage}%, #f7ca0248 ${percentage}%, #f7ca0248 100%)`;
        case "ar":
          return `background: linear-gradient(to right, #e65039, #e65039 ${percentage}%, #e650394f ${percentage}%, #e650394f 100%`;
        case "ns":
          return `background: linear-gradient(to right, #979797, #979797 ${percentage}%, #9797974f ${percentage}%, #9797974f 100%)`;
      }
    },
    getStatus(value, start, target) {
      const $target = parseInt((value / target) * 100);
      if (value === start || value === 0) {
        return "ns";
      }
      if ($target === parseInt(((1.0 * target) / target) * 100)) {
        return "cm";
      }
      if ($target >= parseInt(((0.5 * target) / target) * 100)) {
        return "ot";
      }
      if ($target < parseInt(((0.25 * target) / target) * 100)) {
        return "ar";
      }
      if ($target >= parseInt(((0.25 * target) / target) * 100)) {
        return "bd";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.range__container {
  width: 100%;
  height: 20px;
  border-radius: 999px;
  position: relative;
  span {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    border-radius: 999px;
    color: #fff;
    padding-left: 1rem;
    &.cm {
      background: rgba(76, 175, 79, 0.308);
    }
    &.ot {
      background: rgba(37, 108, 175, 0.308);
    }
    &.bd {
      background: rgba(253, 196, 8, 0.308);
    }
    &.ar {
      background: rgba(230, 80, 57, 0.308);
    }
    &.ns {
      background: #9797974f;
      // background: rgba(231, 231, 231, 0.308);
    }
  }
  & > input[type="range"] {
    position: absolute;
    top: 0;
    left: 0;
    -webkit-appearance: none;
    width: 100%;
    height: 100%;
    border-radius: 999px;
    z-index: 1;
    outline: none;
    padding: 0;
    margin: 0;
    // color: #4caf4f65;
    // color: #4caf50;
    background: transparent;
    cursor: grab;
    &::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 10px;
      height: 30px;
      border-radius: 9999px;
      background: #c7c7c7;
      cursor: -webkit-grab;
      -webkit-transition: background 0.15s ease-in-out;
      transition: background 0.15s ease-in-out;
    }
    &::-moz-range-thumb {
      width: 10px;
      height: 30px;
      border-radius: 9999px;
      background: #c7c7c7;
      cursor: -moz-grab;
      -webkit-transition: background 0.15s ease-in-out;
      transition: background 0.15s ease-in-out;
    }
  }
}

/* custom thumb */

.range__slider [type="range"]::-webkit-slider-thumb:hover {
  // background: #f7ca02bd;
}

.range__slider [type="range"]::-moz-range-thumb:hover {
  // background: #f9ca24;
}

/* remove border */
input::-moz-focus-inner,
input::-moz-focus-outer {
  border: 0;
}
</style>