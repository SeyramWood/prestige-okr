<template>
  <div>
    <ul class="mini__sub__navs">
      <li
        :class="[
          'mini__sub__navs__link',
          route().current('settings.profile') && 'nav-active',
        ]"
      >
        <Link :href="route('settings.profile')">Profile</Link>
      </li>
      <li
        :class="[
          'mini__sub__navs__link',
          route().current('settings.team') && 'nav-active',
        ]"
      >
        <Link :href="route('settings.team')">Team</Link>
      </li>
      <li
        :class="[
          'mini__sub__navs__link',
          route().current('settings.profile') && 'nav-active',
        ]"
        v-if="!prestigeAccount && isAdmin"
      >
        <Link :href="route('settings.subscription')">Subscription</Link>
      </li>
    </ul>
  </div>
</template>

<script>
import STabs from "../tab/STabs";
import STab from "../tab/STab";
import SAccordions from "../accordion/SAccordions";
import SAccordion from "../accordion/SAccordion";
import { Link } from "@inertiajs/inertia-vue";
import { mapGetters } from "vuex";
export default {
  name: "SettingsNavs",
  components: {
    STabs,
    STab,
    Link,
    SAccordions,
    SAccordion,
  },
};
</script>

<style lang="scss" scoped>
</style>