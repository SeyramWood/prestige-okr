<template>
  <section class="profile">
    <header class="profile__header">
      <div class="profile__header__left">
        <div class="profile__header__left__avatar">
          <img
            :src="`/storage/avatar/${
              getAuthProfile.avatar
                ? `user/${getAuthProfile.avatar}`
                : 'default.jpg'
            }`"
            alt="User Avatar"
            srcset=""
          />
        </div>
        <div class="profile__header__left__details">
          <p class="name">
            <template
              v-if="getAuthProfile.first_name && getAuthProfile.last_name"
            >
              {{ `${getAuthProfile.first_name} ${getAuthProfile.last_name}` }}
            </template>
            <template v-else>
              {{ getAuthProfile.username }}
            </template>
          </p>
          <span class="date">
            Joined: {{ formatDateTime(getAuthProfile.created_at) }}
          </span>
          <div class="upload__btn">
            <s-upload
              @selectFile="updateUserAvatar($event)"
              :loading="isUploadingAvatar"
            ></s-upload>
          </div>
        </div>
      </div>
      <div class="profile__header__right"></div>
    </header>
    <main class="profile__main">
      <s-tabs sticky="profile__main__sticky" active="bottom">
        <s-tab label="My Profile">
          <div class="profile">
            <div class="form">
              <v-sheet color="white" elevation="1" rounded>
                <div class="profile__container">
                  <h4 class="title">Update Profile</h4>
                  <form @submit.prevent="updateProfile()">
                    <v-text-field
                      label="First Name"
                      placeholder="Enter first name"
                      outlined
                      dense
                      v-model="profile.firstName"
                      :error-messages="profileErrors.firstName"
                    ></v-text-field>
                    <v-text-field
                      label="Last Name"
                      placeholder="Enter last name"
                      outlined
                      dense
                      v-model="profile.lastName"
                      :error-messages="profileErrors.lastName"
                    ></v-text-field>
                    <v-btn
                      color="primary"
                      outlined
                      class="mr-4"
                      type="submit"
                      :loading="isSubmitting"
                    >
                      save
                    </v-btn>
                    <v-btn color="dark" outlined :disabled="isSubmitting"
                      >cancel</v-btn
                    >
                  </form>
                </div>
              </v-sheet>
            </div>
          </div>
        </s-tab>
        <s-tab label="Company Profile">
          <div class="profile">
            <div class="form">
              <v-sheet color="white" elevation="1" rounded>
                <div class="profile__container">
                  <h4 class="title">Company Profile</h4>
                  <div class="brand">
                    <div class="brand__logo">
                      <img
                        :src="`/storage/logo/${getCompany.logo}`"
                        alt="brand logo"
                        srcset=""
                      />
                      <div class="brand__logo__overlay">
                        <fieldset :disabled="!isAdmin" style="border: none">
                          <div class="icon">
                            <s-upload
                              @selectFile="updateCompanyAvatar($event)"
                              :loading="isUploadingLogo"
                            ></s-upload>
                          </div>
                        </fieldset>
                      </div>
                    </div>
                  </div>
                  <fieldset :disabled="!isAdmin" style="border: none">
                    <form @submit.prevent="updateCompany()">
                      <v-text-field
                        label="Name"
                        placeholder="Enter company name"
                        outlined
                        dense
                        v-model="company.name"
                        :error-messages="companyErrors.name"
                      ></v-text-field>
                      <v-text-field
                        label="Email"
                        placeholder="Enter company email"
                        outlined
                        dense
                        disabled
                        type="email"
                        v-model="company.email"
                        :error-messages="companyErrors.email"
                      ></v-text-field>
                      <v-btn
                        color="primary"
                        outlined
                        class="mr-4"
                        type="submit"
                        :loading="isSubmitting"
                      >
                        save
                      </v-btn>
                      <v-btn color="dark" outlined :disabled="isSubmitting"
                        >cancel</v-btn
                      >
                    </form>
                  </fieldset>
                </div>
              </v-sheet>
            </div>
          </div>
        </s-tab>
        <s-tab label="Change Password">
          <div class="profile">
            <div class="form">
              <v-sheet color="white" elevation="1" rounded>
                <div class="profile__container">
                  <h4 class="title">Change Password</h4>
                  <form @submit.prevent="updatePassword()">
                    <v-text-field
                      label="Current Password"
                      placeholder="Current Password"
                      outlined
                      dense
                      type="password"
                      v-model="password.current_password"
                      :error-messages="passwordErrors.currentPassword"
                    ></v-text-field>
                    <v-text-field
                      label="New Password"
                      placeholder="New Password"
                      outlined
                      dense
                      type="password"
                      v-model="password.password"
                      :error-messages="passwordErrors.password"
                    ></v-text-field>
                    <v-text-field
                      label="Confirm Password"
                      placeholder="Confirm New Password"
                      outlined
                      dense
                      type="password"
                      v-model="password.password_confirmation"
                      :error-messages="passwordErrors.password_confirmation"
                    ></v-text-field>
                    <v-btn
                      color="primary"
                      outlined
                      class="mr-4"
                      type="submit"
                      :loading="isSubmitting"
                    >
                      save
                    </v-btn>
                    <v-btn color="dark" outlined :disabled="isSubmitting"
                      >cancel</v-btn
                    >
                  </form>
                </div>
              </v-sheet>
            </div>
          </div>
        </s-tab>
      </s-tabs>
    </main>
    <s-snackbar v-model="snackOpen">
      {{ snackMessage }}
    </s-snackbar>
  </section>
</template>

<script>
import Layout from "../../components/layout/Layout";
import { mapActions, mapGetters } from "vuex";
import { dateTime } from "../../mixins";
import STabs from "../../components/tab/STabs";
import STab from "../../components/tab/STab";
import SUpload from "../../components/SUpload";
import SSnackbar from "../../components/SSnackbar";
export default {
  name: "Profile",
  layout: Layout,
  mixins: [dateTime],
  components: {
    STabs,
    STab,
    SUpload,
    SSnackbar,
  },
  props: {
    companyInfo: Object,
  },
  computed: {
    ...mapGetters(["getAuthProfile", "getCompany"]),
  },
  created() {
    // this.dispatchCompany({ payload: this.companyInfo });
  },
  beforeMount() {
    this.profile = {
      ...this.profile,
      firstName: this.getAuthProfile.first_name,
      lastName: this.getAuthProfile.last_name,
    };
    this.company = {
      ...this.company,
      name: this.getCompany.name,
      email: this.getCompany.email,
    };
  },
  data() {
    return {
      isUploadingAvatar: false,
      isUploadingLogo: false,
      isSubmitting: false,
      profile: {
        firstName: "",
        lastName: "",
      },
      profileErrors: {
        firstName: "",
        lastName: "",
      },
      company: {
        name: "",
        email: "",
      },
      companyErrors: {
        name: "",
        email: "",
      },
      password: {
        current_password: "",
        password: "",
        password_confirmation: "",
      },
      passwordErrors: {
        currentPassword: "",
        password: "",
        password_confirmation: "",
      },
    };
  },
  methods: {
    ...mapActions(["dispatchCompany", "dispatchUser"]),
    updateProfile() {
      this.isSubmitting = true;
      this.$axios
        .put(`/dashboard/update-profile`, this.profile)
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.updated) {
            this.dispatchUser({ payload: res.data.profile });
            this.showSnackMessage("Profile successfully updated.");
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          console.trace(err);
        });
    },
    updateUserAvatar(file) {
      this.isUploadingAvatar = true;
      const data = new FormData();
      data.append("avatar", file);
      this.$axios
        .post(`/dashboard/update-profile-avatar`, data)
        .then((res) => {
          this.isUploadingAvatar = false;
          if (res.status === 200 && res.data.updated) {
            this.dispatchUser({ payload: res.data.profile });
            this.showSnackMessage("Avatar successfully uploaded.");
          }
        })
        .catch((err) => {
          this.isUploadingAvatar = false;
          console.trace(err);
        });
    },
    updateCompany() {
      this.isSubmitting = true;
      this.$axios
        .put(`/dashboard/update-company`, this.company)
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.updated) {
            this.dispatchCompany({ payload: res.data.company });
            this.showSnackMessage("Company successfully updated.");
          } else {
            this.showSnackMessage(res.data.message);
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          console.trace(err);
        });
    },
    updateCompanyAvatar(file) {
      this.isUploadingLogo = true;
      const data = new FormData();
      data.append("logo", file);
      this.$axios
        .post(`/dashboard/update-company-logo`, data)
        .then((res) => {
          this.isUploadingLogo = false;
          if (res.status === 200 && res.data.updated) {
            this.dispatchCompany({ payload: res.data.company });
            this.showSnackMessage("Logo successfully uploaded.");
          } else {
            this.showSnackMessage(res.data.message);
          }
        })
        .catch((err) => {
          this.isUploadingLogo = false;
          console.trace(err);
        });
    },
    updatePassword() {
      this.isSubmitting = true;
      this.$axios
        .put(`/dashboard/update-password`, this.password)
        .then((res) => {
          this.isSubmitting = false;
          console.log(res.data);
          if (res.status === 200 && res.data.updated) {
            this.showSnackMessage("Password updated successfully");
            this.resetPasswordForm();
            this.setPasswordErrors();
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          if (err.response.status === 422) {
            this.setPasswordErrors(err.response.data.errors);
          } else {
            console.trace(err);
          }
        });
    },
    setPasswordErrors(err = {}) {
      this.passwordErrors.currentPassword = err.current_password || "";
      this.passwordErrors.password = err.password || "";
      this.passwordErrors.password_confirmation =
        err.password_confirmation || "";
    },
    resetPasswordForm() {
      this.password.current_password = "";
      this.password.password = "";
      this.password.password_confirmation = "";
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
