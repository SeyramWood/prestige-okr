<template>
  <section class="page-align">
    <v-data-table
      :headers="headers"
      :items="getCompanies"
      :sort-by="['created_at']"
      :sort-desc="[false, true]"
      v-model="selectedPeople"
      show-select
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Companies</v-toolbar-title>
          <v-spacer></v-spacer>

          <v-btn class="ma-2" outlined rounded color="warning">
            Export People
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
                >Are you sure you want to delete this person?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete()"
                  >Cancel</v-btn
                >
                <v-btn color="blue darken-1" text @click="deleteUserConfirm()"
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
            <v-list-item dense @click="assignTeamDialog = true">
              <v-list-item-title>Assign team</v-list-item-title>
            </v-list-item>
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
      <!-- <template v-slot:item.actions="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              color="primary"
              dark
              small
              v-bind="attrs"
              v-on="on"
              @click="updateUserRole(item)"
            >
              mdi-shield-account-outline
            </v-icon>
          </template>
          <span>Update user role</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              color="primary"
              dark
              v-bind="attrs"
              v-on="on"
              @click="selectPerson(item.id)"
            >
              mdi-account-check-outline
            </v-icon>
          </template>
          <span>Assign team</span>
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
          <span>Delete</span>
        </v-tooltip>
      </template> -->

      <template v-slot:no-data>
        <v-icon dark color="black"> mdi-database </v-icon>
        <h5>Ooops! No record found.</h5>
      </template>
    </v-data-table>

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
export default {
  name: "Companies",
  layout: Layout,
  mixins: [dateTime],
  components: {
    SSnackbar,
  },
  props: {
    companies: Array,
  },
  computed: {
    ...mapGetters(["getCompanies"]),
    isSelectedPeople() {
      return this.selectedPeople.length ? true : false;
    },
    formTitle() {
      return this.editedIndex === -1 ? "New Item" : "Edit Item";
    },
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    assignTeamDialog(val) {
      val || this.closeAssignTeamDialog();
    },
  },

  created() {
    this.dispatchCompany({ type: "ADD_COMPANIES", payload: this.companies });
    console.log(this.companies);
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
    assignTeamDialog: false,
    isSendInvite: false,

    headers: [
      {
        text: "",
        align: "start",
        value: "logo",
      },
      { text: "Company", align: "start", value: "name" },
      { text: "E-mail", value: "email" },
      { text: "Created On", value: "created_at" },
    ],

    email: "",
    emailError: "",
    selectedPerson: "",
    selectedPeople: [],
    selectedTeams: "",
    selectedTeamsError: "",

    editedIndex: -1,
    editedItem: {
      name: "",
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0,
    },
    defaultItem: {
      name: "",
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0,
    },
    isSelected: false,
    selectedTeam: null,
    deleteManyUsers: false,
  }),
  methods: {
    ...mapActions(["dispatchCompany", "dispatchNotifications"]),
    sendInvite() {
      this.isSendInvite = true;
      this.emailError = "";
      this.$inertia.post(
        "/dashboard/send-invite",
        { email: this.email },
        {
          preserveScroll: true,
          errorBag: "inviteEmailError",
          onSuccess: (res) => {
            this.isSendInvite = false;
            this.showSnackMessage("Invites successfully sent.");
            this.dispatchNotifications({
              type: "ADD_NOTIFICATION",
              payload: res.notification,
            });
            this.close();
          },
          onError: (err) => {
            this.isSendInvite = false;
            this.showSnackMessage(err.invite);
            this.emailError = err.email || "";
            this.close();
          },
        }
      );
    },
    selectPerson(id) {
      this.selectedPerson = id;
      this.assignTeamDialog = true;
    },
    openDeleteDialog(many, id = "") {
      this.deleteManyUsers = many;
      this.selectedPerson = id;
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
    assignTeam() {
      this.$inertia.post(
        `/dashboard/assign-team/${this.selectedPerson}`,
        { teams: this.selectedTeams },
        {
          preserveScroll: true,
          errorBag: "assignTeamError",
          onSuccess: (page) => {
            this.closeAssignTeamDialog();
            this.showSnackMessage("Teams successfully assigned.");
            this.dispatchNotifications({
              type: "ADD_NOTIFICATION",
              payload: page.notification,
            });
          },
          onError: (err) => {
            this.closeAssignTeamDialog();
            if (err.teams) {
              this.selectedTeamsError = err.teams || "";
            } else {
              this.showSnackMessage(err.assign);
            }
          },
        }
      );
    },

    assignTeams() {
      const users = this.selectedPeople.map((p) => p.id);
      const teams = this.selectedTeams;
      this.$inertia.post(
        `/dashboard/assign-teams`,
        { users, teams },
        {
          preserveScroll: true,
          onSuccess: (page) => {
            this.closeAssignTeamDialog();
            this.showSnackMessage("Teams successfully assigned.");
            // this.dispatchNotifications({
            //   type: "ADD_NOTIFICATION",
            //   payload: page.notification,
            // });
          },
          onError: (err) => {
            if (err.teams) {
              this.closeAssignTeamDialog();
              this.selectedTeamsError = err.teams || "";
            } else {
              this.showSnackMessage(err.assign);
            }
          },
        }
      );
    },

    deleteUser() {
      this.$axios
        .delete(`/dashboard/delete-user/${this.selectedPerson}`)
        .then((res) => {
          this.showSnackMessage("Person successfully deleted.");
          this.dispatchUser({
            type: "DELETE_PERSON",
            payload: this.selectedPerson,
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    deleteUsers() {
      const users = this.selectedPeople.map((p) => p.id);
      this.$axios
        .delete(`/dashboard/delete-users/${users}`)
        .then((res) => {
          this.showSnackMessage("People successfully deleted.");
          this.dispatchUser({
            type: "DELETE_PEOPLE",
            payload: users,
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    deleteUserConfirm() {
      this.closeDelete();
      if (this.deleteManyUsers) {
        this.deleteUsers();
      } else {
        this.deleteUser();
      }
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.email = "";
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        //
      });
    },

    closeAssignTeamDialog() {
      this.assignTeamDialog = false;
      this.selectedTeamsError = "";
      this.selectedPerson = "";
      this.selectedPeople = [];
      this.selectedTeams = "";
    },

    showAssignTeamDialog(id = null) {
      if (id) {
        this.selectedPeople.onePeople = id;
        this.selectedPeople.multiplePeople = false;
      } else {
        this.selectedPeople.multiplePeople = true;
      }
      this.assignTeamDialog = true;
    },

    setSelectedTeam(value) {
      this.selectedTeam = value;
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
