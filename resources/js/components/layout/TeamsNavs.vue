<template>
  <div>
    <ul class="mini__sub__navs">
      <li
        v-for="team in getMyTeams.teams"
        :key="team.id"
        :class="[
          'mini__sub__navs__link',
          route().current('teams', {
            team: team.id,
            slug: team.name.replace(' ', '-').toLowerCase(),
          }) && 'nav-active',
        ]"
      >
        <Link
          :href="
            route('teams', {
              team: team.id,
              slug: team.name.replace(' ', '-').toLowerCase(),
            })
          "
          >{{ team.name }}</Link
        >
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
import { mapActions, mapGetters } from "vuex";
export default {
  name: "TeamsNavs",
  components: {
    STabs,
    STab,
    Link,
    SAccordions,
    SAccordion,
  },
  computed: {
    ...mapGetters(["getMyTeams"]),
  },
  methods: {
    ...mapActions([]),
  },
};
</script>

<style lang="scss" scoped>
</style>