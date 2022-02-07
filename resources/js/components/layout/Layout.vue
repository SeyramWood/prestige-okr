<template>
  <v-app>
    <div class="d__wrapper">
      <aside
        :class="`d__side__nav ${
          getMiniBarContent ? 'hide-nav-content' : 'show-nav-content'
        }`"
      >
        <nav class="d__side__nav__mini">
          <section class="d__side__nav__mini__top">
            <span
              :class="[getMiniBarContent ? 'open' : 'close']"
              @click="showMiniBarContent()"
              ><v-icon>mdi-menu-open</v-icon></span
            >
          </section>
          <section class="d__side__nav__mini__middle">
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <span
                  :class="[route().current('dashboard') && 'mini-nav-active']"
                  v-bind="attrs"
                  v-on="on"
                >
                  <Link :href="route('dashboard')">
                    <v-icon>mdi-home-outline</v-icon>
                  </Link>
                </span>
              </template>
              <span>Dashboard</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on"
                  ><v-icon>mdi-folder-outline</v-icon></span
                >
              </template>
              <span>Files</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on"
                  ><v-icon>mdi-chart-arc</v-icon></span
                >
              </template>
              <span>Graphs</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on"
                  ><v-icon>mdi-finance</v-icon></span
                >
              </template>
              <span>Stats</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <span
                  :class="[route().current('settings.*') && 'mini-nav-active']"
                  v-bind="attrs"
                  v-on="on"
                >
                  <Link :href="route('settings.index')">
                    <v-icon>mdi-cog-outline</v-icon>
                  </Link>
                </span>
              </template>
              <span>Settings</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on">
                  <Link :href="route('signout')" as="button" method="post">
                    <v-icon>mdi-logout</v-icon>
                  </Link>
                </span>
              </template>
              <span>Sign out</span>
            </v-tooltip>
          </section>
          <section class="d__side__nav__mini__bottom">
            <div class="d__side__nav__mini__bottom__avatar">
              <img
                :src="`/storage/avatar/user/${getAuthProfile.avatar}`"
                alt="av"
                srcset=""
              />
            </div>
          </section>
        </nav>
        <main class="d__side__nav__content">
          <div class="d__side__nav__content__brand">
            <div class="logo" v-if="getCompany.logo">
              <img
                :src="`/storage/logo/${getCompany.logo}`"
                alt="company logo"
                srcset=""
              />
            </div>
            <div class="name" v-else>
              {{ getCompany.name }}
            </div>
            <strong>{{ getAuthProfile.username }}</strong>
          </div>
          <div class="d__side__nav__content__tab">
            <settings-navs v-if="route().current('settings.*')"></settings-navs>
            <teams-navs v-else-if="route().current('teams')"></teams-navs>
            <dashboard-navs
              :openNotificationDrawer="openNotificationDrawer"
              v-else
            ></dashboard-navs>
          </div>
        </main>
      </aside>
      <main class="d__main">
        <header class="d__main__header">
          <section class="top__bar__nav__wrapper">
            <nav class="top__bar__nav">
              <ul class="top__bar__nav__links">
                <li
                  :class="[
                    'top__bar__nav__links__link',
                    route().current('dashboard') && 'is-link-active',
                  ]"
                >
                  <Link :href="route('dashboard')">OKR</Link>
                </li>
                <li
                  :class="[
                    'top__bar__nav__links__link',
                    route().current('teams') && 'is-link-active',
                  ]"
                >
                  <Link :href="route('teams')"> Team OKR </Link>
                </li>
                <li
                  :class="[
                    'top__bar__nav__links__link',
                    route().current('myokr') && 'is-link-active',
                  ]"
                >
                  <Link :href="route('myokr')"> My OKR</Link>
                </li>

                <li
                  :class="[
                    'top__bar__nav__links__link',
                    route().current('people') && 'is-link-active',
                  ]"
                >
                  <Link href="/dashboard/people"> People </Link>
                </li>
                <li
                  :class="[
                    'top__bar__nav__links__link',
                    route().current('companies') && 'is-link-active',
                  ]"
                  v-if="prestigeAccount && isAdmin"
                >
                  <Link href="/dashboard/companies"> Companies </Link>
                </li>
              </ul>
            </nav>
            <div class="search__bar">
              <label class="search__input">
                <input type="search" placeholder="Search..." />
                <span class="search__input__icon">
                  <v-icon>mdi-magnify</v-icon>
                </span>
              </label>
            </div>
          </section>
        </header>
        <article class="d__main__content">
          <slot></slot>
        </article>
      </main>

      <s-drawer v-model="notificationDrawer">
        <section class="add__objective">
          <header class="add__objective__header">
            <div class="add__objective__header__title">
              <span>
                <v-icon>mdi-bell-ring</v-icon>
              </span>
              <h4>Notifications</h4>
            </div>
            <v-btn icon @click="openNotificationDrawer()">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </header>
          <main>
            <div
              style="margin: 15px"
              v-for="notification in notifications"
              :key="notification.id"
            >
              <v-card outlined elevation="2">
                <v-card-title>
                  {{ notification.description }} {{ notification.show }}
                </v-card-title>

                <v-card-actions>
                  <span> {{ getSinceTime(notification.created_at) }} </span>

                  <v-spacer></v-spacer>

                  <!-- <v-btn
                          icon
                          @click="expandAction(index)"
                        >
                          <v-icon>{{ notification.show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                        </v-btn> -->
                  <!---->
                </v-card-actions>

                <v-expand-transition>
                  <div v-show="notification.show">
                    <v-divider></v-divider>

                    <v-card-text>
                      I'm a thing. But, like most politicians, he promised more
                      than he could deliver. You won't have time for sleeping,
                      soldier, not with all the bed making you'll be doing. Then
                      we'll go with that data file! Hey, you add a one and two
                      zeros to that or we walk! You're going to do his laundry?
                      I've got to find a way to escape.
                    </v-card-text>
                  </div>
                </v-expand-transition>
              </v-card>
            </div>
          </main>
        </section>
      </s-drawer>
    </div>
  </v-app>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import STabs from "../tab/STabs";
import STab from "../tab/STab";
import SAccordions from "../accordion/SAccordions";
import SAccordion from "../accordion/SAccordion";
import { Link } from "@inertiajs/inertia-vue";
import DashboardNavs from "./DashboardNavs";
import SettingsNavs from "./SettingsNavs";
import TeamsNavs from "./TeamsNavs";
import SDrawer from "../SDrawer";
export default {
  name: "Layout",
  components: {
    STabs,
    STab,
    Link,
    SAccordions,
    SAccordion,
    DashboardNavs,
    SettingsNavs,
    TeamsNavs,
    SDrawer,
  },
  computed: {
    ...mapGetters(["getAuthProfile", "getNotifications", "getCompany"]),
    getMiniBarContent() {
      return this.miniBarContent;
    },
  },
  created() {
    this.dispatchCompany({ payload: this.$page.props.companyInfo });
  },
  beforeMount() {
    this.dispatchUser({ payload: this.$page.props.authProfile });
  },
  mounted() {
    this.notifications = this.getNotifications.map((x) =>
      Object.assign(x, { show: false })
    );
  },
  data() {
    return {
      notifications: null,
      notificationDrawer: false,
      tabIndex: 0,
      miniBarContent: false,
      drawer: true,
      notificationDrawer: false,
      items: [
        { title: "Click Mec" },
        { title: "Click Mev" },
        { title: "Click Meb" },
        { title: "Click Me 23" },
      ],
    };
  },
  methods: {
    ...mapActions(["dispatchUser", "dispatchCompany"]),
    showMiniBarContent() {
      this.miniBarContent = !this.miniBarContent;
    },
    openNotificationDrawer() {
      this.notificationDrawer = !this.notificationDrawer;
    },
    getSinceTime(time) {
      const month = [
        "jan",
        "feb",
        "mar",
        "apr",
        "may",
        "jun",
        "jul",
        "aug",
        "sep",
        "oct",
        "nov",
        "dec",
      ];
      const notifDate = new Date(time);
      const today = new Date();
      //Get difference between Notification date and today
      let s = Math.round((today.getTime() - notifDate.getTime()) / 1000);
      if (s > 59) {
        let m = Math.round(s / 60);
        if (m > 59) {
          let h = Math.round(m / 60);
          if (h > 23) {
            let d = Math.round(h / 24);
            if (d > 5) {
              let minutes =
                notifDate.getMinutes() > 9
                  ? notifDate.getMinutes()
                  : "0" + notifDate.getMinutes();
              let hours =
                notifDate.getHours() > 9
                  ? notifDate.getHours()
                  : "0" + notifDate.getHours();
              return (
                notifDate.getDate() +
                " " +
                month[notifDate.getMonth()] +
                ". " +
                notifDate.getFullYear() +
                " " +
                hours +
                ":" +
                minutes
              );
            }
            let end = d > 1 ? "s ago" : " ago";
            return d + " day" + end;
          }
          let end = h > 1 ? "s ago" : " ago";
          return h + " hour" + end;
        }
        let end = m > 1 ? "s ago" : " ago";
        return m + " minute" + end;
      }
      let end = s > 1 ? "s ago" : " ago";
      return s + " second" + end;
    },
    expandAction(notif) {
      this.notifications[notif].show = !this.notifications[notif].show;
    },
  },
};
</script>

<style lang="scss" scoped>
.d__side__nav__mini__top > span.open > i {
  animation: icon-rotate-open 0.2s ease-in-out forwards;
}
.d__side__nav__mini__top > span.close > i {
  animation: icon-rotate-close 0.2s ease-in-out forwards;
}
@keyframes icon-rotate-open {
  0% {
    opacity: 0.5;
    transform: rotate(0deg);
  }
  100% {
    opacity: 1;
    transform: rotate(-180deg);
  }
}
@keyframes icon-rotate-close {
  0% {
    opacity: 0.5;
    transform: rotate(-180deg);
  }
  100% {
    opacity: 1;
    transform: rotate(0deg);
  }
}
</style>
