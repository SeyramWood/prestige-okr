<template>
  <section class="page-align">
    <v-data-table
      :headers="headers"
      :items="getTeams"
      :sort-by="['created_at']"
      :sort-desc="[true, true]"
      show-select
      v-model="selectedTeams"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Teams</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            icon
            class="mb-2"
            v-if="isSelected"
            @click="confirmTeamDelete('many')"
          >
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-icon color="red" dark v-bind="attrs" v-on="on">
                  mdi-delete
                </v-icon>
              </template>
              <span>Delete teams</span>
            </v-tooltip>
          </v-btn>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="primary"
                dark
                class="mb-2"
                v-bind="attrs"
                v-on="on"
                rounded
              >
                <v-icon>mdi-plus</v-icon> Add New Team
              </v-btn>
            </template>
            <v-card>
              <form @submit.prevent="isEdittingTeam ? updateTeam() : addTeam()">
                <v-card-title>
                  <strong class="text-h5"> Add New Team </strong>
                </v-card-title>
                <v-card-text>
                  <v-text-field
                    outlined
                    dense
                    label="Name"
                    placeholder="Enter team name"
                    v-model="team.name"
                    :error-messages="teamErrors.name"
                  ></v-text-field>
                  <v-textarea
                    outlined
                    dense
                    label="Description"
                    placeholder="Enter team description"
                    v-model="team.description"
                    :error-messages="teamErrors.description"
                  ></v-textarea>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="error darken-1" text @click="close">
                    Cancel
                  </v-btn>
                  <v-btn color="primary darken-1" text type="submit">
                    {{ isEdittingTeam ? "Save" : "Add" }}
                  </v-btn>
                </v-card-actions>
              </form>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="350px">
            <v-card>
              <v-card-title class="text-h6">{{
                `Are you sure you want to delete this ${
                  deleteType === "single" ? "team" : "teams"
                }?`
              }}</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="dark darken-1" text @click="closeDelete"
                  >Cancel</v-btn
                >
                <v-btn
                  color="blue darken-1"
                  text
                  @click="
                    deleteType === 'single' ? deleteTeam() : deleteTeams()
                  "
                  :loading="isDeleting"
                  >OK</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon small color="primary" class="mr-2" @click="editTeam(item)">
          mdi-pencil
        </v-icon>
        <v-icon small color="red" @click="confirmTeamDelete('single', item.id)">
          mdi-delete
        </v-icon>
      </template>
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
import Layout from "../../components/layout/Layout";
import { mapActions, mapGetters } from "vuex";
import SSnackbar from "../../components/SSnackbar";
export default {
  name: "people",
  layout: Layout,
  components: {
    SSnackbar,
  },
  props: {
    teams: Array,
  },
  computed: {
    ...mapGetters(["getTeams"]),
    isSelected() {
      return this.selectedTeams.length && true;
    },
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {
    this.dispatchTeam({ payload: this.teams });
  },
  data: () => ({
    search: "",
    dialog: false,
    dialogDelete: false,
    isSubmitting: false,
    isEdittingTeam: false,
    isDeleting: false,
    teamToDeleteId: "",
    deleteType: "single",
    selectedTeams: [],
    headers: [
      {
        text: "Name",
        align: "start",
        value: "name",
      },
      { text: "Description", value: "description" },
      { text: "Actions", value: "actions", sortable: false, align: "center" },
    ],
    team: {
      name: "",
      description: "",
    },
    teamErrors: {
      name: "",
      description: "",
    },
  }),
  methods: {
    ...mapActions(["dispatchTeam", "dispatchNotification"]),
    addTeam() {
      this.setTeamErrors();
      this.$axios
        .post("/dashboard/add-team", this.team)
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.created) {
            this.clearTeamForm();
            this.dispatchTeam({ type: "ADD_TEAM", payload: res.data.team });
            this.dispatchNotifications({
              type: "ADD_NOTIFICATION",
              payload: res.data.notification,
            });
            this.showSnackMessage("Team successfully added.");
          } else {
            this.showSnackMessage(res.data.message);    
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          if (err.response.status === 422) {
            this.setTeamErrors(err.response.data.errors);
          } else {
            console.trace(err);
          }
        });
    },
    updateTeam() {
      this.setTeamErrors();
      this.$axios
        .put(`/dashboard/update-team/${this.team.id}`, this.team)
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.updated) {
            this.close();
            this.dispatchTeam({ type: "UPDATE_TEAM", payload: res.data.team });
            this.showSnackMessage("Team successfully updated.");
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          if (err.response.status === 422) {
            this.setTeamErrors(err.response.data.errors);
          } else {
            console.trace(err);
          }
        });
    },
    deleteTeam() {
      this.isDeleting = true;
      this.$axios
        .delete(`/dashboard/delete-team/${this.teamToDeleteId}`)
        .then((res) => {
          this.isDeleting = false;
          if (res.status === 200 && res.data.deleted) {
            this.closeDelete();
            this.dispatchTeam({
              type: "DELETE_TEAM",
              payload: this.teamToDeleteId,
            });
            this.showSnackMessage("Team successfully deleted.");
          }
        })
        .catch((err) => {
          this.isDeleting = false;
          console.trace(err);
        });
    },
    deleteTeams() {
      this.isDeleting = true;
      const ids = this.selectedTeams.map((c) => c.id);
      this.$axios
        .delete(`/dashboard/delete-teams/${ids}`)
        .then((res) => {
          this.isDeleting = false;
          if (res.status === 200 && res.data.deleted) {
            this.closeDelete();
            this.dispatchTeam({
              type: "DELETE_TEAMS",
              payload: ids,
            });
            this.showSnackMessage("Teams successfully deleted.");
          }
        })
        .catch((err) => {
          this.isDeleting = false;
          console.trace(err);
        });
    },
    editTeam(item) {
      this.isEdittingTeam = true;
      this.team = {
        id: item.id,
        name: item.name,
        description: item.description,
      };
      this.dialog = true;
    },

    clearTeamForm() {
      this.team = {
        name: "",
        description: "",
      };
    },
    setTeamErrors(obj = {}) {
      this.teamErrors = {
        name: obj.name || "",
        description: obj.description || "",
      };
    },

    confirmTeamDelete(type = "single", id = "") {
      this.deleteType = type;
      this.teamToDeleteId = id;
      this.dialogDelete = true;
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.isEdittingTeam = false;
        this.clearTeamForm();
        this.setTeamErrors();
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.teamToDeleteId = "";
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
</style>
