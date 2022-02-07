import axios from 'axios'

const state = {
    notifications: []
}

const getters = {
    getNotifications: state => state.notifications
}

const actions = {
    async dispatchNotifications({ commit, state }, { type = "", payload }) {
        try {
            switch (type) {
            case "ADD_NOTIFICATION":
                commit("addNotification", payload)
                break;
            default:
                state.notifications = payload
                break;
        }
        } catch (error) {
            console.log(error)
        }
    }
}

const mutations = {
    addNotification: (state, notification) => {
        state.notifications.splice(
            0,
            0,
            notification
        )
    }
}

export default {
    namespaced: false,
    state,
    getters,
    actions,
    mutations
};