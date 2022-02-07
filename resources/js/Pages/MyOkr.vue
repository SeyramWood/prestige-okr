<template>
  <section class="profile">
    <header class="profile__header">
      <div class="profile__header__left">
        <div class="profile__header__left__avatar">
          <img :src="`/storage/avatar/team.svg`" alt="Avatar" srcset="" />
        </div>
        <div class="profile__header__left__details">
          <template>
            <p class="name">Manage Your Personal OKR</p>
            <span class="date"> Keep record of all objectives </span>
          </template>
          <div class="">
            <v-btn color="primary" @click="openObjectiveDrawer()">
              <v-icon> mdi-plus</v-icon>
              <span class="mr-1">Add Objective</span>
            </v-btn>
          </div>
        </div>
      </div>
      <div class="profile__header__right">
        <div class="okr__stats total">
          <span>OKRs</span>
          <span>{{ getUserObjectives.length }}</span>
        </div>
        <div class="okr__stats notStarted">
          <span>Not Started OKR</span>
          <span>{{ objectiveNotStartedCount }}</span>
        </div>
        <div class="okr__stats inprogress">
          <span>Inprogress OKR</span>
          <span>{{ objectiveInprogressCount }}</span>
        </div>
        <div class="okr__stats completed">
          <span>Completed OKR</span>
          <span>{{ objectiveCompletedCount }}</span>
        </div>
      </div>
    </header>
    <main class="profile__main page-align">
      <objective-keyresult ref="objectiveKeyresult"></objective-keyresult>
    </main>
    <v-overlay absolute :value="overlay" z-index="120">
      <div class="overlay__cta" v-if="!isObjectives">
        <div class="image">
          <img :src="`/storage/bg/objective.svg`" alt="" srcset="" />
        </div>
        <article>
          Ooops! There are no <strong>objectives</strong> to display!
        </article>
        <v-btn color="primary" @click="openObjectiveDrawer()">
          <v-icon> mdi-plus</v-icon>
          <span class="mr-1">Add Objective</span>
        </v-btn>
      </div>
    </v-overlay>
  </section>
</template>

<script>
import Layout from "../components/layout/Layout";
import { mapActions, mapGetters } from "vuex";
import { dateTime } from "../mixins";
import ObjectiveKeyresult from "../components/common/ObjectiveKeyresult";

export default {
  name: "MyOkr",
  layout: Layout,
  components: {
    ObjectiveKeyresult,
  },
  // mixins: [dateTime],
  props: {
    userObjectives: Array,
    teams: Array,
  },
  computed: {
    ...mapGetters(["getUserObjectives"]),
    isObjectives() {
      return this.getUserObjectives.length ? true : false;
    },
    objectiveCompletedCount() {
      return this.getUserObjectives.reduce((total, obj) => {
        let t = total;
        if (obj.metric.progress === 100) {
          t++;
        }
        return t;
      }, 0);
    },
    objectiveInprogressCount() {
      return this.getUserObjectives.reduce((total, obj) => {
        let t = total;
        if (obj.metric.progress >= 1 && obj.metric.progress <= 99) {
          t++;
        }
        return t;
      }, 0);
    },
    objectiveNotStartedCount() {
      return this.getUserObjectives.reduce((total, obj) => {
        let t = total;
        if (obj.metric.progress === 0) {
          t++;
        }
        return t;
      }, 0);
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.$watch(
        () => this.getUserObjectives,
        (n) => {
          if (n.length) {
            this.overlay = false;
          } else {
            this.overlay = true;
          }
        },
        { deep: true, immediate: true }
      );
    });
  },
  created() {
    this.dispatchTeam({ payload: this.teams });
    this.dispatchObjective({ payload: this.userObjectives });
  },
  data() {
    return {
      overlay: false,
    };
  },

  methods: {
    ...mapActions(["dispatchObjective", "dispatchTeam"]),
    openObjectiveDrawer() {
      this.$refs.objectiveKeyresult.openObjectiveDrawer({ type: "individual" });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
