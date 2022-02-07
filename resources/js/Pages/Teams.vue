<template>
  <section class="profile">
    <header class="profile__header">
      <div class="profile__header__left">
        <div class="profile__header__left__avatar">
          <img :src="`/storage/avatar/team.png`" alt="Team Avatar" srcset="" />
        </div>
        <div class="profile__header__left__details">
          <template v-if="teamObjectivesIsArray">
            <p class="name">Please select a team to view OKRs</p>
            <span class="date"> Keep record of all objectives </span>
          </template>
          <template v-else>
            <p class="name">{{ getTeamOjectives.name }}</p>
            <span class="date">
              Joined: {{ formatDateTime(getAuthProfile.created_at) }}
            </span>
          </template>

          <div class="" v-if="teamObjectivesIsArray">
            <v-btn color="primary" @click="openObjectiveDrawer('all')">
              <v-icon> mdi-plus</v-icon>
              <span class="mr-1">Add Objective</span>
            </v-btn>
          </div>
          <div class="" v-else>
            <v-btn
              color="primary"
              @click="openObjectiveDrawer(teamObjectives.id)"
            >
              <v-icon> mdi-plus</v-icon>
              <span class="mr-1">Add Objective</span>
            </v-btn>
          </div>
        </div>
      </div>
      <div class="profile__header__right">
        <template v-if="teamObjectivesIsArray">
          <div class="okr__stats total">
            <span>OKRs</span>
            <span>{{ getUserObjectives.length }}</span>
          </div>
          <div class="okr__stats notStarted">
            <span>Not Started OKR</span>
            <span>{{ teamObjectiveNotStartedCount }}</span>
          </div>
          <div class="okr__stats inprogress">
            <span>Inprogress OKR</span>
            <span>{{ teamObjectiveInprogressCount }}</span>
          </div>
          <div class="okr__stats completed">
            <span>Completed OKR</span>
            <span>{{ teamObjectiveCompletedCount }}</span>
          </div>
        </template>
        <template v-else>
          <div class="okr__stats total">
            <span>OKRs</span>
            <span>{{ getTeamOjectives.objectives.length }}</span>
          </div>
          <div class="okr__stats notStarted">
            <span>Not Started OKR</span>
            <span>{{ teamObjectiveNotStartedCount }}</span>
          </div>
          <div class="okr__stats inprogress">
            <span>Inprogress OKR</span>
            <span>{{ teamObjectiveInprogressCount }}</span>
          </div>
          <div class="okr__stats completed">
            <span>Completed OKR</span>
            <span>{{ teamObjectiveCompletedCount }}</span>
          </div>
        </template>
      </div>
    </header>
    <main class="profile__main page-align">
      <objective-keyresult ref="objectiveKeyresult"></objective-keyresult>
    </main>
    <v-overlay absolute :value="overlay" z-index="120">
      <template v-if="isTeam">
        <div class="overlay__cta" v-if="!isObjectives">
          <div class="image">
            <img :src="`/storage/bg/objective.svg`" alt="" srcset="" />
          </div>
          <article>
            Ooops! There are no <strong>objectives</strong> to display!
          </article>
          <v-btn
            color="primary"
            @click="openObjectiveDrawer(teamObjectives.id || 'all')"
          >
            <v-icon> mdi-plus</v-icon>
            <span class="mr-1">Add Objective</span>
          </v-btn>
        </div>
        <div class="overlay__cta" v-else>
          <div class="image">
            <img :src="`/storage/bg/team.svg`" alt="" srcset="" />
          </div>
          <article>
            Please select a team to view <strong>objectives</strong>
          </article>
        </div>
      </template>

      <div class="overlay__cta" v-else>
        <div class="image">
          <img :src="`/storage/bg/objective.svg`" alt="" srcset="" />
        </div>
        <article>
          Ooops! You don't <strong>belong</strong> to any team yet!
        </article>
      </div>
    </v-overlay>
  </section>
</template>

<script>
import Layout from "../components/layout/Layout";
import { mapActions, mapGetters } from "vuex";
import { dateTime } from "../mixins";
import STabs from "../components/tab/STabs";
import STab from "../components/tab/STab";
import ObjectiveKeyresult from "../components/common/ObjectiveKeyresult.vue";
export default {
  name: "Teams",
  layout: Layout,
  mixins: [dateTime],
  components: {
    STabs,
    STab,
    ObjectiveKeyresult,
  },
  props: ["myTeams", "teams", "teamObjectives"],
  computed: {
    ...mapGetters(["getAuthProfile", "getTeamOjectives", "getUserObjectives"]),
    isTeam() {
      return this.myTeams && this.myTeams.teams.length ? true : false;
    },
    isObjectives() {
      return this.getUserObjectives.length ? true : false;
    },
    teamObjectivesIsArray() {
      return Array.isArray(this.teamObjectives) ? true : false;
    },
    teamObjectiveCompletedCount() {
      return this.getUserObjectives.reduce((total, obj) => {
        let t = total;
        if (obj.metric.progress === 100) {
          t++;
        }
        return t;
      }, 0);
    },
    teamObjectiveInprogressCount() {
      return this.getUserObjectives.reduce((total, obj) => {
        let t = total;
        if (obj.metric.progress >= 1 && obj.metric.progress <= 99) {
          t++;
        }
        return t;
      }, 0);
    },
    teamObjectiveNotStartedCount() {
      return this.getUserObjectives.reduce((total, obj) => {
        let t = total;
        if (obj.metric.progress === 0) {
          t++;
        }
        return t;
      }, 0);
    },
  },
  created() {
    this.dispatchTeam({ type: "ADD_MY_TEAMS", payload: this.myTeams });
    this.dispatchTeam({ payload: this.teams });
    if (this.teamObjectivesIsArray) {
      this.dispatchObjective({ payload: this.teamObjectives });
    } else {
      this.dispatchObjective({ payload: this.teamObjectives.objectives });
    }
    this.dispatchTeam({
      type: "ADD_TEAM_OBJAECTIVES",
      payload: this.teamObjectives,
    });
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

  data: () => ({
    overlay: false,
  }),
  methods: {
    ...mapActions(["dispatchTeam", "dispatchObjective"]),
    openObjectiveDrawer(team) {
      this.$refs.objectiveKeyresult.openObjectiveDrawer({ type: "team", team });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>