<template>
  <div class="block-process" style="margin-top: 10px; margin-bottom: 80px;">
    <div class="block-process-info">
      <table class="block-process-table">

        <tr>
          <td class="block-process-table-head" style="width: 10%">{{ localize.TYPE_GOALS }}</td>
          <td class="block-process-table-head" style="width: 20%">{{ localize.GOAL }}</td>
          <td class="block-process-table-head" style="width: 20%">{{ localize.CRITERION }}</td>
          <td class="block-process-table-head" style="width: 10%">{{ localize.WEIGHT }}</td>
          <td class="block-process-table-head" style="width: 10%">{{ localize.GOAL_FILE}}</td>
          <td class="block-process-table-head" style="width: 20%">{{ localize.CASCADE }}</td>
          <td class="block-process-table-head" style="width: 20%">{{ localize.DEPENDS_ON }}</td>
          <td class="block-process-table-head" style="width: 30%; min-width: 200px;">{{
              localize.LINK_TASK_PROJECT
            }}
          </td>
          <td class="block-process-table-head" style="width: 10%" v-if="canEdit">{{ localize.DELETE }}</td>
        </tr>

        <tr v-for="(goal, index) in goals" :key="'goal_' + index">

          <td class="block-process-table-column">
            <select :title="goal.type_title" class="block-process-table-select" :disabled="goal.disableFields === 'Y'"
                    v-bind:class="{'block-process-criterion-value-error' : goal.type_goal.length === 0}"
                    v-model="goal.type_goal" @change="clearGoalTask(index)">
              <option value="" selected disabled>{{ localize.SELECT_TYPE_GOAL }}</option>
              <option v-for="(typeGoal, index) in typeGoals" :value="typeGoal.id" :key="'type_goal_' + index">
                {{ typeGoal.value }}
              </option>
            </select>
          </td>
          <td class="block-process-table-column">
            <textarea class="block-process-table-textarea" v-model="goal.task"
                      v-bind:class="{'block-process-criterion-value-error' : goal.task.length === 0}"
                      v-if="goal.type_goal && typeGoals[goal.type_goal]['xml_id'] === 'smart'"></textarea>
            <select :title="goal.goal_title" class="block-process-table-select" :disabled="goal.disableFields === 'Y'"
                    v-bind:class="{'block-process-criterion-value-error' : goal.goal.length === 0}"
                    v-model="goal.goal" @change="checkGoalWeight(index)" v-else>
              <option value="" selected disabled>{{ localize.SELECT_GOAL }}</option>
              <option v-for="(goalListItem, index) in listGoals[goal.type_goal]" :value="index"
                      :key="'goal_user_' + index">{{ goalListItem.NAME }}
              </option>
            </select>
          </td>
          <td class="block-process-table-column">
                        <textarea class="block-process-table-textarea" :disabled="goal.disableFields === 'Y'"
                                  v-bind:class="{'block-process-criterion-value-error' : goal.criterion.length === 0}"
                                  v-model="goal.criterion"></textarea>
          </td>
          <td class="block-process-table-column">
            <span v-if="goal.weight < minWeight || goal.weight > 100" style="font-size: 11px;color: red;">{{localize.VALUE_FROM}}{{
                minWeight
              }}{{localize.VALUE_TO}}</span>
            <input class="block-process-table-input" type="number"
                   v-bind:class="{'block-process-weight-value-error' : goal.weight < minWeight || goal.weight > 100}"
                   v-model="goal.weight"
                   :min="goal.categoryWeight"
                   :disabled="goal.disabled_input_number === true"
                   @input="checkMinGoalWeight($event, index)">
          </td>

          <!-- file -->
          <td class="block-process-table-column">
            <div v-if="goal.file">
              <a class="block-process-file-img" :title="goal.file.name" :href="goal.file.path" target="_blank"></a>
              <span class="block-process-file-delete" @click="deleteFile(index)" v-if="goal.disableFields === 'N'"
                    :key="'goal_file_delete_' + index">x</span>
            </div>
            <div v-else>
              <label :for="'goal_file_' + index" class="block-process-file-text">{{localize.ATTACH}}</label>
              <input :id="'goal_file_' + index" type="file" style="display: none;" @change="loadFile($event, index)"
                     :key="'goal_file_' + index">
            </div>
          </td>

          <!-- cascade -->
          <td class="block-process-table-column">

            <div v-if="goal.canCascade === 'Y'">

              <div class="block-process-users-block"
                   v-bind:style="[
                       userInfo.HAVE_SUBORDINATE_BOSSES === 'Y' &&
                       userInfo.HAVE_SUBORDINATE_EMPLOYEES === 'Y' &&
                       userInfo.IS_BOSS === 'Y'
                       ?
                       {
                          'top' : '30px'
                       }
                       :
                       {
                          'top' : '0px'
                       }
              ]">
                <div class="faces-base">

                  <!-- Если есть источник каскадирования - и это НЕ оцениваемый сотрудник -->
                  <div v-if="userInfo.ID !== goal.cascadeSource && (goal.cascadeSource !== '0' && goal.cascadeSource !== null)">
                    <a class="bp-short-process-step bp-short-process-step-firs"
                       v-bind:href="'/company/personal/user/' + goal.cascadeSource + '/'">
                      <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                        <i v-if="usersAvatars[goal.cascadeSource]"
                           v-bind:style="{ backgroundImage: 'url(' + usersAvatars[goal.cascadeSource] + ')' }"></i>
                        <i v-else></i>
                      </div>
                    </a>
                    <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                    <a class="bp-short-process-step bp-short-process-step-ready"
                       v-bind:href="'/company/personal/user/' + userInfo.ID + '/'">
                      <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                        <i v-if="usersAvatars[userInfo.ID]"
                           v-bind:style="{ backgroundImage: 'url(' + usersAvatars[userInfo.ID] + ')' }"></i>
                        <i v-else></i>
                      </div>
                    </a>
                  </div>

                  <!-- Если есть источник каскадирования - и это оцениваемый сотрудник -->
                  <div v-if="userInfo.ID === goal.cascadeSource && userInfo.IS_BOSS === 'Y' && (userInfo.HAVE_SUBORDINATE_BOSSES === 'Y' || userInfo.HAVE_SUBORDINATE_EMPLOYEES === 'Y')">
                    <a class="bp-short-process-step bp-short-process-step-ready"
                       v-bind:href="'/company/personal/user/' + goal.cascadeSource + '/'">
                      <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                        <i v-if="usersAvatars[goal.cascadeSource]"
                           v-bind:style="{ backgroundImage: 'url(' + usersAvatars[goal.cascadeSource] + ')' }"></i>
                        <i v-else></i>
                      </div>
                    </a>
<!--                    <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>-->
                  </div>

                  <!-- Если НЕТ источника каскадирования -->
                  <div v-if="(goal.cascadeSource === '0' || goal.cascadeSource === null) && userInfo.IS_BOSS === 'Y' && (userInfo.HAVE_SUBORDINATE_BOSSES === 'Y' || userInfo.HAVE_SUBORDINATE_EMPLOYEES === 'Y')">
                    <a class="bp-short-process-step bp-short-process-step-ready"
                       v-bind:href="'/company/personal/user/' + userInfo.ID + '/'">
                      <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                        <i v-if="usersAvatars[userInfo.ID]"
                           v-bind:style="{ backgroundImage: 'url(' + usersAvatars[userInfo.ID] + ')' }"></i>
                        <i v-else></i>
                      </div>
                    </a>
<!--                    <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>-->
                  </div>

                  <!-- Если ЕСТЬ подчиненные руководители и ЕСТЬ подчиненные сотрудники-->
                  <div
                      v-if="
                      userInfo.IS_BOSS &&
                      userInfo.HAVE_SUBORDINATE_BOSSES === 'Y' &&
                      userInfo.HAVE_SUBORDINATE_EMPLOYEES === 'Y'"
                  >

                    <div class="multiple-choice-faces" style="bottom: 30px">

                      <div v-if="goal.cascadeOnBosses.length > 0">

                        <div>
                          <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                          <a class="bp-short-process-step bp-short-process-step-more">
                            <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                              <i v-if="usersAvatars[Object.values(userSubordinateBosses)[0]]"
                                 v-bind:style="{ backgroundImage: 'url(' + usersAvatars[Object.values(userSubordinateBosses)[0]] + ')' }"></i>
                              <i v-else></i>
                            </div>
                          </a>
                          <span class="process-step-more">
                            <span @click="getParticipationUsersInfo(goal, 'bosses')"
                                  style="cursor: pointer;">{{ goal.cascadeOnBosses.length }} {{localize.SUB_BOSSES}}</span>
                            <span @click="cleanCascadeUsers(goal, 'bosses')"
                                  style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle">
                                <svg width="8" height="9" viewBox="0 0 8 9" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.94151 4.50001L7.53917 1.90235C7.66246 1.77929 7.7318 1.61229 7.73196 1.43809C7.73211 1.26389 7.66306 1.09677 7.53999 0.973486C7.41692 0.850202 7.24992 0.780855 7.07573 0.780701C6.90153 0.780547 6.73441 0.849599 6.61112 0.972666L4.01347 3.57032L1.41581 0.972666C1.29253 0.849381 1.12532 0.780121 0.950968 0.780121C0.776617 0.780121 0.609408 0.849381 0.486124 0.972666C0.36284 1.09595 0.293579 1.26316 0.293579 1.43751C0.293579 1.61186 0.36284 1.77907 0.486124 1.90235L3.08378 4.50001L0.486124 7.09767C0.36284 7.22095 0.293579 7.38816 0.293579 7.56251C0.293579 7.73686 0.36284 7.90407 0.486124 8.02735C0.609408 8.15064 0.776617 8.2199 0.950968 8.2199C1.12532 8.2199 1.29253 8.15064 1.41581 8.02735L4.01347 5.4297L6.61112 8.02735C6.73441 8.15064 6.90162 8.2199 7.07597 8.2199C7.25032 8.2199 7.41753 8.15064 7.54081 8.02735C7.6641 7.90407 7.73336 7.73686 7.73336 7.56251C7.73336 7.38816 7.6641 7.22095 7.54081 7.09767L4.94151 4.50001Z"
                                        fill="#D9D9D9"/>
                                </svg>
                            </span>
                          </span>
                        </div>

                      </div>

                      <div v-if="goal.cascadeOnBosses.length === 0" style="margin-top: 15px">

                        <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                        <div class="add-faces" @click="checkAccessTo('bosses', goal)">
                          <svg width="34" height="34" viewBox="0 0 34 34" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.793579" y="0.5" width="31" height="31" rx="15.5" stroke="#A5ADB3"/>
                            <svg width="30" height="30" viewBox="-8 -8 30 30" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                              <path d="M8.00012 15V8M8.00012 8V1M8.00012 8H15.0001M8.00012 8H1.00012" stroke="#A5ADB3"
                                    stroke-linecap="round"/>
                            </svg>
                          </svg>
                          <span class="process-step-more" style="position: relative; bottom: 15px">
                            <span style="cursor: pointer; color: #535C69">{{localize.SUB_BOSSES}}</span>
                            <span style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle"></span>
                          </span>
                        </div>
                      </div>

                      <div v-if="goal.cascadeOnEmployees.length > 0">

                        <div>
                          <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                          <a class="bp-short-process-step bp-short-process-step-more">
                            <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                              <i v-if="usersAvatars[Object.values(goal.cascadeOnEmployees)[0]]"
                                 v-bind:style="{ backgroundImage: 'url(' + usersAvatars[Object.values(goal.cascadeOnEmployees)[0]] + ')' }"></i>
                              <i v-else></i>
                            </div>
                            <span class="process-step-more" style="left: 40px; bottom: 5px; position: absolute">
                              <span @click="getParticipationUsersInfo(goal, 'employees')"
                                    style="cursor: pointer;">{{ goal.cascadeOnEmployees.length }} {{localize.SUB_EMPLOYEES}}</span>
                              <span @click="cleanCascadeUsers(goal, 'employees')"
                                    style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle">
                                  <svg width="8" height="9" viewBox="0 0 8 9" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                      <path
                                          d="M4.94151 4.50001L7.53917 1.90235C7.66246 1.77929 7.7318 1.61229 7.73196 1.43809C7.73211 1.26389 7.66306 1.09677 7.53999 0.973486C7.41692 0.850202 7.24992 0.780855 7.07573 0.780701C6.90153 0.780547 6.73441 0.849599 6.61112 0.972666L4.01347 3.57032L1.41581 0.972666C1.29253 0.849381 1.12532 0.780121 0.950968 0.780121C0.776617 0.780121 0.609408 0.849381 0.486124 0.972666C0.36284 1.09595 0.293579 1.26316 0.293579 1.43751C0.293579 1.61186 0.36284 1.77907 0.486124 1.90235L3.08378 4.50001L0.486124 7.09767C0.36284 7.22095 0.293579 7.38816 0.293579 7.56251C0.293579 7.73686 0.36284 7.90407 0.486124 8.02735C0.609408 8.15064 0.776617 8.2199 0.950968 8.2199C1.12532 8.2199 1.29253 8.15064 1.41581 8.02735L4.01347 5.4297L6.61112 8.02735C6.73441 8.15064 6.90162 8.2199 7.07597 8.2199C7.25032 8.2199 7.41753 8.15064 7.54081 8.02735C7.6641 7.90407 7.73336 7.73686 7.73336 7.56251C7.73336 7.38816 7.6641 7.22095 7.54081 7.09767L4.94151 4.50001Z"
                                          fill="#D9D9D9"/>
                                  </svg>
                              </span>
                            </span>
                          </a>
                        </div>

                      </div>

                      <div v-if="goal.cascadeOnEmployees.length === 0" style="margin-top: 10px">

                        <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                        <div class="add-faces" @click="checkAccessTo('employees', goal)">
                          <svg width="34" height="34" viewBox="0 0 34 34" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.793579" y="0.5" width="31" height="31" rx="15.5" stroke="#A5ADB3"/>
                            <svg width="30" height="30" viewBox="-8 -8 30 30" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                              <path d="M8.00012 15V8M8.00012 8V1M8.00012 8H15.0001M8.00012 8H1.00012" stroke="#A5ADB3"
                                    stroke-linecap="round"/>
                            </svg>
                          </svg>
                          <span class="process-step-more" style="position: relative; bottom: 15px">
                            <span style="cursor: pointer; color: #535C69">{{localize.SUB_EMPLOYEES}}</span>
                            <span style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle"></span>
                          </span>
                        </div>

                      </div>

                    </div>

                  </div>

                  <!-- Если ЕСТЬ подчиненные руководители и НЕТ подчиненных сотрудников-->
                  <div
                      v-if="
                      userInfo.IS_BOSS === 'Y' &&
                      userInfo.HAVE_SUBORDINATE_BOSSES === 'Y' &&
                      userInfo.HAVE_SUBORDINATE_EMPLOYEES === 'N'"
                  >

                    <div v-if="goal.cascadeOnBosses.length > 0">

                      <div>
                        <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                        <a class="bp-short-process-step bp-short-process-step-more">
                          <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                            <i v-if="usersAvatars[Object.values(goal.cascadeOnBosses)[0]]"
                               v-bind:style="{ backgroundImage: 'url(' + usersAvatars[Object.values(goal.cascadeOnBosses)[0]] + ')' }"></i>
                            <i v-else></i>
                          </div>
                          <span href="#" class="process-step-more" style="left: 40px; bottom: 5px; position: absolute">
                            <span @click="getParticipationUsersInfo(goal, 'bosses')"
                                  style="cursor: pointer">{{ goal.cascadeOnBosses.length }} {{localize.SUB_BOSSES}}</span>
                            <span @click="cleanCascadeUsers(goal, 'bosses')"
                                  style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle">
                                <svg width="8" height="9" viewBox="0 0 8 9" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.94151 4.50001L7.53917 1.90235C7.66246 1.77929 7.7318 1.61229 7.73196 1.43809C7.73211 1.26389 7.66306 1.09677 7.53999 0.973486C7.41692 0.850202 7.24992 0.780855 7.07573 0.780701C6.90153 0.780547 6.73441 0.849599 6.61112 0.972666L4.01347 3.57032L1.41581 0.972666C1.29253 0.849381 1.12532 0.780121 0.950968 0.780121C0.776617 0.780121 0.609408 0.849381 0.486124 0.972666C0.36284 1.09595 0.293579 1.26316 0.293579 1.43751C0.293579 1.61186 0.36284 1.77907 0.486124 1.90235L3.08378 4.50001L0.486124 7.09767C0.36284 7.22095 0.293579 7.38816 0.293579 7.56251C0.293579 7.73686 0.36284 7.90407 0.486124 8.02735C0.609408 8.15064 0.776617 8.2199 0.950968 8.2199C1.12532 8.2199 1.29253 8.15064 1.41581 8.02735L4.01347 5.4297L6.61112 8.02735C6.73441 8.15064 6.90162 8.2199 7.07597 8.2199C7.25032 8.2199 7.41753 8.15064 7.54081 8.02735C7.6641 7.90407 7.73336 7.73686 7.73336 7.56251C7.73336 7.38816 7.6641 7.22095 7.54081 7.09767L4.94151 4.50001Z"
                                        fill="#D9D9D9"/>
                                </svg>
                            </span>
                          </span>
                        </a>
                      </div>

                    </div>

                    <div v-if="goal.cascadeOnBosses.length === 0" style="margin-top: 10px">

                      <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                      <div class="add-faces" @click="checkAccessTo('bosses', goal)">
                        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect x="0.793579" y="0.5" width="31" height="31" rx="15.5" stroke="#A5ADB3"/>
                          <svg width="30" height="30" viewBox="-8 -8 30 30" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.00012 15V8M8.00012 8V1M8.00012 8H15.0001M8.00012 8H1.00012" stroke="#A5ADB3"
                                  stroke-linecap="round"/>
                          </svg>
                        </svg>
                        <span class="process-step-more" style="position: relative; bottom: 15px">
                          <span style="cursor: pointer; color: #535C69">{{localize.SUB_BOSSES}}</span>
                          <span style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle"></span>
                        </span>
                      </div>

                    </div>

                  </div>

                  <!-- Если НЕТ подчиненных руководителей и ЕСТЬ подчиненные сотрудники-->
                  <div
                      v-if="
                      userInfo.IS_BOSS === 'Y' &&
                      userInfo.HAVE_SUBORDINATE_BOSSES === 'N' &&
                      userInfo.HAVE_SUBORDINATE_EMPLOYEES === 'Y'"
                  >

                    <div v-if="goal.cascadeOnEmployees.length > 0">

                      <div>
                        <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                        <a class="bp-short-process-step bp-short-process-step-more">
                          <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                            <i v-if="usersAvatars[Object.values(goal.cascadeOnEmployees)[0]]"
                               v-bind:style="{ backgroundImage: 'url(' + usersAvatars[Object.values(goal.cascadeOnEmployees)[0]] + ')' }"></i>
                            <i v-else></i>
                          </div>
                          <span href="#" class="process-step-more" style="left: 40px; bottom: 5px; position: absolute">
                            <span @click="getParticipationUsersInfo(goal, 'employees')"
                                  style="cursor: pointer">{{ goal.cascadeOnEmployees.length }} {{localize.SUB_EMPLOYEES}}</span>
                            <span @click="cleanCascadeUsers(goal, 'employees')"
                                  style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle">
                                <svg width="8" height="9" viewBox="0 0 8 9" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.94151 4.50001L7.53917 1.90235C7.66246 1.77929 7.7318 1.61229 7.73196 1.43809C7.73211 1.26389 7.66306 1.09677 7.53999 0.973486C7.41692 0.850202 7.24992 0.780855 7.07573 0.780701C6.90153 0.780547 6.73441 0.849599 6.61112 0.972666L4.01347 3.57032L1.41581 0.972666C1.29253 0.849381 1.12532 0.780121 0.950968 0.780121C0.776617 0.780121 0.609408 0.849381 0.486124 0.972666C0.36284 1.09595 0.293579 1.26316 0.293579 1.43751C0.293579 1.61186 0.36284 1.77907 0.486124 1.90235L3.08378 4.50001L0.486124 7.09767C0.36284 7.22095 0.293579 7.38816 0.293579 7.56251C0.293579 7.73686 0.36284 7.90407 0.486124 8.02735C0.609408 8.15064 0.776617 8.2199 0.950968 8.2199C1.12532 8.2199 1.29253 8.15064 1.41581 8.02735L4.01347 5.4297L6.61112 8.02735C6.73441 8.15064 6.90162 8.2199 7.07597 8.2199C7.25032 8.2199 7.41753 8.15064 7.54081 8.02735C7.6641 7.90407 7.73336 7.73686 7.73336 7.56251C7.73336 7.38816 7.6641 7.22095 7.54081 7.09767L4.94151 4.50001Z"
                                        fill="#D9D9D9"/>
                                </svg>
                            </span>
                          </span>
                        </a>
                      </div>

                    </div>

                    <div v-if="goal.cascadeOnEmployees.length === 0" style="margin-top: 10px">

                      <span class="bp-short-prosess-steps-arrow bp-short-prosess-steps-arrow-running"></span>
                      <div class="add-faces" @click="checkAccessTo('employees', goal)">
                        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect x="0.793579" y="0.5" width="31" height="31" rx="15.5" stroke="#A5ADB3"/>
                          <svg width="30" height="30" viewBox="-8 -8 30 30" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.00012 15V8M8.00012 8V1M8.00012 8H15.0001M8.00012 8H1.00012" stroke="#A5ADB3"
                                  stroke-linecap="round"/>
                          </svg>
                        </svg>
                        <span class="process-step-more" style="position: relative; bottom: 15px">
                          <span style="cursor: pointer; color: #535C69">{{localize.SUB_EMPLOYEES}}</span>
                          <span style="cursor: pointer; margin: 0 0 0 5px; vertical-align: middle"></span>
                        </span>
                      </div>

                    </div>

                  </div>

                </div>
              </div>

            </div>

          </td>

          <!-- linked departments -->
          <td class="block-process-table-column">
            <div :id="goal.signature">
<!--              <span class="ui-tile-selector-selector-wrap">-->
<!--                  <span data-role="tile-select" class="ui-tile-selector-select">Добавить</span>-->
<!--              </span>-->
            </div>
          </td>

          <!-- Linked projects and tasks -->
          <td class="block-process-table-column">
            <div :id="goal.signature + '_select_project_task'">
              <!--                            <span class="ui-tile-selector-selector-wrap">-->
              <!--                                <span data-role="tile-select" class="ui-tile-selector-select">Добавить</span>-->
              <!--                            </span>-->
            </div>
          </td>

          <td class="block-process-table-column" v-if="goal.disableFields === 'N'" @click="deleteGoal(index)">
            <span class="delete-goal-row">x</span>
          </td>

        </tr>

        <tr>
          <td colspan="5" class="block-process-weight-title">{{ localize.TOTAL_WEIGHT }}</td>
          <td colspan="5" class="block-process-weight-value"
              v-bind:class="{'block-process-weight-value-error' : totalWeight !== 100 }">{{ totalWeight }}
          </td>
        </tr>

      </table>
    </div>
    <div class="button-block" v-if="canEdit">
      <button type="button" class="ui-btn ui-btn-primary ui-btn-icon-add crm-btn-toolbar-add"
              :class="{'btn-submit-cancel': disableBtn}"
              :disabled="disableBtn"
              @click="addGoal">{{ localize.ADD_GOAL }}
      </button>
      <button type="button" class="ui-btn ui-btn-primary"
              :class="{'btn-submit-cancel': disableBtnSave}"
              :disabled="disableBtnSave"
              @click="saveGoals">{{ localize.SAVE_GOALS }}
      </button>
    </div>

  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "GoalsTable",
  data() {
    return {
      contentSelect: '',
      ptSelectors: []
    }
  },
  computed: {
    ...mapState([
      'goals',
      'cardId',
      'canEdit',
      'typeGoals',
      'listGoals',
      'minWeight',
      'userInfo',
      'userProjects',
      'userTasks',
      'userSubordinateEmployees',
      'userSubordinateBosses',
      'usersAvatars',
      'cascadeAccess',
      'maxCountGoals'
    ]),
    localize() {
      return Object.freeze({
        'TYPE_GOALS': BX.message("TYPE_GOALS"),
        'GOAL': BX.message("GOAL"),
        'CRITERION': BX.message("CRITERION"),
        'WEIGHT': BX.message("WEIGHT"),
        'DELETE': BX.message("DELETE"),
        'SELECT_TYPE_GOAL': BX.message("SELECT_TYPE_GOAL"),
        'SELECT_GOAL': BX.message("SELECT_GOAL"),
        'TOTAL_WEIGHT': BX.message("TOTAL_WEIGHT"),
        'ADD_GOAL': BX.message("ADD_GOAL"),
        'SAVE_GOALS': BX.message("SAVE_GOALS"),
        'LINK_TASK_PROJECT': BX.message("LINK_TASK_PROJECT"),
        'CASCADE': BX.message("CASCADE"),
        'GOAL_FILE': BX.message("GOAL_FILE"),
        'DEPENDS_ON': BX.message("DEPENDS_ON"),
        'VALUE_FROM': BX.message("VALUE_FROM"),
        'VALUE_TO': BX.message("VALUE_TO"),
        'ATTACH': BX.message("ATTACH"),
        'TASKS': BX.message("TASKS"),
        'PROJECTS': BX.message("PROJECTS"),
        'WARNING_NON_PARTICIPATION': BX.message("WARNING_NON_PARTICIPATION"),
        'WARNING_ACCEPT_DESC': BX.message("WARNING_ACCEPT_DESC"),
        'WARNING_CANCEL_DESC': BX.message("WARNING_CANCEL_DESC"),
        'TASK': BX.message("TASK"),
        'PROJECT': BX.message("PROJECT"),
        'APPROVE': BX.message("APPROVE"),
        'DECLINE': BX.message("DECLINE"),
        'WARNING_INFO': BX.message("WARNING_INFO"),
        'CASCADE_THIS': BX.message("CASCADE_THIS"),
        'CASCADE_CANCEL': BX.message("CASCADE_CANCEL"),
        'SUB_BOSSES': BX.message("SUB_BOSSES"),
        'SUB_EMPLOYEES': BX.message("SUB_EMPLOYEES"),
      })
    },
    totalWeight() {
      let total = 0;
      this.goals.forEach(goal => {
        total += parseInt(goal.weight) ? parseInt(goal.weight) : 0;
      });
      return total;
    },
    disableBtn() {
      return this.goals.length >= this.maxCountGoals;
    },
    disableBtnSave() {
      let total = 0;
      let hasError = false;
      this.goals.forEach(goalItem => {
        if (
            parseInt(goalItem.weight) < this.minWeight
            || parseInt(goalItem.weight) > 100
            || goalItem.goal && goalItem.goal.length === 0
            || goalItem.task && goalItem.task.length === 0
            || goalItem.criterion.length === 0
        )
          hasError = true;
        total += parseInt(goalItem.weight) ? parseInt(goalItem.weight) : 0;
      });

      return total !== 100 || hasError || this.goals.length > 5;
    }
  },
  mounted() {
    setTimeout(this.initSelector, 100);
    this.synchronizeCascadeAccess();
  },
  methods: {
    initSelector() {
      this.goals.forEach((goal, index) => {
            // include user selector
            this.getSelector(goal.signature, goal.disableFields, goal.depend);
            this.checkGoalWeight(index)
            // include project & task selector
            this.getProjectTaskSelector(goal.signature + '_select_project_task', goal.disableFields, goal.related,);
          }
      );
    },
    getSelector(id, disableFields, depend = []) {
      BX.ajax({
        url: templateFolder + '/select.php',
        data: {
          id: id,
          disableFields: disableFields,
          depend: depend,
          isDep: 1,
        },
        dataType: 'html',
        method: 'POST',
        timeout: 30,
        async: false,
        processData: true,
        scriptsRunFirst: false,
        cache: false,
        onsuccess: data => {
          BX(id).innerHTML = data;
          if (disableFields === 'Y')
          {
            let addButton = BX(id).querySelector('span.ui-tile-selector-select-container');
            addButton.remove();
          }
        },
        onfailure: error => {
          console.log(error);
        }
      });
    },
    getProjectTaskSelector(id, disableFields, related = []) {
      let root = this;
      let tagSelector = new BX.UI.EntitySelector.TagSelector({
        id: id,
        readonly: disableFields === 'Y',
        events: {
          onAfterTagAdd: function (event) {
              let data = event.data.tag;
              root.goals.forEach(function (v, k){
                if (v.signature + '_select_project_task' === id && data.entityType === 'task')
                {
                  v.related.push('task_' + data.id);
                }
                if (v.signature + '_select_project_task' === id && data.entityType === 'project')
                {
                  v.related.push('project_' + data.id);
                }
              });
            },
            onAfterTagRemove: function (event) {
              let data = event.data.tag;
              root.goals.forEach(function (goal){
                goal.related.forEach(function (v, k){
                  if (typeof v === 'object') {
                    if (data.entityId === v.entityId) {
                      goal.related.splice(k, 1);
                    }
                  } else {
                    if (data.entityId === v) {
                      goal.related.splice(k, 1);
                    }
                  }
                });
              });
            }
        },
        dialogOptions: {
          id: id + '_dialog',
          context: 'linked',
          items: root.userProjects.concat(root.userTasks),
          selectedItems: related ? related : [],
          preselectedItems: [],
          undeselectedItems: [],
          tabs: [
            {
              id: 'task',
              title: root.localize.TASKS,
            },
            {
              id: 'project',
              title: root.localize.PROJECTS,
            }
          ],
          popupOptions: [],
          multiple: true,
          preload: false,
          dropdownMode: true,
          enableSearch: false,
          searchOptions: {
            allowCreateItem: false,
          },
          searchTabOptions: [],
          recentTabOptions: [],
          tagSelectorOptions: [],
          events: {},
          hideOnSelect: false,
          hideOnDeselect: false,
          clearSearchOnSelect: true,
          width: 565,
          height: 420,
          autoHide: true,
          hideByEsc: true,
          offsetTop: 5,
          offsetLeft: 0,
          cacheable: true,
          focusOnFirst: false,
          footer: '',
          footerOptions: {},
          showAvatars: false,
          compactView: true,
          clearUnavailableItems: true,
        }
      });
      root.ptSelectors.push(tagSelector);
      tagSelector.renderTo(BX(id));
    },
    loadFile(event, index) {

      var obFormData = new FormData();

      if (event.target.files && event.target.files[0]) {
        let file = event.target.files[0];
        obFormData.append("file", file, file.name);

        BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'saveFile', {
          mode: 'class',
          dataType: 'json',
          data: obFormData
        })
            .then(response => {
              this.goals[index]['file'] = {
                id: response.data.id,
                name: file.name,
                path: response.data.path
              }

            })
            .catch(error => {
              console.log(error);
            });
      }
    },
    deleteFile(index) {
      BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'deleteFile', {
        mode: 'class',
        dataType: 'json',
        data: {
          id: this.goals[index]['file']
        }
      })
          .then(() => {
            this.goals[index]['file'] = '';
          })
          .catch(error => {
            console.log(error);
          });
    },
    clearGoalTask(index) {
      this.goals[index]['goal'] = '';
      this.goals[index]['task'] = '';
      this.goals[index]['type_title'] = '';
      this.goals[index]['goal_title'] = '';
      if(!!this.typeGoals[this.goals[index].type_goal]){
        this.goals[index]['type_title'] = this.typeGoals[this.goals[index].type_goal].value;
      }
      this.synchronizeCascadeAccess();
    },
    checkGoalWeight(index) {
      let goal = this.listGoals[this.goals[index].type_goal];
      let id = this.goals[index].goal;
      let weight = goal[id].PROPERTY_WEIGHT_GOALS_VALUE;
      if (weight > 0) {
        if (this.goals[index].weight < weight) {
          this.goals[index].weight = weight;
        }
        this.goals[index].categoryWeight = weight;
      } else {
        if (this.goals[index].weight <= 0) {
          this.goals[index].weight = 10;
        }
        this.goals[index].categoryWeight = null;
      }
      if(goal[this.goals[index].goal]){
        this.goals[index]['goal_title'] = goal[this.goals[index].goal].NAME;
      }
      this.synchronizeCascadeAccess();
    },
    checkMinGoalWeight(event, index) {
      let weight = this.getActiveCategoryWeightGoal(index);
      weight = parseInt(weight);
      if (weight > 0 && parseInt(this.goals[index].weight) < weight) {
        this.goals[index].weight = weight;
      }
    },
    getActiveCategoryWeightGoal(index) {
      return this.goals[index].categoryWeight;
    },
    addGoal() {
      let signature = Math.random().toString(36).substring(7);
      this.goals.push({
        type_goal: '',
        type_title: '',
        goal_title: '',
        goal: '',
        task: '',
        criterion: '',
        weight: this.minWeight,
        file: '',
        depend: [],
        related: [],
        canCascade: 'N',
        cascadeOnEmployees: [],
        cascadeOnBosses: [],
        cascadeSource: '0',
        disableFields: 'N',
        signature: signature,
      });

      this.$nextTick(function () {
        setTimeout(this.getSelector, 100, signature)
        setTimeout(this.getProjectTaskSelector, 100, signature + '_select_project_task')
      });
    },
    deleteGoal(index) {
      this.goals.splice(index, 1);
    },
    saveGoals() {
      event.preventDefault();
      let slider = window.top.BX.SidePanel.Instance.getTopSlider();
      slider.showLoader();

      this.goals.forEach(goal => {

        // Сохранение выбранных подразделений
        let elements = BX.findChildren(
            BX(goal.signature),
            {
              "tag": "input",
              "property": {
                "name": goal.signature + "[]"
              }
            },
            true,
            true
        );
        goal.depend = [];
        elements.forEach(element => {
          goal.depend.push(element.value);
        });

        // Сохранение выбранных задач и проектов
        let root = this;
        root.ptSelectors.forEach(selector => {
          if (selector.id === goal.signature + '_select_project_task') {
            goal.related = [];
            if (selector.tags.length > 0) {
              selector.tags.forEach(tag => {
                switch (tag.entityType) {
                  case 'task':
                    goal.related.push('task_' + tag.id);
                    break;
                  case 'project':
                    goal.related.push('project_' + tag.id);
                    break;
                }
              });
            } else {
              goal.related = false;
            }
          }
        });
      });

      window.top.BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'saveGoals', {
        mode: 'class',
        data: {
          params: {
            cardId: this.cardId,
            goals: this.goals,
            userId: this.userInfo.ID,
          }
        }
      })
          .then(() => {
            slider.closeLoader();

            let id = window.top.BX.Main.gridManager.data[0].id;
            window.top.BX.Main.gridManager.getById(id).instance
                .reloadTable('POST', {apply_filter: 'Y', clear_nav: 'Y'});
          })
          .catch(() => {
            slider.closeLoader();
          });

    },
    /**
     * Метод синхронизирует доступность полей каскадирования (отображаются они в таблице или нет)
     */
    synchronizeCascadeAccess() {
      // Текущие выбранные цели
      let selectedGoals = this.goals;
      // Для каких корпоративных целей открыт доступ каскадирования
      let cascadeAccess = this.cascadeAccess === undefined ? [] : this.cascadeAccess;
      let corporateGoalId = 0;
      for (let key in this.typeGoals) {
        if (this.typeGoals[key].xml_id === 'strategy') {
          corporateGoalId = this.typeGoals[key].id;
        }
      }
      selectedGoals.forEach(function (data) {
        // Если ID цели есть в таблице доступности корпоративных целей для каскадирования
        if (cascadeAccess.includes(data.goal)) {
          data.canCascade = 'Y';
          // Если ID цели нет, и тип цели не корпоративный
        } else if ((data.goal === '' || data.goal === '0') && data.type_goal !== corporateGoalId) {
          data.canCascade = 'Y';
          // Во всех остальных случаях
        } else {
          data.canCascade = 'N';
          data.cascadeOnEmployees = [];
          data.cascadeOnBosses = [];
        }
      });
    },
    /**
     * Отображаем в popup информацию о пользователях, на которых установлено каскадирование
     *
     * @param goal
     * @param logic
     */
    getParticipationUsersInfo(goal, logic) {
      let app = this;
      window.top.BX.ajax.runComponentAction(
          'ithive.goalsbazhen:edit.card.goals',
          'getUsersFullName',
          {
            mode: 'class',
            data: {
              users: (logic === 'bosses') ? goal.cascadeOnBosses : goal.cascadeOnEmployees
            }
          })
          .then(response => {
            let content = `
            <div style="display: grid; margin: 10px">
            `;

            for (let key in response.data) {

              content = content + `
              <div style="display: inline-flex">
              <a class="bp-short-process-step bp-short-process-step-firs" style="margin: 0 0 10px 0">
              <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
              <i style="background-image: url('${app.usersAvatars[key]}')"></i>
              </div>
              <a class="process-step-more" href="/company/personal/user/${key}/" style="margin: 6px 0 0 14px">${response.data[key]}</a>
              <span
              class="js_remove_user_cascade"
              data-user="${key}"
              data-logic="${logic}"
              data-signature="${goal.signature}"
              style="cursor: pointer; margin: 10px 0 0 10px; vertical-align: middle;">
                <svg width="8" height="9" viewBox="0 0 8 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.94151 4.50001L7.53917 1.90235C7.66246 1.77929 7.7318 1.61229 7.73196 1.43809C7.73211 1.26389 7.66306 1.09677 7.53999 0.973486C7.41692 0.850202 7.24992 0.780855 7.07573 0.780701C6.90153 0.780547 6.73441 0.849599 6.61112 0.972666L4.01347 3.57032L1.41581 0.972666C1.29253 0.849381 1.12532 0.780121 0.950968 0.780121C0.776617 0.780121 0.609408 0.849381 0.486124 0.972666C0.36284 1.09595 0.293579 1.26316 0.293579 1.43751C0.293579 1.61186 0.36284 1.77907 0.486124 1.90235L3.08378 4.50001L0.486124 7.09767C0.36284 7.22095 0.293579 7.38816 0.293579 7.56251C0.293579 7.73686 0.36284 7.90407 0.486124 8.02735C0.609408 8.15064 0.776617 8.2199 0.950968 8.2199C1.12532 8.2199 1.29253 8.15064 1.41581 8.02735L4.01347 5.4297L6.61112 8.02735C6.73441 8.15064 6.90162 8.2199 7.07597 8.2199C7.25032 8.2199 7.41753 8.15064 7.54081 8.02735C7.6641 7.90407 7.73336 7.73686 7.73336 7.56251C7.73336 7.38816 7.6641 7.22095 7.54081 7.09767L4.94151 4.50001Z" fill="#D9D9D9">
                    </path>
                    </svg>
                </span>
              </div>
              `;

            }
            content = content + `</div>`;
            let popup = BX.PopupWindowManager.create('users_participation_' + logic, BX('users_participation_' + logic), {
              content: content,
              zIndex: 100, // z-index
              closeIcon: {
                // объект со стилями для иконки закрытия, при null - иконки не будет
                opacity: 1
              },
              closeByEsc: true, // закрытие окна по esc
              darkMode: false, // окно будет светлым или темным
              autoHide: true, // закрытие при клике вне окна
              draggable: false, // можно двигать или нет
              resizable: false, // можно ресайзить
              width: 300, // Ширина
              lightShadow: true, // использовать светлую тень у окна
              angle: false, // появится уголок
              overlay: {
                // объект со стилями фона
                backgroundColor: 'black',
                opacity: 500
              },
              buttons: [],
              events: {
                onPopupShow: function () {
                  // Событие при показе окна
                  app.addAbilityRemoveCascadeUser(popup);
                },
                onPopupClose: function () {
                  // Событие при закрытии окна
                  popup.destroy();
                }
              }
            });
            popup.show();

          })
          .catch(error => {
            console.log(error);
          });
    },
    /**
     * Очищаем пользователей на который установлено каскадирование (руководители или подчиненные)
     *
     * @param goal
     * @param logic
     */
    cleanCascadeUsers(goal, logic) {
      switch (logic)
      {
        case 'bosses':
          goal.cascadeOnBosses = [];
          break;
        case 'employees':
          goal.cascadeOnEmployees = [];
          break;
      }
    },
    /**
     * Проверяем доступность задач и проектов для пользователей на которых установлено каскадирование
     *
     * @param logic
     * @param goal
     */
    checkAccessTo(logic, goal) {
      let app = this;

      // Сохранение выбранных задач и проектов
      let root = this;
      root.ptSelectors.forEach(selector => {
        if (selector.id === goal.signature + '_select_project_task') {
          goal.related = [];
          if (selector.tags.length > 0) {
            selector.tags.forEach(tag => {
              switch (tag.entityType) {
                case 'task':
                  goal.related.push('task_' + tag.id);
                  break;
                case 'project':
                  goal.related.push('project_' + tag.id);
                  break;
              }
            });
          } else {
            goal.related = false;
          }
        }
      });

      BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'checkUsersParticipation', {
        mode: 'class',
        data: {
          logic: logic,
          currentCardUserId: app.userInfo.ID,
          related: goal.related ? goal.related : []
        }
      })
          .then(response => {

            //console.log('INFO >>>>>>>>');
            //console.log(response.data.TASKS);
            //console.log(response.data.PROJECTS);
            //console.log('<<<<<<<< INFO');

            // todo: раскомментирование включит уведомление о проектах и задачах в которых нет каскадируемых
            // let tasksCount = 0;
            // if (response.data.TASKS) {
            //   tasksCount = Object.keys(response.data?.TASKS).length;
            // }
            //
            // let projectsCount = 0;
            // if (response.data.PROJECTS) {
            //   projectsCount = Object.keys(response.data?.PROJECTS).length;
            // }

            // todo: раскомментирование включит уведомление о проектах и задачах в которых нет каскадируемых
            // Проверяем - есть ли задачи и проекты в которых нет каскадируемых пользователей
            // if ((tasksCount + projectsCount) > 0) {
            //
              let content = `
              <div style="display: grid">
                <div class="notification_alert">

                <div class="notification_alert__title">
                    ${app.localize.WARNING_NON_PARTICIPATION}
                </div>
              `;

              // убрать это - если требуется показывать кого нет в проектах и задачах
              content = content + `
                <div class="notification_alert__warning">
                  ${app.localize.WARNING_INFO}
              </div>
              `;

              // todo: раскомментирование включит уведомление о проектах и задачах в которых нет каскадируемых
              // content = content + `
              //   <div class="notification_alert__description_block">
              //       <div class="notification_alert__accept_message">
              //       ${app.localize.WARNING_ACCEPT_DESC}
              //       </div>
              //       <div class="notification_alert__cancel_message">
              //       ${app.localize.WARNING_CANCEL_DESC}
              //       </div>
              //   </div>
              //   `;

              // content = content + `
              //   <div style="height: 100px">
              // `;

            // todo: раскомментирование включит уведомление о проектах и задачах в которых нет каскадируемых
            //
            //   if (tasksCount > 0) {
            //     for (let key in response.data.TASKS) {
            //       content = content + `
            //       <div class="notification_alert__common_message">
            //           <span>${app.localize.TASK}:
            //             <a href="${response.data?.TASKS[key].DATA.URL}">${response.data?.TASKS[key].DATA.TITLE}</a>
            //           </span>
            //           <hr>
            //       `;
            //
            //       for (let k in response.data?.TASKS[key].NONE_PARTICIPATION_USERS) {
            //         let user = response.data?.TASKS[key].NONE_PARTICIPATION_USERS[k];
            //         content = content + `
            //         <a class="bp-short-process-step bp-short-process-step-firs" style="margin: 4px 0 4px 0">
            //             <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
            //               <i style="background-image: url('${user.AVATAR_SRC}')"></i>
            //             </div>
            //             <a class="notification_alert__user_link" href="${user.USER_LINK}">${user.FULL_NAME}</a><br>
            //         </a>
            //         `;
            //       }
            //
            //       content = content + `
            //       </div>
            //       `;
            //     }
            //   }
            //
            //   if (projectsCount > 0) {
            //     for (let key in response.data.PROJECTS) {
            //       content = content + `
            //       <div class="notification_alert__common_message">
            //           <span>${app.localize.PROJECT}:
            //             <a href="${response.data?.PROJECTS[key].DATA.URL}">${response.data?.PROJECTS[key].DATA.NAME}</a>
            //           </span>
            //           <hr>
            //       `;
            //
            //       for (let k in response.data?.PROJECTS[key].NONE_PARTICIPATION_USERS) {
            //         let user = response.data?.PROJECTS[key].NONE_PARTICIPATION_USERS[k];
            //         content = content + `
            //         <a class="bp-short-process-step bp-short-process-step-firs" style="margin: 4px 0 4px 0">
            //             <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
            //               <i style="background-image: url('${user.AVATAR_SRC}')"></i>
            //             </div>
            //             <a class="notification_alert__user_link" href="${user.USER_LINK}">${user.FULL_NAME}</a><br>
            //         </a>
            //         `;
            //       }
            //
            //       content = content + `
            //       </div>
            //       `;
            //     }
            //   }
            //
              content = content + `
              </div></div></div>
              `;
            //
              let popup = BX.PopupWindowManager.create('users_none_participation', BX('users_none_participation'), {
                content: content,
                zIndex: 100, // z-index
                darkMode: false, // окно будет светлым или темным
                draggable: false, // можно двигать или нет
                resizable: false, // можно ресайзить
                width: 600, // Ширина
                lightShadow: true, // использовать светлую тень у окна
                angle: false, // появится уголок
                overlay: {
                  // объект со стилями фона
                  backgroundColor: 'black',
                  opacity: 70
                },
                buttons: [
                  new BX.PopupWindowButton({
                    text: app.localize.CASCADE_THIS,
                    className: 'ui-btn ui-btn-primary',
                    events: {
                      click: function () {
                        // Добавляем в каскадирование пользователей
                        switch (logic)
                        {
                          case 'bosses':
                            goal.cascadeOnBosses = response.data.USERS;
                            break;
                          case 'employees':
                            goal.cascadeOnEmployees = response.data.USERS;
                            break;
                        }
                        popup.destroy();
                      }
                    }
                  }),
                  new BX.PopupWindowButton({
                    text: app.localize.CASCADE_CANCEL,
                    className: 'ui-btn ui-btn-outline',
                    events: {
                      click: function () {
                        // Событие при клике на кнопку
                        popup.destroy();
                      }
                    }
                  })
                ],
                events: {
                  onPopupShow: function () {
                    // Событие при показе окна
                  },
                  onPopupClose: function () {
                    // Событие при закрытии окна
                    popup.destroy();
                  }
                }
              });
              popup.show();
            // todo: раскомментирование включит уведомление о проектах и задачах в которых нет каскадируемых
            // } else {
            //   // Добавляем в каскадирование пользователей
            //   switch (logic)
            //   {
            //     case 'bosses':
            //       goal.cascadeOnBosses = response.data.USERS;
            //       break;
            //     case 'employees':
            //       goal.cascadeOnEmployees = response.data.USERS;
            //       break;
            //   }
            // }

          })
          .catch(error => {
            console.log(error);
          });
    },
    /**
     * Добавляем события при клике на крестик в popup каскадируемых пользователей
     *
     * @param popup
     */
    addAbilityRemoveCascadeUser(popup)
    {
      let app = this;
      let cascadeUserRemoveNodeList = popup.contentContainer.querySelectorAll('span.js_remove_user_cascade');
      cascadeUserRemoveNodeList.forEach(function (data){
        data.addEventListener('click', function (){
          app.removeCascadeUser(
              popup,
              data
          )
        })
      });
    },
    /**
     * Удаляем пользователя из каскадируемых
     *
     * @param popup
     * @param node
     */
    removeCascadeUser(popup, node)
    {
      let app = this;
      let user = node.dataset.user;
      let logic = node.dataset.logic;
      let signature = node.dataset.signature;
      app.goals.forEach(function (data){
        if (data.signature === signature)
        {
          switch (logic)
          {
            case 'bosses':
              data.cascadeOnBosses.forEach(function (v, i){
                if (v === user)
                {
                  data.cascadeOnBosses.splice(i, 1);
                  if (data.cascadeOnBosses.length === 0)
                  {
                    popup.destroy();
                  } else {
                    node.parentNode.remove();
                  }
                }
              });
              break;
            case 'employees':
              data.cascadeOnEmployees.forEach(function (v, i){
                if (v === user)
                {
                  data.cascadeOnEmployees.splice(i, 1);
                  if (data.cascadeOnEmployees.length === 0)
                  {
                    popup.destroy();
                  } else {
                    node.parentNode.remove();
                  }
                }
              });
              break;
          }
        }
      });
    }
  }
}
</script>