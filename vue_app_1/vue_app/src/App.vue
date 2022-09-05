<template>
  <div id="app">
    <form method="post" name="sliderForm" class="slider-form">
      <input type="hidden" class="slider-field" value="" name="ID"/>

      <head-title/>
      <head-stages/>

      <div class="form-tabs">
        <span class="form-tabs-span" v-bind:class="{'form-tabs-span-active': activeTab === 'goals'}"
              @click="activeTab = 'goals'">{{ localize.GOALS }}</span>
        <span class="form-tabs-span" v-bind:class="{'form-tabs-span-active': activeTab === 'history'}"
              @click="activeTab = 'history'">{{ localize.HISTORY }}</span>
      </div>

      <div class="form-container" v-if="activeTab === 'goals'">
        <head-info/>
        <goals-table/>
      </div>

      <div class="form-container" v-if="activeTab === 'history'">
        <logs/>
      </div>


      <template v-if="canEdit">
        <div class="button-line" v-if="isDraftStage">
          <button type="button" class="btn-submit" :disabled="disableBtn"
                  v-bind:class="{'btn-submit-cancel': disableBtn}"
                  @click="sendAgreement()">{{ localize.SEND_AGREE }}
          </button>
          <button type="button" class="btn-cancel" @click="closeSlider()">{{ localize.DECLINE }}</button>
        </div>
        <div class="button-line" v-else-if="isApprovalStage">
          <button type="button" class="btn-submit" :disabled="disableBtn"
                  v-bind:class="{'btn-submit-cancel': disableBtn}"
                  @click="sendAgreement()">{{ localize.APPROVE }}
          </button>
          <button type="button" class="btn-return" @click="openModalComment()">{{ localize.NOT_APPROVE }}</button>
          <button type="button" class="btn-cancel" @click="closeSlider()">{{ localize.DECLINE }}</button>
        </div>
        <div class="button-line" v-else>
          <button type="button" class="btn-submit" :disabled="disableBtn"
                  v-bind:class="{'btn-submit-cancel': disableBtn}"
                  @click="sendAgreement()">{{ localize.AGREE }}
          </button>
          <button type="button" class="btn-return" @click="openModalComment()">{{ localize.NOT_AGREE }}</button>
          <button type="button" class="btn-cancel" @click="closeSlider()">{{ localize.DECLINE }}</button>
        </div>

        <div id="commentPopup" style="display: none">
          <select class="block-process-table-select" style="margin-bottom: 15px;" v-model="stageToChange"
                  v-if="isAdmin">
            <option value="" selected disabled>{{ localize.SELECT_STAGE }}</option>
            <option v-for="stage in statusGoals" :value="stage.id" v-if="stage.sort < activeSort">{{
                stage.value
              }}
            </option>
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
import GoalsTable from "./components/GoalsTable";
import Logs from "./components/Logs";
import {mapState} from "vuex";

export default {
  name: 'App',
  components: {
    HeadTitle,
    HeadStages,
    HeadInfo,
    GoalsTable,
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
      'goals',
      'cardId',
      'canEdit',
      'statusGoals',
      'isDraftStage',
      'isApprovalStage',
      'activeStage',
      'minWeight',
      'isAdmin',
      'usersAvatars'
    ]),
    localize() {
      return Object.freeze({
        'GOALS': BX.message("GOALS"),
        'HISTORY': BX.message("HISTORY"),
        'SEND_AGREE': BX.message("SEND_AGREE"),
        'DECLINE': BX.message("DECLINE"),
        'APPROVE': BX.message("APPROVE"),
        'NOT_APPROVE': BX.message("NOT_APPROVE"),
        'AGREE': BX.message("AGREE"),
        'NOT_AGREE': BX.message("NOT_AGREE"),
        'COMMENT': BX.message("COMMENT"),
        'SEND': BX.message("SEND"),
        'SELECT_STAGE': BX.message("SELECT_STAGE"),
        'LINK_TASK_PROJECT': BX.message("LINK_TASK_PROJECT"),
        'CASCADE': BX.message("CASCADE"),
        'INTRODUCED': BX.message("INTRODUCED"),
        'EMPLOYEES_NOT_ACCESS_GOALS': BX.message("EMPLOYEES_NOT_ACCESS_GOALS"),
        'TO_MODULE_ADMINS': BX.message("TO_MODULE_ADMINS"),
        'SUB_BOSSES': BX.message("SUB_BOSSES"),
        'SUB_EMPLOYEES': BX.message("SUB_EMPLOYEES"),
      })
    },
    activeSort() {
      let stages = Object.values(this.statusGoals)
      for (let i = 0; i < stages.length; i++) {
        if (stages[i].id === this.activeStage)
          return stages[i].sort;
      }
    },
    disableBtn() {
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
  methods: {
    sendAgreement() {
      let app = this;
      let slider = window.top.BX.SidePanel.Instance.getTopSlider();
      //slider.showLoader();

      this.goals.forEach(goal => {
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
      });

      window.top.BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'nextStatus', {
        mode: 'class',
        data: {
          params: {
            cardId: this.cardId,
            goals: this.goals,
            activeStage: this.activeStage
          }
        }
      })
          .then((response) => {

            if (response.data?.needShowWarningPopup === 'Y') {

              let content = ``;
              content = content + `
                <div style="display: grid">
                <div class="notification_alert">

                <div class="notification_alert__title">
                    ${app.localize.EMPLOYEES_NOT_ACCESS_GOALS}
                </div>
                  <div class="notification_alert__warning">
              `;

              for (let key in response.data) {
                if (Number(key)) {
                  content = content + `
                    ${response.data[key].title}<br>
                 `;
                }
              }

              content = content + `
              <br><div style="display: grid; margin: 10px">
              ${app.localize.TO_MODULE_ADMINS}
              `;

              for (let key in response.data.moduleAdmins) {
                content = content + `
                    <div style="display: inline-flex; margin-top: 10px">
                    <a class="bp-short-process-step bp-short-process-step-firs" style="margin: 0 0 10px 0">
                    <div class="bp-short-process-step-inner ui-icon ui-icon-common-user">
                    <i style="background-image: url('${app.usersAvatars[key]}')"></i>
                    </div>
                    <a class="process-step-more" href="/company/personal/user/${key}/" style="margin: 6px 0 0 14px">${response.data.moduleAdmins[key]}</a>
                    </div>
                 `;
              }

              content = content + `
                </div></div>
              </div></div></div>
              `;
              let popup = BX.PopupWindowManager.create('users_none_access_to_goals', BX('users_none_access_to_goals'), {
                content: content,
                zIndex: 100, // z-index
                darkMode: false, // окно будет светлым или темным
                draggable: false, // можно двигать или нет
                resizable: false, // можно ресайзить
                width: 700, // Ширина
                lightShadow: true, // использовать светлую тень у окна
                angle: false, // появится уголок
                overlay: {
                  // объект со стилями фона
                  backgroundColor: 'black',
                  opacity: 70
                },
                buttons: [
                  new BX.PopupWindowButton({
                    text: app.localize.INTRODUCED,
                    className: 'ui-btn ui-btn-primary',
                    events: {
                      click: function () {
                        popup.close();
                      }
                    }
                  }),
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

            } else {

              window.top.BX.SidePanel.Instance.close();
              let id = window.top.BX.Main.gridManager.data[0].id;
              window.top.BX.Main.gridManager.getById(id).instance.reloadTable('POST', {
                apply_filter: 'Y',
                clear_nav: 'Y'
              });

            }

          })
          .catch((response) => {
            if (!!response.errors[0]) {
              this.showError(response.errors[0].message)
            }
            slider.closeLoader();
            let id = window.top.BX.Main.gridManager.data[0].id;
            window.top.BX.Main.gridManager.getById(id).instance
                .reloadTable('POST', {apply_filter: 'Y', clear_nav: 'Y'});
          });
    },
    showError(text) {
      var oPopup = new window.BX.PopupWindow('error', window.body, {
        closeIcon: {right: "20px", top: "10px"},
        titleBar: {
          content: BX.create("span", {html: BX.message('ERROR_TITLE'), 'props': {'className': 'error-dialog-title'}})
        },
        autoHide: true,
        zIndex: 5000,
        offsetTop: 1,
        offsetLeft: 0,
        lightShadow: true,
        closeByEsc: true,
        overlay: {
          backgroundColor: 'grey', opacity: '80'
        }
      });
      oPopup.setContent(text);
      oPopup.show();
    },
    openModalComment() {
      var oPopup = new window.BX.PopupWindow('call-decline', window.body, {
        closeIcon: {right: "20px", top: "10px"},
        titleBar: {
          content: window.BX.create("span", {
            html: BX.message('SET_COMMENT'),
            'props': {'className': 'call-decline-title'}
          })
        },
        autoHide: true,
        zIndex: 5000,
        offsetTop: 1,
        offsetLeft: 0,
        lightShadow: true,
        closeByEsc: true,
        overlay: {
          backgroundColor: 'grey', opacity: '80'
        }
      });
      oPopup.setContent(window.BX('commentPopup'));
      oPopup.show();
    },
    sendDecline() {
      let slider = window.top.BX.SidePanel.Instance.getTopSlider();
      slider.showLoader();
      window.top.BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'prevStatus', {
        mode: 'class',
        data: {
          params: {
            cardId: this.cardId,
            goals: this.goals,
            stageToChange: this.stageToChange,
            activeStage: this.activeStage,
            comment: this.comment
          }
        }
      })
          .then(() => {
            window.top.BX.SidePanel.Instance.close();
            let id = window.top.BX.Main.gridManager.data[0].id;
            window.top.BX.Main.gridManager.getById(id).instance
                .reloadTable('POST', {apply_filter: 'Y', clear_nav: 'Y'});
          })
          .catch((response) => {
            if (!!response.errors[0]) {
              this.showError(response.errors[0].message)
            }
            slider.closeLoader();
            let id = window.top.BX.Main.gridManager.data[0].id;
            window.top.BX.Main.gridManager.getById(id).instance
                .reloadTable('POST', {apply_filter: 'Y', clear_nav: 'Y'});
          });
    },
    closeSlider() {
      window.top.BX.SidePanel.Instance.close();
    }
  }
}
</script>