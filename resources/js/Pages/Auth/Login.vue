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
              loginErrors && (loginErrors.username || loginErrors.password)
            "
          >
            <span v-if="loginErrors.username"
              >{{ loginErrors.username }}<br
            /></span>
            <span v-if="loginErrors.password">{{ loginErrors.password }}</span>
          </v-alert>
        </v-scale-transition>
        <form @submit.prevent="signin()" class="mb-4">
          <h2>Sign in & manage your OKRs</h2>
          <div class="form">
            <div class="form__group__icon">
              <span class="prepend"><v-icon>mdi-account-outline</v-icon></span>
              <input
                type="text"
                id="username"
                placeholder="Username"
                aria-label="username"
                v-model="login.username"
              />
            </div>
            <div class="form__group__icon">
              <span class="prepend"><v-icon>mdi-key-outline</v-icon></span>
              <input
                type="password"
                id="password"
                placeholder="Password"
                aria-label="password"
                v-model="login.password"
                ref="revealPassword"
              />
              <span class="append clickable" @click="revealPassword()">
                <v-icon v-if="reveal">mdi-eye-outline</v-icon>
                <v-icon v-else>mdi-eye-off-outline</v-icon>
              </span>
            </div>
          </div>

          <button type="submit" class="login__form__btn block">Continue</button>
        </form>
        <div class="login__form__footer">
          <Link href="/register">I don't have account</Link>
          <v-divider vertical></v-divider>
          <Link href="/register">Forget password?</Link>
        </div>
      </div>
    </section>
  </v-app>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue";
import { revealPassword } from "../../mixins";
export default {
  name: "Login",
  components: { Link },
  mixins: [revealPassword],
  data() {
    return {
      isSubmitting: false,
      login: {
        username: "",
        password: "",
        remember: false,
      },
      loginErrors: {
        username: "",
        password: "",
      },
    };
  },
  methods: {
    signin() {
      this.isSubmitting = true;
      this.setLoginErrors();
      this.$inertia.post(`/login`, this.login, {
        onError: (err) => {
          this.isSubmitting = false;
          this.setLoginErrors(err);
        },
      });
    },
    setLoginErrors(err = {}) {
      this.loginErrors = {
        username: err.username || "",
        password: err.password || "",
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>