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
          <v-alert type="error" v-show="accountErrors.accountError.length">
            <span>{{ accountErrors.accountError }}</span>
          </v-alert>
        </v-scale-transition>
        <h2>Sign up for OKRs</h2>
        <v-stepper v-model="step" vertical flat class="login__stepper">
          <v-stepper-step step="1" :complete="step > 1">
            Company Details
          </v-stepper-step>
          <v-stepper-content step="1">
            <form @submit.prevent="createAdminUser('detail')" class="pl-1 pb-2">
              <div class="form__group">
                <input
                  type="text"
                  name=""
                  id="company-name"
                  placeholder="Compay name"
                  aria-label="company-name"
                  v-model="account.companyName"
                />
              </div>
              <v-fade-transition>
                <template v-if="accountErrors.companyName.length">
                  <p class="mb-1" style="margin-top: -0.5rem; color: orangered">
                    {{ accountErrors.companyName }}
                  </p>
                </template>
              </v-fade-transition>
              <div class="form__group">
                <input
                  type="email"
                  name=""
                  id="company-email"
                  placeholder="Company email"
                  aria-label="company-email"
                  v-model="account.companyEmail"
                />
              </div>
              <v-fade-transition>
                <template v-if="accountErrors.companyEmail.length">
                  <p class="mb-1" style="margin-top: -0.5rem; color: orangered">
                    {{ accountErrors.companyEmail }}
                  </p>
                </template>
              </v-fade-transition>
              <button type="submit" class="login__form__btn">Continue</button>
            </form>
          </v-stepper-content>

          <v-stepper-step step="2" :complete="step > 2">
            Login Credentials
          </v-stepper-step>
          <v-stepper-content step="2">
            <form @submit.prevent="createAdminUser('finish')" class="pl-1 pb-2">
              <div class="form__group__icon">
                <span class="prepend"
                  ><v-icon>mdi-account-outline</v-icon></span
                >
                <input
                  type="text"
                  name=""
                  id="username"
                  placeholder="Username"
                  aria-label="username"
                  v-model="account.username"
                />
              </div>
              <v-fade-transition>
                <template v-if="accountErrors.username.length">
                  <p class="mb-1" style="margin-top: -0.5rem; color: orangered">
                    {{ accountErrors.username }}
                  </p>
                </template>
              </v-fade-transition>
              <div class="form__group__icon">
                <span class="prepend"><v-icon>mdi-key-outline</v-icon></span>
                <input
                  type="password"
                  name=""
                  id="password"
                  placeholder="Password"
                  aria-label="password"
                  v-model="account.password"
                  ref="revealPassword"
                />
                <span class="append clickable" @click="revealPassword()">
                  <v-icon v-if="reveal">mdi-eye-outline</v-icon>
                  <v-icon v-else>mdi-eye-off-outline</v-icon>
                </span>
              </div>
              <v-fade-transition>
                <template v-if="accountErrors.password.length">
                  <p class="mb-1" style="margin-top: -0.5rem; color: orangered">
                    {{ accountErrors.password }}
                  </p>
                </template>
              </v-fade-transition>
              <button
                type="button"
                class="login__form__btn mr-3"
                @click="step = 1"
              >
                Previous
              </button>
              <button type="submit" class="login__form__btn">Submit</button>
            </form>
          </v-stepper-content>
        </v-stepper>

        <div class="login__form__footer">
          <Link href="/">Already have an account?</Link>
        </div>
      </div>
    </section>
  </v-app>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue";
import { revealPassword } from "../../mixins";
export default {
  name: "Register",
  components: { Link },
  mixins: [revealPassword],
  data() {
    return {
      step: 1,
      isSubmitting: false,
      account: {
        companyName: "",
        companyEmail: "",
        username: "",
        password: "",
        remember: true,
      },
      accountErrors: {
        companyName: [],
        companyEmail: [],
        username: [],
        password: [],
        accountError: "",
      },
    };
  },
  methods: {
    createAdminUser(step) {
      this.isSubmitting = true;
      this.setAccountErrors();
      this.$inertia.post(`/create-admin`, this.account, {
        errorBag: "createAccountError",
        headers: { step },
        onSuccess: ({ props }) => {
          this.isSubmitting = false;
          if (props.flash.stepCompleted && step === "detail") {
            this.setAccountErrors();
            this.step = 2;
          }
        },
        onError: (err) => {
          this.isSubmitting = false;
          this.setAccountErrors(err);
        },
      });
    },
    setAccountErrors(err = {}) {
      this.accountErrors = {
        companyName: err.companyName || [],
        companyEmail: err.companyEmail || [],
        username: err.username || [],
        password: err.password || [],
        accountError: err.accountError || "",
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>