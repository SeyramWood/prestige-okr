require("./bootstrap");
import Vue from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue";
import { InertiaProgress } from "@inertiajs/progress";
import axios from "axios";
import vuetify from "@/plugins/vuetify";
import { DateTime, Interval, Duration } from "luxon";
import dropper from "dropper.mood";
import "dropper.mood/dist/dropper.css";
import VueMeta from "vue-meta";
import store from "./store";

Vue.use(dropper);
Vue.use(VueMeta, {
    // optional pluginOptions
    refreshOnceOnNavigation: true
});

Object.defineProperty(Vue.prototype, "$luxon", { value: DateTime });
Object.defineProperty(Vue.prototype, "$axios", { value: axios });
Object.defineProperty(Vue.prototype, "route", { value: window.route });

InertiaProgress.init();

Vue.mixin({
    computed: {},
    beforeMount() {
        this.$nextTick(() => {
            this.isAdmin = this.$page.props.role === "admin";
            this.prestigeAccount = this.$page.props.accountType === null;
        });
    },
    data() {
        return {
            snackOpen: false,
            snackMessage: "",
            isAdmin: null,
            prestigeAccount: null
        };
    },
    methods: {
        isNull(el) {
            if (el === "null" || el === null) {
                return "";
            }
            return el;
        },
        showSnackMessage(message) {
            this.snackMessage = message;
            this.snackOpen = true;
        }
    }
});

createInertiaApp({
    resolve: name => import(`./Pages/${name}`),
    setup({ el, App, props }) {
        new Vue({
            vuetify,
            store,
            render: h => h(App, props)
        }).$mount(el);
    }
});
