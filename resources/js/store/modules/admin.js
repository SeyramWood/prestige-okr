import axios from "axios";

const state = {
    people: [],
    teams: [],
    myTeams: {},
    teamOjectives: {},
    authProfile: {},
    company: {},
    companies: []
};

const getters = {
    getAuthProfile: state => state.authProfile,
    getPeople: state => state.people,
    getTeams: state => state.teams,
    getMyTeams: state => state.myTeams,
    getTeamOjectives: state => state.teamOjectives,
    getCompany: state => state.company,
    getCompanies: state => state.companies
};

const actions = {
    async dispatchUser({ commit, state }, { type = "", payload }) {
        try {
            switch (type) {
                case "ADD_PEOPLE":
                    state.people = payload;
                    break;
                case "UPDATE_USER_ROLE":
                    commit("updateUserRole", payload);
                    break;
                case "DELETE_PERSON":
                    commit("deletePerson", payload);
                    break;
                case "DELETE_PEOPLE":
                    commit("deletePeople", payload);
                    break;
                default:
                    state.authProfile = payload;
                    break;
            }
        } catch (err) {
            console.error(err);
        }
    },
    async dispatchTeam({ commit, state }, { type = "", payload }) {
        try {
            switch (type) {
                case "ADD_TEAM":
                    state.teams = [payload, ...state.teams];
                    break;
                case "ADD_MY_TEAMS":
                    state.myTeams = payload;
                    break;
                case "ADD_TEAM_OBJAECTIVES":
                    state.teamOjectives = payload;
                    break;
                case "UPDATE_TEAM":
                    commit("updateTeam", payload);
                    break;
                case "DELETE_TEAM":
                    commit("deleteTeam", payload);
                    break;
                case "DELETE_TEAMS":
                    commit("deleteTeams", payload);
                    break;
                default:
                    state.teams = payload;
                    break;
            }
        } catch (err) {
            console.error(err);
        }
    },
    async dispatchCompany({ commit, state }, { type = "", payload }) {
        try {
            switch (type) {
                case "ADD_COMPANIES":
                    state.companies = payload;
                    break;
                default:
                    state.company = payload;
                    break;
            }
        } catch (err) {
            console.error(err);
        }
    }
};

const mutations = {
    updateUserRole: (state, user) => {
        state.people.splice(
            state.people.findIndex(p => p.id === user.id),
            1,
            user
        );
    },
    updateTeam: (state, team) => {
        state.teams.splice(
            state.teams.findIndex(t => t.id === team.id),
            1,
            team
        );
    },
    deleteTeam: (state, id) => {
        state.teams.splice(
            state.teams.findIndex(t => t.id === id),
            1
        );
    },
    deleteTeams: (state, ids) => {
        ids.forEach(id => {
            state.teams.splice(
                state.teams.findIndex(t => t.id === id),
                1
            );
        });
    },
    deletePerson: (state, id) => {
        state.people.splice(
            state.people.findIndex(t => t.id === id),
            1
        );
    },
    deletePeople: (state, ids) => {
        ids.forEach(id => {
            state.people.splice(
                state.people.findIndex(t => t.id === id),
                1
            );
        });
    }
};

export default {
    namespaced: false,
    state,
    getters,
    actions,
    mutations
};
