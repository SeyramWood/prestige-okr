import axios from "axios";

const state = {
    userObjectives: []
};

const getters = {
    getUserObjectives: state => state.userObjectives
};

const actions = {
    async dispatchObjective({ commit, state }, { type = "", payload }) {
        try {
            switch (type) {
                case "ADD_NEW_OBJECTIVE":
                    commit("addNewUserOjective", payload);
                    break;
                case "UPDATE_OBJECTIVE":
                    commit("updateUserOjective", payload);
                    break;
                case "DELETE_OBJECTIVE":
                    commit("deleteUserOjective", payload);
                    break;
                case "ADD_NEW_KEY_RESULT":
                    commit("addNewKeyResult", payload);
                    break;
                case "UPDATE_KEY_RESULT":
                    commit("updateKeyResult", payload);
                    break;
                case "DELETE_KEY_RESULT":
                    commit("deleteKeyResult", payload);
                    break;
                case "UPDATE_PROGRESS":
                    commit("updateProgress", payload);
                    break;
                default:
                    commit("addUserOjectives", payload);
                    break;
            }
        } catch (err) {
            console.error(err);
        }
    }
};

const mutations = {
    addUserOjectives: (state, data) => {
        state.userObjectives = data.map(obj => {
            obj.period = JSON.parse(obj.period);
            obj.progress = JSON.parse(obj.progress);
            obj.key_results = obj.key_results.map(kr => {
                kr.period = JSON.parse(kr.period);
                kr.progress = JSON.parse(kr.progress);
                return kr;
            });
            return obj;
        });
    },
    addNewUserOjective: (state, data) => {
        state.userObjectives = [
            {
                ...data,
                period: JSON.parse(data.period),
                progress: JSON.parse(data.progress)
            },
            ...state.userObjectives
        ];
    },
    updateUserOjective: (state, data) => {
        data.key_results = data.key_results.map(kr => {
            kr.period = JSON.parse(kr.period);
            kr.progress = JSON.parse(kr.progress);
            return kr;
        });
        state.userObjectives.splice(
            state.userObjectives.findIndex(obj => obj.id === data.id),
            1,
            {
                ...data,
                period: JSON.parse(data.period),
                progress: JSON.parse(data.progress)
            }
        );
    },
    deleteUserOjective: (state, id) => {
        state.userObjectives.splice(
            state.userObjectives.findIndex(obj => obj.id === id),
            1
        );
    },
    addNewKeyResult: (state, data) => {
        state.userObjectives = state.userObjectives.map(obj => {
            if (obj.id === data.objective_id) {
                obj.metric.target += data.kr_metric.target;
                obj.key_results = [
                    ...obj.key_results,
                    {
                        ...data,
                        period: JSON.parse(data.period),
                        progress: JSON.parse(data.progress)
                    }
                ];
            }
            return obj;
        });
    },
    updateKeyResult: (state, data) => {
        state.userObjectives = state.userObjectives.map(obj => {
            if (obj.id === data.objective_id) {
                obj.key_results.splice(
                    obj.key_results.findIndex(kr => kr.id === data.id),
                    1,
                    {
                        ...data,
                        period: JSON.parse(data.period),
                        progress: JSON.parse(data.progress)
                    }
                );
            }
            return obj;
        });
    },
    deleteKeyResult: (state, data) => {
        state.userObjectives.forEach(obj => {
            if (obj.id === data.objId) {
                obj.key_results.splice(
                    obj.key_results.findIndex(kr => kr.id === data.id),
                    1
                );
                const progressTotal = obj.key_results.reduce(
                    (total, progress) => {
                        return (total += progress.kr_metric.progress);
                    },
                    0
                );
                const targetTotal = obj.key_results.reduce((total, target) => {
                    return (total += target.kr_metric.target);
                }, 0);

                const subTotal = ((progressTotal / targetTotal) * 100).toFixed(
                    2
                );
                obj.metric.target = targetTotal;
                if (subTotal.toString().includes(".00")) {
                    obj.metric.progress = parseInt(
                        subTotal.toString().split(".")[0]
                    );
                } else {
                    obj.metric.progress = parseFloat(subTotal);
                }
            }
        });
    },
    updateProgress(state, data) {
        state.userObjectives.forEach(obj => {
            if (obj.id === data.objId) {
                let pro =
                    obj.key_results[data.krIndex].kr_metric.start === data.val
                        ? 0
                        : data.val || 0;
                obj.key_results[data.krIndex].kr_metric.progress = pro;
                const krTotal = obj.key_results.reduce((total, progress) => {
                    return (total += progress.kr_metric.progress);
                }, 0);
                const subTotal = ((krTotal / obj.metric.target) * 100).toFixed(
                    2
                );
                if (subTotal.toString().includes(".00")) {
                    obj.metric.progress = parseInt(
                        subTotal.toString().split(".")[0]
                    );
                } else {
                    obj.metric.progress = parseFloat(subTotal);
                }
            }
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
