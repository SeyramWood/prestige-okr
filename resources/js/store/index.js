import Vue from "vue";
import Vuex from "vuex";

import Admin from "./modules/admin";
import Objective from "./modules/objective";
import Notification from "./modules/notification";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: { Admin, Objective, Notification}
});
