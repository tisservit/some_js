<template>
    <div class="block-process">
        <div class="main-grid-fade main-grid-fade-right main-grid-fade-left">
            <div class="main-grid-container">
                <div class="block-process-info">
                    <table class="block-process-table">
                        <tr>
                          <td class="block-process-table-head" style="width: 10%">{{ localize.TYPE_GOALS }}</td>
                          <td class="block-process-table-head" style="width: 20%">{{ localize.GOAL }}</td>
                            <td class="block-process-table-head" style="width: 20%">{{ localize.CRITERION }}</td>
                            <td class="block-process-table-head" style="width: 10%">{{ localize.WEIGHT }}</td>

                            <td class="block-process-table-head" style="width: 15%">Файл цели</td>
                            <td class="block-process-table-head" v-if="isLast" style="width: 15%">Файл оценки</td>
                            <td class="block-process-table-head" style="width: 20%">Зависит от</td>

                            <td class="block-process-table-head" style="width: 10%">{{ localize.PREV_PROGRESS }}</td>

                            <td v-if="percentSelectOptions" class="block-process-table-head" style="width: 10%; min-width: 10%">{{ localize.PROGRESS }}</td>
                            <td v-if="!percentSelectOptions" class="block-process-table-head" style="width: 333px; min-width: 333px">{{ localize.PROGRESS }}</td>

                            <td class="block-process-table-head" style="width: 10%" v-if="canViewEmployeeComment">{{ localize.COMMENT_EMPLOYEE }}</td>
                            <td class="block-process-table-head" style="width: 10%" v-if="canViewHeadComment">{{ localize.COMMENT_HEAD }}</td>
                            <td class="block-process-table-head" style="width: 10%" v-if="canViewFuncHeadComment">{{ localize.COMMENT_FUNC_HEAD }}</td>
                            <td class="block-process-table-head" style="width: 10%" v-if="canViewAdminComment">{{ localize.COMMENT_ADMIN }}</td>
                        </tr>
                        <tr v-for="(ratingItem, index) in rating" :key="'goal_' + index">
                            <td class="block-process-table-column"><span>{{ ratingItem.type_goal }}</span></td>
                            <td class="block-process-table-column" v-if="ratingItem.task.length > 0">
                                <p style="max-height: 100px; overflow-y: auto;">
                                    {{ ratingItem.task }}
                                </p>
                            </td>
                            <td class="block-process-table-column" v-else><span>{{ ratingItem.goal }}</span></td>
                            <td class="block-process-table-column">
                                <p style="max-height: 100px; overflow-y: auto;">
                                    {{ ratingItem.criterion }}
                                </p>
                            </td>
                            <td class="block-process-table-column"><span>{{ ratingItem.weight }}</span></td>

                            <td class="block-process-table-column">
                                <div v-if="ratingItem.file_goals">
                                    <a class="block-process-file-img" :title="ratingItem.file_goals.name" :href="ratingItem.file_goals.path" target="_blank"></a>
                                </div>
                            </td>
                            <td class="block-process-table-column" v-if="isLast">
                                <div v-if="ratingItem.file_rating">
                                    <a class="block-process-file-img" :title="ratingItem.file_rating.name" :href="ratingItem.file_rating.path" target="_blank"></a>
                                    <span class="block-process-file-delete" @click="deleteFile(index)" :key="'rating_file_delete_' + index">x</span>
                                </div>
                                <div v-else>
                                    <label :for="'rating_file_' + index" class="block-process-file-text">Прикрепить</label>
                                    <input :id="'rating_file_' + index" type="file" style="display: none;" @change="loadFile($event, index)" :key="'rating_file_' + index">
                                </div>
                            </td>
                            <td class="block-process-table-column">
                                <div :id="ratingItem.signature">
                                    <span class="ui-tile-selector-selector-wrap">
                                        <span data-role="tile-select" class="ui-tile-selector-select">Добавить</span>
                                    </span>
                                </div>
                            </td>

                            <td class="block-process-table-column"><span>{{ ratingItem.progressPrev }}</span></td>
                            <td class="block-process-table-column">

                                <span v-if="ratingItem.progress < 0 || ratingItem.progress > maxPercent" style="font-size: 11px;color: red;">Значение (от 0 до {{maxPercent}})</span>
                                <span v-if="ratingItem.progress < ratingItem.progressPrev" style="font-size: 11px;color: red;">{{ localize.INCORRECT_VALUE }}</span>

                                <div v-if="canSelectPercentOptions === true">

                                  <div class="block_process_table_switcher_container">

                                    <!-- select mode -->
                                    <select style="visibility: visible"
                                            class="block_process_table_switcher_container__select" v-bind:class="{
                                            'block-process-weight-value-error-select' : !ratingItem.progress || ratingItem.progress < 0
                                            || ratingItem.progress > maxPercent || ratingItem.progress < ratingItem.progressPrev }"
                                       v-model="ratingItem.progress">
                                        <option value="" selected>-</option>
                                      <option v-for="(percentOption, index) in percentSelectOptions" :key="'goal_' + index">{{percentSelectOptions[index]}}</option>
                                    </select>

                                    <!-- mode switcher -->
                                    <label class="switch">
                                        <input type="checkbox" data-active-mode="select" @change="changePercentInputType(index)">
                                        <span class="slider round"></span>
                                    </label>

                                    <!-- input mode -->
                                    <input style="visibility: hidden"
                                           class="block_process_table_switcher_container__input block-process-table-input" type="number" v-bind:class="{
                                            'block-process-weight-value-error' : !ratingItem.progress || ratingItem.progress < 0
                                            || ratingItem.progress > maxPercent || ratingItem.progress < ratingItem.progressPrev }"
                                       v-model="ratingItem.progress">

                                  </div>

                                </div>

                                <div v-if="canSelectPercentOptions !== true">

                                    <input class="block-process-table-input" type="number"
                                           v-bind:class="{
                                                'block-process-weight-value-error' : !ratingItem.progress || ratingItem.progress < 0
                                                || ratingItem.progress > maxPercent || ratingItem.progress < ratingItem.progressPrev }"
                                           v-model="ratingItem.progress">

                                </div>

                              <!-- EDIT THIS - START -->
<!--                              <span v-if="ratingItem.progress < 0 || ratingItem.progress > maxPercent" style="font-size: 11px;color: red;">Значение (от 0 до {{maxPercent}})</span>-->
<!--                              <span v-if="ratingItem.progress < ratingItem.progressPrev" style="font-size: 11px;color: red;">{{ localize.INCORRECT_VALUE }}</span>-->
<!--                                <input class="block-process-table-input" type="number"-->
<!--                                       v-bind:class="{-->
<!--                                            'block-process-weight-value-error' : !ratingItem.progress || ratingItem.progress < 0-->
<!--                                            || ratingItem.progress > maxPercent || ratingItem.progress < ratingItem.progressPrev }"-->
<!--                                       v-model="ratingItem.progress">-->
                              <!-- EDIT THIS - END -->

                            </td>
                            <td class="block-process-table-column" v-if="canViewEmployeeComment">
                            <textarea class="block-process-table-textarea"
                                      v-bind:class="{'block-process-weight-value-error' : ratingItem.commentEmployee.length === 0 }"
                                      v-bind:disabled="!isDraftStage"
                                      v-model="ratingItem.commentEmployee"></textarea>
                            </td>
                            <td class="block-process-table-column" v-if="canViewHeadComment">
                            <textarea class="block-process-table-textarea"
                                      v-bind:class="{'block-process-weight-value-error' : (parseInt(ratingItem.progress) !== parseInt(ratingItem.progressBeforeUpdate)) && ratingItem.commentHead.length === 0 && isAgreementHead }"
                                      v-bind:disabled="!isAgreementHead"
                                      v-model="ratingItem.commentHead"></textarea>
                            </td>
                            <td class="block-process-table-column" v-if="canViewFuncHeadComment">
                            <textarea class="block-process-table-textarea"
                                      v-bind:class="{'block-process-weight-value-error' : (parseInt(ratingItem.progress) !== parseInt(ratingItem.progressBeforeUpdate)) && ratingItem.commentFuncHead.length === 0 && isAgreementFuncHead }"
                                      v-bind:disabled="!isAgreementFuncHead"
                                      v-model="ratingItem.commentFuncHead"></textarea>
                            </td>
                            <td class="block-process-table-column" v-if="canViewAdminComment">
                            <textarea class="block-process-table-textarea"
                                      v-model="ratingItem.commentAdmin"></textarea>
                            </td>
                        </tr>
                        <tr style="border-top: 1px solid #E7E7E7; border-bottom: 1px solid #E7E7E7;">
                            <td colspan="6" class="block-process-weight-title">{{ localize.TOTAL }}</td>
                            <td class="block-process-weight-value" style="text-align: center;">{{ totalProgressOld }}</td>
                            <td class="block-process-weight-value">{{ totalProgress }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "RatingTable",
        computed: {
            ...mapState([
                'rating',
                'cardId',
                'isAdmin',
                'isDraftStage',
                'isAgreementHead',
                'isAgreementFuncHead',
                'isAgreementAdmin',
                'progressValue',
                'maxPercent',
                'isLast',
                'canSelectPercentOptions',
                'percentSelectOptions',
            ]),
            localize()
            {
                return Object.freeze({
                    'TYPE_GOALS': BX.message("TYPE_GOALS"),
                    'GOAL': BX.message("GOAL"),
                    'CRITERION': BX.message("CRITERION"),
                    'WEIGHT': BX.message("WEIGHT"),
                    'PREV_PROGRESS': BX.message("PREV_PROGRESS"),
                    'PROGRESS': BX.message("PROGRESS"),
                    'COMMENT_EMPLOYEE': BX.message("COMMENT_EMPLOYEE"),
                    'COMMENT_HEAD': BX.message("COMMENT_HEAD"),
                    'COMMENT_FUNC_HEAD': BX.message("COMMENT_FUNC_HEAD"),
                    'COMMENT_ADMIN': BX.message("COMMENT_ADMIN"),
                    'TOTAL': BX.message("TOTAL"),
                    'INCORRECT_VALUE': BX.message("INCORRECT_VALUE"),
                    'SET_INPUT_PERCENT_MODE': BX.message("SET_INPUT_PERCENT_MODE"),
                })
            },
            totalProgress() {
                let total = 0;
                this.rating.forEach(goal => {
                    let progress = (parseInt(goal.progress) && !isNaN(parseInt(goal.progress))) ? parseInt(goal.progress) : 0;
                    let weight = (parseInt(goal.weight) || !isNaN(parseInt(goal.weight))) ? parseInt(goal.weight) : 0;
                    //Target 1 weight 80, 25% completed (80/100*25=20)
                    total += weight / 100 * progress;
                });
                return total.toFixed(2);
            },
            totalProgressOld() {
                let total = 0;
                this.rating.forEach(goal => {
                    total += parseInt(goal.progressPrev) ? parseInt(goal.progressPrev) * (parseInt(goal.weight) / 100) : 0;
                });
                return total.toFixed(2);
            },
            canViewEmployeeComment() {
                if(this.isDraftStage)
                    return true;
                let canView = false;
                this.rating.forEach(goal => {
                    if(goal.commentEmployee.length > 0)
                        canView = true;
                });
                return canView;
            },
            canViewHeadComment() {
                let canView = false;
                if(this.isAgreementHead) {
                    return true;
                }
                this.rating.forEach(goal => {
                    if(goal.commentHead.length > 0)
                        canView = true;
                });
                return canView;
            },
            canViewFuncHeadComment() {
                let canView = false;
                if(this.isAgreementFuncHead) {
                    return true;
                }
                this.rating.forEach(goal => {
                    if(goal.commentFuncHead.length > 0)
                        canView = true;
                });
                return canView;
            },
            canViewAdminComment() {
                let canView = false;
                if(this.isAgreementAdmin) {
                    return true;
                }
                this.rating.forEach(goal => {
                    if(goal.commentAdmin.length > 0)
                        canView = true;
                });
                return canView && this.isAdmin;
            }
        },
        mounted() {
            setTimeout( this.initSelector, 100)
        },
        methods: {
            initSelector() {
                this.rating.forEach( ratingItem => {
                        this.getSelector(ratingItem.signature, ratingItem.depend);
                    }
                );
            },
            getSelector(id, depend = []) {
                BX.ajax({
                    url: templateFolder + '/select.php',
                    data: {
                        id: id,
                        depend: depend
                    },
                    dataType: 'html',
                    method: 'POST',
                    timeout: 30,
                    async: false,
                    processData: true,
                    scriptsRunFirst: false,
                    cache: false,
                    onsuccess: data => {
                        // console.log(data);
                        BX(id).innerHTML = data;
                    },
                    onfailure: error => {
                        console.log(error);
                    }
                });
            },
            loadFile(event, index) {

                var obFormData = new FormData();

                if (event.target.files && event.target.files[0]) {
                    let file = event.target.files[0];
                    obFormData.append("file", file, file.name);

                    BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.rating', 'saveFile', {
                        mode:'class',
                        dataType: 'json',
                        data: obFormData
                    })
                        .then( response => {
                            this.rating[index]['file_rating'] = {
                                id: response.data.id,
                                name: file.name,
                                path: response.data.path
                            }
                        })
                        .catch( error => {
                            console.log(error);
                        });
                }
            },
            deleteFile(index) {
                BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.rating', 'deleteFile', {
                    mode:'class',
                    dataType: 'json',
                    data: {
                        id: this.rating[index]['file_rating']
                    }
                })
                .then( () => {
                    this.rating[index]['file_rating'] = '';
                })
                .catch( error => {
                    console.log(error);
                });
            },
            changePercentInputType(index) {
              event.preventDefault()
              let selectNode = event.target.parentElement.previousSibling
              let inputNode = event.target.parentElement.nextSibling

              if (event.target.dataset.activeMode === 'input') {
                  inputNode.style.visibility = 'hidden'
                  selectNode.style.visibility = 'visible'
                  event.target.dataset.activeMode = 'select'
              } else {
                  inputNode.style.visibility = 'visible'
                  selectNode.style.visibility = 'hidden'
                  event.target.dataset.activeMode = 'input'
              }
              this.rating[index].progress = 0
            },
            saveRating() {
                event.preventDefault();

                let slider = window.top.BX.SidePanel.Instance.getTopSlider();
                slider.showLoader();

                window.top.BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'saveGoals', {
                    mode:'class',
                    data: {
                        params: {
                            cardId: this.cardId,
                            goals: this.goals
                        }
                    }
                })
                .then( () => {
                    slider.reload();
                    let id = window.top.BX.Main.gridManager.data[0].id;
                    window.top.BX.Main.gridManager.getById(id).instance
                        .reloadTable('POST', { apply_filter: 'Y', clear_nav: 'Y' });
                })
                .catch( () => {
                     slider.closeLoader();
                });

            }
        },
        watch: {
            totalProgress: function () {
                if(parseFloat(this.totalProgress) > parseFloat(this.totalProgressOld))
                    this.$store.commit('setProgressValue', parseFloat(this.totalProgress));
                else
                    this.$store.commit('setProgressValue', parseFloat(this.totalProgressOld));

            },
            totalProgressOld: function () {
                if(parseFloat(this.totalProgress) > parseFloat(this.totalProgressOld))
                    this.$store.commit('setProgressValue', parseFloat(this.totalProgress));
                else
                    this.$store.commit('setProgressValue', parseFloat(this.totalProgressOld));
            }
        }
    }
</script>