<template>
  <v-app>
    <v-navigation-drawer app>
        <!---->
    </v-navigation-drawer>

    <v-app-bar app>
        <!---->
    </v-app-bar>

    <!-- Sizes your content based upon application components -->
    <!---->

    <v-main>
      <!-- Provides the application the proper gutter -->
      <!---->
      <v-container fluid>
        <!-- If using vue-router -->
        <!---->
        Welcome Page <a href="">link</a>
        <v-icon color="primary">mdi-account</v-icon>
        <v-btn depressed color="primary"> Primary </v-btn>
        <router-view></router-view>
      </v-container>
    </v-main>

    <v-footer app>
      <!---->
    </v-footer>
  </v-app>
</template>

<script>
export default {
  name: "Welcome",
};
</script>

<style lang="scss" scoped>
</style>
