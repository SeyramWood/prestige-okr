<template>
  <v-app>
    <section class="login">
      <div class="login__image">
        <div class="login__image__wrapper">
          <img src="/storage/bg/welcome_login.svg" alt="" srcset="" />
        </div>
      </div>
      <div class="login__form">
        <v-scale-transition>
          <v-alert
            type="error"
            v-show="
              userErrors &&
              (userErrors.email || userErrors.username || userErrors.password)
            "
          >
            <span v-if="userErrors.username"
              >{{ userErrors.username }} <br
            /></span>
            <span v-if="userErrors.email">{{ userErrors.email }}<br /></span>
            <span v-if="userErrors.password">{{ userErrors.password }}</span>
          </v-alert>
        </v-scale-transition>
        <h2>Welcome to OKRs</h2>
        <form @submit.prevent="register()">
          <div class="form__group">
            <div class="form__group__icon">
              <span class="prepend"><v-icon>mdi-account-outline</v-icon></span>
              <input
                type="text"
                id="username"
                placeholder="Username"
                aria-label="username"
                v-model="user.username"
              />
            </div>
          </div>
          <div class="form__group">
            <div class="form__group__icon">
              <span class="prepend"><v-icon>mdi-email-outline</v-icon></span>
              <input
                type="email"
                name=""
                id=""
                placeholder="Email"
                aria-label="email"
                v-model="user.email"
              />
            </div>
          </div>
          <div class="form__group">
            <div class="form__group__icon">
              <span class="prepend"><v-icon>mdi-key-outline</v-icon></span>
              <input
                type="password"
                id="password"
                placeholder="Password"
                aria-label="password"
                ref="revealPassword"
                v-model="user.password"
              />
              <span class="append clickable" @click="revealPassword()">
                <v-icon v-if="reveal">mdi-eye-outline</v-icon>
                <v-icon v-else>mdi-eye-off-outline</v-icon>
              </span>
            </div>
          </div>
          <button type="submit" class="login__form__btn block">Join</button>
        </form>
      </div>
    </section>
  </v-app>
</template>

<script>
import { revealPassword } from "../../mixins";
export default {
  name: "Invite",
  mixins: [revealPassword],
  props: {
    guest: Object,
  },
  beforeMount() {
    this.user.email = this.guest.email;
    this.user.admin = this.guest.admin;
  },
  data() {
    return {
      isSubmitting: false,
      user: {
        username: "",
        email: "",
        password: "",
        admin: "",
      },
      userErrors: {
        username: "",
        email: "",
        password: "",
      },
    };
  },
  methods: {
    register() {
      this.isSubmitting = true;
      this.setUserErrors();
      this.$inertia.post(`/add-invite`, this.user, {
        onError: (err) => {
          this.isSubmitting = false;
          this.setUserErrors(err);
        },
      });
    },
    setUserErrors(err = {}) {
      this.userErrors = {
        username: err.username || "",
        email: err.email || "",
        password: err.password || "",
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>