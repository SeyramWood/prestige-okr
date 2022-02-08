<template>
  <section class="page-align">
    <v-data-table
      :headers="headers"
      :items="getCompanies"
      :sort-by="['created_at']"
      :sort-desc="[false, true]"
      v-model="selectedAccounts"
      show-select
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Company Accounts</v-toolbar-title>
          <v-spacer></v-spacer>

          <v-btn class="ma-2" outlined rounded color="warning">
            Export Accounts
          </v-btn>
          <!-- <v-dialog v-model="dialog" max-width="500px">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                  rounded
                >
                  <v-icon>mdi-plus</v-icon> Invite People
                </v-btn>
              </template>
              <v-card>
                <form @submit.prevent="sendInvite()">
                  <v-card-title>
                    <strong class="text-h5"> Invite People </strong>
                  </v-card-title>
                  <v-card-text>
                    <span class="text-h6 pt-2"
                      >Enter email of people to invite, seperate emails with
                      whitespace.</span
                    >
                  </v-card-text>
                  <v-card-text>
                    <v-container>
                      <v-textarea
                        solo
                        name="input-7-4"
                        label="Enter emails"
                        v-model="email"
                        :error-messages="emailError"
                      ></v-textarea>
                    </v-container>
                  </v-card-text>

                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      color="dark darken-1"
                      text
                      @click="close"
                      :disabled="isSendInvite"
                    >
                      Cancel
                    </v-btn>
                    <v-btn
                      color="primary darken-1"
                      text
                      type="submit"
                      :loading="isSendInvite"
                    >
                      Send Invite
                    </v-btn>
                  </v-card-actions>
                </form>
              </v-card>
            </v-dialog> -->
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5"
                >Are you sure you want to delete this account?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete()"
                  >Cancel</v-btn
                >
                <v-btn
                  color="blue darken-1"
                  text
                  @click="deleteCompanyAccountConfirm()"
                  >OK</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
        <v-menu bottom origin="center center" transition="scale-transition">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              color="info"
              dark
              v-bind="attrs"
              v-on="on"
              class="ml-4"
              :disabled="!isSelectedPeople"
            >
              More Actions
              <v-icon>mdi-chevron-down</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item dense @click="openDeleteDialog(true)">
              <v-list-item-title>Delete people</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:item.logo="{ item }">
        <div class="company-logo">
          <img :src="`/storage/logo/${item.logo}`" alt="Comapany Logo" />
        </div>
      </template>
      <template v-slot:item.created_at="{ item }">
        {{ formatDateTime(item.created_at) }}
      </template>
      <template v-slot:item.account.status="{ item }">
        <v-chip
          text-color="white"
          color="green"
          small
          filter
          pill
          v-if="item.account.status === 'active'"
        >
          {{ item.account.status.toUpperCase() }}
        </v-chip>
        <v-chip text-color="white" color="red" small filter pill v-else>
          {{ item.account.status.toUpperCase() }}
        </v-chip>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              color="black"
              dark
              small
              v-bind="attrs"
              v-on="on"
              @click="viewCompanyDetails(item)"
            >
              mdi-eye-outline
            </v-icon>
          </template>
          <span>View more</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              color="black"
              dark
              v-bind="attrs"
              v-on="on"
              @click="updateAccountStatus(item)"
            >
              mdi-account-check-outline
            </v-icon>
          </template>
          <span
            >{{
              item.account.status === "active" ? "Deactivate" : "Activate"
            }}
            account</span
          >
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              small
              color="red"
              @click="openDeleteDialog(false, item.id)"
              v-bind="attrs"
              v-on="on"
            >
              mdi-delete
            </v-icon>
          </template>
          <span>Delete Account</span>
        </v-tooltip>
      </template>

      <template v-slot:no-data>
        <v-icon dark color="black"> mdi-database </v-icon>
        <h5>Ooops! No record found.</h5>
      </template>
    </v-data-table>
    <s-drawer v-model="detailsDrawer">
      <section class="add__objective">
        <header class="add__objective__header">
          <div class="add__objective__header__title">
            <span>
              <v-icon>mdi-briefcase-eye-outline</v-icon>
            </span>
            <h4>Company Details</h4>
          </div>
          <v-btn icon @click="detailsDrawer = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </header>
        <main>
          <div class="company__details" v-if="currentCompany">
            <h1 class="company__details__title">Profile</h1>
            <div class="company__details__body">
              <div class="">Name</div>
              <div class="">{{ currentCompany.name }}</div>
            </div>
            <div class="company__details__body">
              <div class="">E-mail</div>
              <div class="">{{ currentCompany.email }}</div>
            </div>
            <div class="company__details__body">
              <div class="">Created on</div>
              <div class="">
                {{ formatDateTime(currentCompany.created_at) }}
              </div>
            </div>
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
                  v-if="currentCompany.account.status === 'active'"
                >
                  {{ currentCompany.account.status.toUpperCase() }}
                </v-chip>
                <v-chip text-color="white" color="red" small filter pill v-else>
                  {{ currentCompany.account.status.toUpperCase() }}
                </v-chip>
              </div>
            </div>
            <div class="company__details__body">
              <div class="">Type</div>
              <div class="cap">
                {{ currentCompany.account.type }}
              </div>
            </div>
            <div class="company__details__body">
              <div class="">Members</div>
              <div class="cap">
                {{ currentCompany.account.num_members }}
              </div>
            </div>
            <div class="company__details__body">
              <div class="">Max Members</div>
              <div class="cap">
                {{ currentCompany.account.max_num_members }}
              </div>
            </div>
            <div class="company__details__body">
              <div class="">Expiry</div>
              <div class="cap">
                {{
                  formatDateTime(
                    currentCompany.account.expired_at.replace(" ", "T")
                  )
                }}{{
                  formatDateDiff(
                    currentCompany.account.expired_at.replace(" ", "T"),
                    new Date()
                  )
                }}
              </div>
            </div>
          </div>
        </main>
      </section>
    </s-drawer>
    <s-snackbar v-model="snackOpen">
      {{ snackMessage }}
    </s-snackbar>
  </section>
</template>

<script>
import Layout from "../components/layout/Layout";
import { mapActions, mapGetters } from "vuex";
import SSnackbar from "../components/SSnackbar";
import { dateTime } from "../mixins";
import SDrawer from "../components/SDrawer";
export default {
  name: "Companies",
  layout: Layout,
  mixins: [dateTime],
  components: {
    SSnackbar,
    SDrawer,
  },
  props: {
    companies: Array,
  },
  computed: {
    ...mapGetters(["getCompanies"]),
    isSelectedPeople() {
      return this.selectedAccounts.length ? true : false;
    },
  },
  watch: {
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {
    this.dispatchCompany({ type: "ADD_COMPANIES", payload: this.companies });
  },
  mounted() {
    this.$nextTick(() => {
      if (this.isAdmin) {
        this.headers = [
          ...this.headers,
          { text: "Actions", value: "actions", sortable: false },
        ];
      }
    });
  },
  data: () => ({
    search: "",
    dialog: false,
    dialogDelete: false,
    detailsDrawer: false,
    currentCompany: null,

    headers: [
      {
        text: "",
        align: "start",
        value: "logo",
      },
      { text: "Company", align: "start", value: "name" },
      { text: "E-mail", value: "email" },
      { text: "Created On", value: "created_at" },
      { text: "Status", value: "account.status" },
    ],

    email: "",
    emailError: "",
    selectedAccount: "",
    selectedAccounts: [],
    selectedTeams: "",
    selectedTeamsError: "",

    isSelected: false,
    selectedTeam: null,
    deleteManyAccounts: false,
  }),
  methods: {
    ...mapActions(["dispatchCompany", "dispatchNotifications"]),

    openDeleteDialog(many, id = null) {
      this.deleteManyAccounts = many;
      this.selectedAccount = id;
      this.dialogDelete = true;
    },
    updateUserRole(user) {
      this.$axios
        .put(`/dashboard/update-user-role/${user.id}/${user.role_id}`)
        .then((res) => {
          if (res.data.updated) {
            this.showSnackMessage("User role successfully updated.");
            this.$nextTick(() => {
              this.dispatchUser({
                type: "UPDATE_USER_ROLE",
                payload: {
                  ...user,
                  role_id: res.data.role.id,
                  role: res.data.role.name,
                },
              });
            });
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    viewCompanyDetails(company) {
      this.currentCompany = company;
      this.$nextTick(() => {
        this.detailsDrawer = true;
      });
    },
    updateAccountStatus(company) {
      const newStatus =
        company.account.status === "active" ? "inactive" : "active";
      this.$axios
        .put(
          `/dashboard/update-account-status/${company.account.id}/${newStatus}`
        )
        .then((res) => {
          if (res.data.updated) {
            this.showSnackMessage("Account status successfully updated.");
            this.$nextTick(() => {
              this.dispatchCompany({
                type: "UPDATE_COMPANY_ACCOUNT_STATUS",
                payload: {
                  ...company,
                  account: {
                    ...company.account,
                    status: newStatus,
                  },
                },
              });
            });
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },

    deleteCompanyAccount() {
      this.$axios
        .delete(`/dashboard/delete-company-account/${this.selectedAccount}`)
        .then((res) => {
          this.showSnackMessage("Account successfully deleted.");
          this.dispatchCompany({
            type: "DELETE_COMPANY_ACCOUNT",
            payload: this.selectedAccount,
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    deleteCompanyAccounts() {
      const companies = this.selectedAccounts.map((a) => a.id);
      this.$axios
        .delete(`/dashboard/delete-company-accounts/${companies}`)
        .then((res) => {
          this.showSnackMessage("Account successfully deleted.");
          this.dispatchCompany({
            type: "DELETE_COMPANY_ACCOUNTS",
            payload: companies,
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    deleteCompanyAccountConfirm() {
      this.closeDelete();
      if (this.deleteManyAccounts) {
        this.deleteCompanyAccounts();
      } else {
        this.deleteCompanyAccount();
      }
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        //
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.peple__status {
  display: flex;
  justify-content: space-between;
}
.options-menu {
  display: flex;
  justify-content: center;
  align-items: center;
  & > p {
    padding-left: 1rem;
    color: #797979;
  }
}
</style>
