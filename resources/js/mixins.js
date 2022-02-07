const revealPassword = {
    data() {
        return {
            reveal: false
        };
    },
    methods: {
        revealPassword() {
            switch (this.$refs.revealPassword.type) {
                case "password":
                    this.$refs.revealPassword.type = "text";
                    this.reveal = true;
                    break;
                default:
                    this.$refs.revealPassword.type = "password";
                    this.reveal = false;
                    break;
            }
        }
    }
};
const dateTime = {
    computed: {
        objectivePeriods() {
            return [
                {
                    text: `Month ${this.currentDate.getUTCFullYear()} (${this.formatDateMonth(
                        this.getDate(
                            this.currentDate.getUTCMonth(),
                            this.currentDate.getUTCDate(),
                            this.currentDate.getUTCFullYear()
                        )
                    )} - ${this.formatDateMonth(
                        this.getDate(
                            this.currentDate.getUTCMonth() + 1,
                            0,
                            this.currentDate.getUTCFullYear()
                        )
                    )})`,
                    value: "month"
                },

                {
                    text: `Q1 ${this.currentDate.getUTCFullYear()} (Jan 01 - Mar 31)`,
                    value: "Q1"
                },
                {
                    text: `Q2 ${this.currentDate.getUTCFullYear()} (Apr 01 - Jun 30)`,
                    value: "Q2"
                },
                {
                    text: `Q3 ${this.currentDate.getUTCFullYear()} (Jul 01 - Sep 30)`,
                    value: "Q3"
                },
                {
                    text: `Q4 ${this.currentDate.getUTCFullYear()} (Oct 01 - Dec 31)`,
                    value: "Q4"
                },
                {
                    text: `1st half ${this.currentDate.getUTCFullYear()} (Jan 01 - Jun 30)`,
                    value: "firstHalf"
                },
                {
                    text: `2nd half ${this.currentDate.getUTCFullYear()} (Jul 01 - Dec 31)`,
                    value: "secondHalf"
                },
                {
                    text: `Annual ${this.currentDate.getUTCFullYear()} (Jan 01 - Dec 31)`,
                    value: "annual"
                }
            ];
        }
    },
    data() {
        return {
            months: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec"
            ],
            currentDate: new Date()
        };
    },
    methods: {
        getDate(m, d, y = null) {
            if (y) {
                return new Date(y, m, d).toISOString();
            }
            return new Date(
                `${m}-${d}-${new Date().getUTCFullYear()}`
            ).toISOString();
        },
        formatDateMonth(str) {
            const date = this.$luxon.fromISO(str).toFormat("MMM dd");
            if (date === "Invalid DateTime") return "";
            return date;
        },
        formatDateYear(str) {
            const date = this.$luxon.fromISO(str).toFormat("y");
            if (date === "Invalid DateTime") return "";
            return date;
        },
        formatDate(str) {
            const date = this.$luxon.fromISO(str).toFormat("MMMM dd, y");
            if (date === "Invalid DateTime") return "";
            return date;
        },
        formatDate2(str) {
            const date = this.$luxon.fromISO(str).toFormat("MMM dd, y");
            if (date === "Invalid DateTime") return "";
            return date;
        },
        formatTime(str) {
            const time = this.$luxon.fromISO(str).toFormat("hh:mm a");
            if (time === "Invalid DateTime") return "";
            return time;
        },
        formatDateTime(str) {
            const date = this.formatDate2(str);
            const time = this.formatTime(str);
            if (date === "Invalid DateTime" || time === "Invalid DateTime")
                return "";
            return `${date} ${time}`;
        },
        formatDurationInHours(start, end) {
            const duration = Interval.fromDateTimes(
                this.$luxon.fromJSDate(start),
                this.$luxon.fromJSDate(end)
            )
                .toDuration(["hours", "minutes", "seconds"])
                .toObject();
            if (Object.keys(duration).length === 0) return "";
            return `${
                duration.hours !== 0
                    ? duration.hours === 1
                        ? duration.hours + " hour"
                        : duration.hours + " hours"
                    : ""
            } ${
                duration.minutes !== 0
                    ? duration.minutes === 1
                        ? duration.minutes + " minute"
                        : duration.minutes + " minutes"
                    : ""
            }`;
        }
    }
};

export { revealPassword, dateTime };
