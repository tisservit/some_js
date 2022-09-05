import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
const startParams = window.jsParams;

let goals = [];
if(startParams.ITEM.GOALS.length) {
    startParams.ITEM.GOALS.forEach(goal => {
        goal.signature = Math.random().toString(36).substring(7);
        goal.canCascade = 'N';
        goals.push(goal);
    });
}

export default new Vuex.Store({
    state : {
        maxCountGoals: startParams.ITEM.MAX_COUNT_GOALS,
        goals: goals,
        selectorId: startParams.ITEM.SELECTOR_ID,
        processInfo: startParams.ITEM.PROCESS_INFO,
        userInfo: startParams.ITEM.USER_INFO,
        delegateInfo: startParams.ITEM.DELEGATE_INFO,
        typeGoals: startParams.ITEM.TYPE_GOALS,
        listGoals: startParams.ITEM.LIST_GOALS,
        statusGoals: startParams.ITEM.STATUS_GOALS,
        activeStage: startParams.ITEM.ACTIVE_STAGE,
        cardId: startParams.ITEM.ID,
        isDraftStage: startParams.ITEM.IS_DRAFT_STAGE,
        isApprovalStage: startParams.ITEM.IS_APPROVAL_STAGE,
        isCompleteStage: startParams.ITEM.IS_COMPLETE_STAGE,
        logs: startParams.ITEM.LOGS,
        canEdit: startParams.ITEM.CAN_EDIT,
        canEditDelegate: startParams.ITEM.CAN_EDIT_DELEGATE,
        isAdmin: startParams.ITEM.IS_ADMIN,
        minWeight: parseInt(startParams.ITEM.MIN_WEIGHT),
        userProjects: startParams.ITEM.AVAILABLE_PROJECTS,
        userTasks: startParams.ITEM.AVAILABLE_TASKS,
        userSubordinateEmployees: startParams.ITEM.SUBORDINATE_EMPLOYEES,
        userSubordinateBosses: startParams.ITEM.SUBORDINATE_BOSSES,
        usersAvatars: startParams.ITEM.USERS_AVATARS,
        cascadeAccess: startParams.ITEM.CASCADE_ACCESS,
    }
});