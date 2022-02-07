<template>
  <section>
    <section class="dashboard page-align">
      <section class="dashboard__top">
        <div class="s__row">
          <div class="s__col c--10">
            <div class="s__row">
              <div class="s__col c--3">
                <div class="dashboard__card__top">
                  <div class="dashboard__card__top__left">
                    <div class="dashboard__card__top__left__icon icon--info">
                      <v-icon>mdi-finance</v-icon>
                    </div>
                    <div class="dashboard__card__top__left__content">
                      <p>Company OKR</p>
                      <p>{{ totalCompanyOkrs }}</p>
                    </div>
                  </div>
                  <div class="dashboard__card__top__right text--danger">
                    <p>-25%</p>
                  </div>
                </div>
              </div>
              <div class="s__col c--3">
                <div class="dashboard__card__top">
                  <div class="dashboard__card__top__left">
                    <div class="dashboard__card__top__left__icon icon--danger">
                      <v-icon>mdi-finance</v-icon>
                    </div>
                    <div class="dashboard__card__top__left__content">
                      <p>Team OKR</p>
                      <p>{{ totalTeamOkrs }}</p>
                    </div>
                  </div>
                  <div class="dashboard__card__top__right text--warnning">
                    <p>-10%</p>
                  </div>
                </div>
              </div>

              <div class="s__col c--3">
                <div class="dashboard__card__top">
                  <div class="dashboard__card__top__left">
                    <div
                      class="dashboard__card__top__left__icon icon--warnning"
                    >
                      <v-icon>mdi-finance</v-icon>
                    </div>
                    <div class="dashboard__card__top__left__content">
                      <p>My OKR</p>
                      <p>{{ totalOkrs }}</p>
                    </div>
                  </div>
                  <div class="dashboard__card__top__right text--success">
                    <p>+20%</p>
                  </div>
                </div>
              </div>

              <div class="s__col c--3">
                <div class="dashboard__card__top">
                  <div class="dashboard__card__top__left">
                    <div class="dashboard__card__top__left__icon icon--success">
                      <v-icon>mdi-finance</v-icon>
                    </div>
                    <div class="dashboard__card__top__left__content">
                      <p>Completed OKR</p>
                      <p>{{ completedOkrs }}</p>
                    </div>
                  </div>
                  <div class="dashboard__card__top__right text--info">
                    <p>-75%</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="s__col c--2">
            <div class="dashboard__card__top__button">
              <v-btn color="primary" large @click="openObjectiveDrawer()">
                <v-icon>mdi-plus</v-icon>
                Add Objective
              </v-btn>
            </div>
          </div>
        </div>
      </section>
      <objective-keyresult ref="objectiveKeyresult"></objective-keyresult>
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
  </section>
</template>
<script>
import Layout from "../components/layout/Layout";
import { mapActions, mapGetters } from "vuex";
import { dateTime } from "../mixins";
import ObjectiveKeyresult from "../components/common/ObjectiveKeyresult";

export default {
  name: "Dashboard",
  layout: Layout,
  components: {
    ObjectiveKeyresult,
  },
  // mixins: [dateTime],
  props: {
    organizationObjectives: Array,
    teams: Array,
    totalCompanyOkrs: Number,
    totalOkrs: Number,
    totalTeamOkrs: Number,
    completedOkrs: Number,
    totalRiskOkrs: Number,
    totalNotAtRiskOkrs: Number,
    notifications: Array,
  },
  computed: {
    ...mapGetters(["getUserObjectives"]),
    isObjectives() {
      return this.getUserObjectives.length ? true : false;
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
    this.dispatchObjective({ payload: this.organizationObjectives });
    this.dispatchNotifications({ payload: this.notifications });
  },
  data() {
    return {
      overlay: false,
    };
  },

  methods: {
    ...mapActions([
      "dispatchObjective",
      "dispatchTeam",
      "dispatchNotifications",
    ]),
    openObjectiveDrawer() {
      this.$refs.objectiveKeyresult.openObjectiveDrawer({
        type: "organization",
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
