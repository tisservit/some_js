import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
const startParams = window.jsParams;

let ratings = [];
if(startParams.ITEM.RATING.length) {
    startParams.ITEM.RATING.forEach(rating => {
        rating.signature = Math.random().toString(36).substring(7);
        ratings.push(rating);
    });
}

export default new Vuex.Store({
    state : {
        rating: ratings,
        prevRating: startParams.ITEM.PREV_RATING,
        processInfo: startParams.ITEM.PROCESS_INFO,
        userInfo: startParams.ITEM.USER_INFO,
        statusGoals: startParams.ITEM.STATUS_GOALS,
        activeStage: startParams.ITEM.ACTIVE_STAGE,
        cardId: startParams.ITEM.ID,
        isDraftStage: startParams.ITEM.IS_DRAFT_STAGE,
        isAgreementHead: startParams.ITEM.IS_AGREEMENT_HEAD,
        isAgreementFuncHead: startParams.ITEM.IS_AGREEMENT_FUNC_HEAD,
        isAgreementDirector: startParams.ITEM.IS_AGREEMENT_DIRECTOR,
        isAgreementAdmin: startParams.ITEM.IS_AGREEMENT_ADMIN,
        isCompleteStage: startParams.ITEM.IS_COMPLETE_STAGE,
        logs: startParams.ITEM.LOGS,
        canEdit: startParams.ITEM.CAN_EDIT,
        isAdmin: startParams.ITEM.IS_ADMIN,
        progressValue: parseFloat(startParams.ITEM.PROGRESS_VALUE),
        progressTime: parseFloat(startParams.ITEM.PROGRESS_TIME),
        maxPercent: parseInt(startParams.ITEM.MAX_PERCENT),
        isLast: startParams.ITEM.IS_LAST,
        canSelectPercentOptions: startParams.ITEM.ADDITIONAL_PARAMETERS.CAN_SELECT_PERCENT_OPTION,
        percentSelectOptions: startParams.ITEM.ADDITIONAL_PARAMETERS.PERCENT_SELECT_OPTIONS,
    },
    mutations: {
        setProgressValue(state, payload) {
            state.progressValue = payload
        }
    }
});