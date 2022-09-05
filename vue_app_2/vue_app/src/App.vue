<template>
    <div id="app">
        <form method="post" name="sliderForm" class="slider-form">
            <input type="hidden" class="slider-field" value="" name="ID" />

            <head-title />
            <head-stages />

            <div class="form-tabs">
                <span class="form-tabs-span" v-bind:class="{'form-tabs-span-active': activeTab === 'goals'}" @click="activeTab = 'goals'">{{ localize.RATING }}</span>
                <span class="form-tabs-span" v-bind:class="{'form-tabs-span-active': activeTab === 'history'}" @click="activeTab = 'history'">{{ localize.HISTORY }}</span>
            </div>

            <div class="form-container" v-if="activeTab === 'goals'">
                <head-info />
                <rating-table />
                <history />
            </div>

            <div class="form-container" v-if="activeTab === 'history'">
                <logs />
            </div>


            <template v-if="canEdit">
                <div class="button-line" v-if="isDraftStage">
                    <button type="button" class="btn-submit" @click="sendAgreement()" :disabled="!canSubmit"
                            v-bind:class="{'btn-submit-cancel': !canSubmit}">{{ localize.SEND_AGREE }}</button>
                    <button type="button" class="btn-cancel" @click="closeSlider()">{{ localize.DECLINE }}</button>
                </div>
                <div class="button-line" v-else-if="isAgreementAdmin">
                    <button type="button" class="btn-submit" @click="sendAgreement()">{{ localize.APPROVE }}</button>
                    <button  type="button" class="btn-return" @click="openModalComment()">{{ localize.NOT_APPROVE }}</button>
                    <button type="button" class="btn-cancel" @click="closeSlider()">{{ localize.DECLINE }}</button>
                </div>
                <div class="button-line" v-else>
                    <button type="button" class="btn-submit" @click="sendAgreement()" :disabled="progressChange"
                            v-bind:class="{'btn-submit-cancel': progressChange}">{{ localize.AGREE }}</button>
                    <button  type="button" class="btn-return" @click="openModalComment()">{{ localize.NOT_AGREE }}</button>
                    <button type="button" class="btn-cancel" @click="closeSlider()">{{ localize.DECLINE }}</button>
                </div>
                <div id="commentPopup" style="display: none">
                    <select class="block-process-table-select" style="margin-bottom: 15px;" v-model="stageToChange" v-if="isAdmin">
                        <option value="" selected disabled>{{ localize.SELECT_STAGE }}</option>
                        <option v-for="stage in statusGoals" :value="stage.id" v-if="stage.sort < activeSort">{{ stage.value }}</option>
                    </select>
                    <textarea rows="5" class="comment-area" v-model="comment" :placeholder="localize.COMMENT"></textarea>
                    <button type="button" class="btn-submit" @click="sendDecline()">{{ localize.SEND }}</button>
                </div>
            </template>
        </form>

    </div>
</template>

<script>

import HeadTitle from "./components/HeadTitle";
import HeadStages from "./components/HeadStages";
import HeadInfo from "./components/HeadInfo";
import RatingTable from "./components/RatingTable";
import History from "./components/History";
import Logs from "./components/Logs";
import {mapState} from "vuex";

export default {
    name: 'App',
    components: {
        History,
        HeadTitle,
        HeadStages,
        HeadInfo,
        RatingTable,
        Logs
    },
    data() {
        return {
            comment: '',
            stageToChange: "",
            activeTab: 'goals'
        }
    },
    computed: {
        ...mapState([
            'cardId',
            'rating',
            'canEdit',
            'statusGoals',
            'isDraftStage',
            'isCompleteStage',
            'activeStage',
            'isDraftStage',
            'isAgreementHead',
            'isAgreementAdmin',
            'maxPercent',
            'isAdmin'
        ]),
        localize()
        {
            return Object.freeze({
                'RATING': BX.message("RATING"),
                'HISTORY': BX.message("HISTORY"),
                'SEND_AGREE': BX.message("SEND_AGREE"),
                'DECLINE': BX.message("DECLINE"),
                'APPROVE': BX.message("APPROVE"),
                'NOT_APPROVE': BX.message("NOT_APPROVE"),
                'AGREE': BX.message("AGREE"),
                'NOT_AGREE': BX.message("NOT_AGREE"),
                'COMMENT': BX.message("COMMENT"),
                'SEND': BX.message("SEND"),
                'SELECT_STAGE': BX.message("SELECT_STAGE")
            })
        },
        activeSort() {
            let stages = Object.values(this.statusGoals)
            for(let i = 0; i < stages.length; i++) {
                if(stages[i].id === this.activeStage)
                    return stages[i].sort;
            }
        },
        canSubmit() {
            let canSubmit = true;
            this.rating.forEach( ratingItem => {
                if(!ratingItem.progress || (this.isDraftStage && ratingItem.commentEmployee.length === 0) || ratingItem.progress < 0
                    || ratingItem.progress > this.maxPercent || ratingItem.progress < ratingItem.progressPrev)
                    canSubmit = false;
            })
            return canSubmit;
        },
        progressChange() {
            let change = false;
            this.rating.forEach(ratingItem => {
                if((parseInt(ratingItem.progress) !== parseInt(ratingItem.progressBeforeUpdate)) &&
                  ((this.isAgreementHead && ratingItem.commentHead.length === 0)
                    || (this.isAgreementDirector && ratingItem.commentDirector.length === 0)) || ratingItem.progress < 0 || ratingItem.progress > this.maxPercent)
                    change = true;
            });
            return change;
        },
    },
    methods: {
        sendAgreement() {
            let slider = window.BX.SidePanel.Instance.getTopSlider();
            slider.showLoader();
            window.top.BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.rating', 'nextStatus', {
                mode:'class',
                data: {
                    params: {
                        cardId: this.cardId,
                        rating: this.rating,
                        activeStage: this.activeStage
                    }
                }
            })
            .then( () => {
                window.top.BX.SidePanel.Instance.close();
                let id = window.top.BX.Main.gridManager.data[0].id;
                window.top.BX.Main.gridManager.getById(id).instance
                    .reloadTable('POST', { apply_filter: 'Y', clear_nav: 'Y' });
            })
            .catch( () => {
                slider.closeLoader();
                let id = window.top.BX.Main.gridManager.data[0].id;
                window.top.BX.Main.gridManager.getById(id).instance
                    .reloadTable('POST', { apply_filter: 'Y', clear_nav: 'Y' });
            });
        },
        openModalComment() {
            var oPopup = new window.BX.PopupWindow('call-decline', window.body, {
                closeIcon: {right: "20px", top: "10px"},
                titleBar: { content: window.BX.create("span", {html: BX.message('SET_COMMENT'), 'props': {'className': 'call-decline-title'}})},
                autoHide : true,
                zIndex: 5000,
                offsetTop : 1,
                offsetLeft : 0,
                lightShadow : true,
                closeByEsc : true,
                overlay: {
                    backgroundColor: 'grey', opacity: '80'
                }
            });
            oPopup.setContent(window.BX('commentPopup'));
            oPopup.show();
        },
        closeSlider() {
            window.top.BX.SidePanel.Instance.close();
        },
        sendDecline() {
            let slider = window.BX.SidePanel.Instance.getTopSlider();
            slider.showLoader();
            window.top.BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.rating', 'prevStatus', {
                mode:'class',
                data: {
                    params: {
                        cardId: this.cardId,
                        rating: this.rating,
                        activeStage: this.activeStage,
                        stageToChange: this.stageToChange,
                        comment: this.comment
                    }
                }
            })
            .then( () => {
                window.top.BX.SidePanel.Instance.close();
                let id = window.top.BX.Main.gridManager.data[0].id;
                window.top.BX.Main.gridManager.getById(id).instance
                    .reloadTable('POST', { apply_filter: 'Y', clear_nav: 'Y' });
            })
            .catch( () => {
                slider.closeLoader();
                window.top.BX.SidePanel.Instance.close();
                let id = window.top.BX.Main.gridManager.data[0].id;
                window.top.BX.Main.gridManager.getById(id).instance
                    .reloadTable('POST', { apply_filter: 'Y', clear_nav: 'Y' });
            });
        }
    }
}
</script>