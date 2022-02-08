<template>
  <section class="page-align">
    <div class="subscription__container">
      <v-sheet color="white" elevation="1" rounded>
        <div class="company__details">
          <h1 class="company__details__title">Subscription</h1>
          <div class="company__details__body">
            <div class="">Status</div>
            <div class="cap">
              <v-chip
                text-color="white"
                color="green"
                small
                filter
                pill
                v-if="getCompany.account.status === 'active'"
              >
                {{ getCompany.account.status.toUpperCase() }}
              </v-chip>
              <v-chip text-color="white" color="red" small filter pill v-else>
                {{ getCompany.account.status.toUpperCase() }}
              </v-chip>
            </div>
          </div>
          <div class="company__details__body">
            <div class="">Type</div>
            <div class="cap">
              {{ getCompany.account.type }}
            </div>
          </div>
          <div class="company__details__body">
            <div class="">Members</div>
            <div class="cap">
              {{ getCompany.account.num_members }}
            </div>
          </div>
          <div class="company__details__body">
            <div class="">Max Members</div>
            <div class="cap">
              {{ getCompany.account.max_num_members }}
            </div>
          </div>
          <div class="company__details__body">
            <div class="">Expiry</div>
            <div class="cap">
              {{
                formatDateTime(getCompany.account.expired_at.replace(" ", "T"))
              }}{{
                formatDateDiff(
                  getCompany.account.expired_at.replace(" ", "T"),
                  new Date()
                )
              }}
            </div>
          </div>
        </div>

        <form
          class="upgrade__form"
          @submit.prevent="upgradeSubscription('renew')"
          v-if="getCompany.account.type !== 'trial'"
        >
          <h1 class="company__details__title">Renew</h1>

          <v-row>
            <v-col cols="10" sm="12" md="10">
              <v-row>
                <v-col cols="6" sm="12" md="6">
                  <v-autocomplete
                    :items="generatePeriods()"
                    item-text="text"
                    item-value="value"
                    outlined
                    dense
                    label="Period"
                    v-model="subscription.renew.period"
                    :error-messages="subscriptionErr.renew.period"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="6" sm="12" md="6">
                  <v-text-field
                    label="Maximum Members"
                    placeholder="Maximum Members"
                    outlined
                    dense
                    type="number"
                    readonly
                    v-model="subscription.renew.maxMembers"
                    :error-messages="subscriptionErr.renew.maxMembers"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="2" sm="12" md="2">
              <v-btn
                color="primary"
                outlined
                type="submit"
                :loading="isSubmitting"
              >
                Submit
              </v-btn>
            </v-col>
          </v-row>
        </form>
        <form
          class="upgrade__form"
          @submit.prevent="upgradeSubscription('upgrade')"
        >
          <h1 class="company__details__title">Upgrade</h1>
          <v-row>
            <v-col cols="10" sm="12" md="10">
              <v-row>
                <v-col cols="6" sm="12" md="6">
                  <v-autocomplete
                    :items="generatePeriods()"
                    item-text="text"
                    item-value="value"
                    outlined
                    dense
                    label="Period"
                    v-model="subscription.upgrade.period"
                    :error-messages="subscriptionErr.upgrade.period"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="6" sm="12" md="6">
                  <v-text-field
                    label="Maximum Members"
                    placeholder="Maximum Members"
                    outlined
                    dense
                    type="number"
                    v-model="subscription.upgrade.maxMembers"
                    :error-messages="subscriptionErr.upgrade.maxMembers"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="2" sm="12" md="2">
              <v-btn
                color="primary"
                outlined
                type="submit"
                :loading="isSubmittingUpgrade"
              >
                Submit
              </v-btn>
            </v-col>
          </v-row>
        </form>
      </v-sheet>
    </div>
  </section>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Layout from "../../components/layout/Layout";
import { dateTime } from "../../mixins";
export default {
  name: "Subscription",
  layout: Layout,
  mixins: [dateTime],
  props: {
    companyInfo: { require: true, type: Object },
  },
  computed: {
    ...mapGetters(["getCompany"]),
  },
  created() {
    this.dispatchCompany({ payload: this.companyInfo });
    this.$nextTick(() => {
      this.subscription.renew.maxMembers =
        this.getCompany.account.max_num_members;
      this.subscription.upgrade.maxMembers =
        this.getCompany.account.max_num_members;
    });
  },
  data: () => ({
    isSubmitting: false,
    isSubmittingUpgrade: false,
    subscription: {
      upgrade: {
        maxMembers: 0,
        period: "",
        type: "upgrade",
      },
      renew: {
        maxMembers: 0,
        period: "",
        type: "renew",
      },
    },
    subscriptionErr: {
      upgrade: {
        maxMembers: "",
        period: "",
      },
      renew: {
        maxMembers: "",
        period: "",
      },
    },
    periods: [{ text: "1 Month", value: 1 }],
  }),
  methods: {
    ...mapActions(["dispatchCompany"]),
    upgradeSubscription(type) {
      this.subscriptionErr[`${type}`].maxMembers = "";
      this.subscriptionErr[`${type}`].period = "";
      if (type === "upgrade") {
        this.isSubmittingUpgrade = true;
      } else {
        this.isSubmitting = true;
      }
      const data = this.$axios
        .post(`/dashboard/upgrade-subscription`, {
          ...this.subscription[`${type}`],
          company: this.getCompany,
        })
        .then((res) => {
          if (type === "upgrade") {
            this.isSubmittingUpgrade = false;
          } else {
            this.isSubmitting = false;
          }
          if (res.data.url) {
            location.assign(res.data.url);
          }
          this.showSnackMessage(
            "Oops! Something went wrong, refresh and try agagin."
          );
        })
        .catch((err) => {
          if (type === "upgrade") {
            this.isSubmittingUpgrade = false;
          } else {
            this.isSubmitting = false;
          }
          this.subscriptionErr[`${type}`].maxMembers =
            err.response.data.errors.maxMembers;
          this.subscriptionErr[`${type}`].period =
            err.response.data.errors.period;
        });
    },
    generatePeriods() {
      let p = [];
      let counter = 1;
      while (counter <= 12) {
        if (counter === 1) {
          p = [
            ...p,
            { text: `${counter * 30} days (${counter} Month)`, value: counter },
          ];
          counter += 2;
          continue;
        }
        p = [
          ...p,
          { text: `${counter * 30} days (${counter} Months)`, value: counter },
        ];
        counter += 3;
      }
      return p;
    },
  },
};
</script>

<style lang="scss" scoped>
.subscription__container {
  width: 70%;
  margin: 0 auto;
}
.company__details {
  padding: 2rem;
}
.upgrade__form {
  padding: 2rem;
}
</style>