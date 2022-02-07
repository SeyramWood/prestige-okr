<template>
  <div>
    <s-tabs mb="mb-3">
      <s-tab label="Notifications" :badge="getNotifications.length">
        <s-accordions :defaultIndex="0">
          <section class="notification">
            <article
              class="notification__container"
              v-for="notification in getNotifications.slice(0, 9)"
              :key="notification.id"
              @click="openNotification(notification.id)"
              role="button"
            >
              <!-- <div class="notification__avatar">
                  <img src="" alt="av" />
                </div> -->
              <!---->
              <div class="notification__content">
                <h5 class="notification__content__title">
                  {{ notification.description }}
                </h5>
                <span class="notification__content__time">
                  {{ getSinceTime(notification.created_at) }}
                </span>
              </div>
            </article>
            <div class="d-flex justify-center pt-3">
              <v-btn color="primary" elevation="2" @click="openNotification()"
                >View more</v-btn
              >
            </div>
          </section>
        </s-accordions>
      </s-tab>
      <!-- <s-tab label="Notifications" :badge="80">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ut similique
        saepe doloribus illo distinctio voluptatum dicta nam et? Accusantium
        odit fugit voluptatum aperiam rem aliquid libero cumque ex quidem
        asperiores.Lorem ipsum dolor sit amet consectetur adipisicing elit. Ut
        similique saepe doloribus illo distinctio voluptatum dicta nam et?
        Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque ex
        quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.Lorem ipsum dolor sit amet consectetur adipisicing
        elit. Ut similique saepe doloribus illo distinctio voluptatum dicta nam
        et? Accusantium odit fugit voluptatum aperiam rem aliquid libero cumque
        ex quidem asperiores.
      </s-tab> -->
    </s-tabs>
  </div>
</template>

<script>
import STabs from "../tab/STabs";
import STab from "../tab/STab";
import SAccordions from "../accordion/SAccordions";
import SAccordion from "../accordion/SAccordion";
import SDrawer from "../SDrawer";
import { Link } from "@inertiajs/inertia-vue";
import { mapGetters } from "vuex";

export default {
  name: "DasboardNavs",
  components: {
    STabs,
    STab,
    Link,
    SAccordions,
    SAccordion,
    SDrawer,
  },

  props: {
    openNotificationDrawer: { require: true, type: Function },
  },

  props: {
    openNotificationDrawer: {
      require: true,
    },
  },
  data() {
    return {};
  },

  computed: {
    ...mapGetters(["getNotifications"]),
  },

  created() {},

  mounted() {},

  methods: {
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

    openNotification(id = null) {
      this.openNotificationDrawer();
    },
  },

  openNotification() {
    this.openNotificationDrawer();
  },
};
</script>

<style lang="scss" scoped>
</style>
