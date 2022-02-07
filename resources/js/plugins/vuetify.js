import "@mdi/font/css/materialdesignicons.css";
import Vue from "vue";
import Vuetify from "vuetify/lib";
import minifyTheme from "minify-css-string";

Vue.use(Vuetify);

const opts = {
    icons: {
        iconfont: "mdi"
    },
    theme: {
        dark: false,
        default: "light",
        disable: false,
        options: {
            minifyTheme: minifyTheme,
        },
        themes: {
            light: {
                primary: "#651FFF",
                secondary: "#661FFF",
                accent: "#82B1FF",
                error: "#FF5252",
                info: "#2196F3",
                success: "#4CAF50",
                warning: "#FB8C00",
                anchor: "#651FFF"
            },
            dark: {
                primary: "#6200EA",
                secondary: "#424242",
                accent: "#FF4081",
                error: "#FF5252",
                info: "#2196F3",
                success: "#4CAF50",
                warning: "#FB8C00",
                anchor: "#6200EA"
            }
        }
    }
};

export default new Vuetify(opts);
