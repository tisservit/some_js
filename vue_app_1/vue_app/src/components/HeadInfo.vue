<template>
  <div class="block-process">
    <div class="title-block-process">{{ localize.ABOUT_PROCESS }}</div>
    <div class="block-process-info">
      <div class="sub-block-info-user">
        <span class="sub-block-info-title">{{ localize.ASSESSED }}</span>
        <div class="sub-block-info-employee">
          <div class="sub-block-info-inline" v-if="userInfo.ICON">
            <img class="sub-block-info-icon" :src="userInfo.ICON">
          </div>
          <div class="sub-block-info-inline">
                        <span class="sub-block-info-name">
                            <a :href="'/company/personal/user/' + userInfo.ID + '/'">{{ userInfo.NAME }}</a></span>
            <span class="sub-block-info-position">{{ userInfo.POSITION }}</span>
            <span class="sub-block-info-department">{{ userInfo.DEPARTMENT }}</span>
          </div>
        </div>
      </div>
      <div class="sub-block-info-user" v-show="delegateInfo.ID>0">
        <span class="sub-block-info-title">{{ localize.DELEGATE }}</span>
        <div class="sub-block-info-employee">

          <div class="sub-block-info-inline" v-if="delegateInfo.ICON">
            <img class="sub-block-info-icon" :src="delegateInfo.ICON">
          </div>
          <div class="sub-block-info-inline">
                        <span class="sub-block-info-name">
                            <a :href="'/company/personal/user/' + delegateInfo.ID + '/'">{{
                                delegateInfo.NAME
                              }}</a></span>
            <span class="sub-block-info-position">{{ delegateInfo.POSITION }}</span>
            <span class="sub-block-info-department">{{ delegateInfo.DEPARTMENT }}</span>
          </div>
          <div class="delete-delegate" v-if = "canEditDelegate" @click="deleteDelegate()"></div>
        </div>
      </div>
      <div class="sub-block-info-user" v-if = "canEditDelegate" v-show="delegateInfo.ID<=0">
        <span class="sub-block-info-title">{{ localize.DELEGATE }}</span>
        <div class="sub-block-info-employee">
          <div class="sub-block-info-inline">
            <img class="sub-block-info-icon">
          </div>
          <div class="sub-block-info-inline">
                        <span class="sub-block-info-name" style="line-height: 34px;">
                            <div id="choice-delegate"></div></span>
          </div>
        </div>
      </div>
      <div class="sub-block-info">
        <disable-input v-for="(process, index) in processInfo"
                       :title="process.title"
                       :value="process.value"
                       :key="'process_info_' + index"
        />
      </div>
    </div>
  </div>
</template>

<script>
import {mapState} from 'vuex';
import DisableInput from "./controls/DisableInput";

export default {
  name: "HeadInfo",
  components: {
    DisableInput
  },
  computed: {
    ...mapState([
      'processInfo',
      'userInfo',
      'delegateInfo',
      'cardId',
      'selectorId',
      'canEditDelegate',
    ]),
    localize() {
      // noinspection JSAnnotator
      return Object.freeze({
        'ABOUT_PROCESS': BX.message("ABOUT_PROCESS"),
        'ASSESSED': BX.message("ASSESSED"),
        'DELEGATE': BX.message("DELEGATE"),
        'DELETE_DELEGATE_POPUP_TITLE': BX.message("DELETE_DELEGATE_POPUP_TITLE"),
        'DELETE_DELEGATE_POPUP_CONTENT': BX.message("DELETE_DELEGATE_POPUP_CONTENT"),
        'DELETE_DELEGATE_POPUP_YES': BX.message("DELETE_DELEGATE_POPUP_YES"),
        'DELETE_DELEGATE_POPUP_CANCEL': BX.message("DELETE_DELEGATE_POPUP_CANCEL"),
        'SET_DELEGATE_POPUP_TITLE': BX.message("SET_DELEGATE_POPUP_TITLE"),
        'SET_DELEGATE_POPUP_CONTENT': BX.message("SET_DELEGATE_POPUP_CONTENT"),
        'SET_DELEGATE_POPUP_YES': BX.message("SET_DELEGATE_POPUP_YES"),
        'SET_DELEGATE_POPUP_CANCEL': BX.message("SET_DELEGATE_POPUP_CANCEL"),
      })
    }
  },
  mounted() {
    setTimeout( this.initSelector, 100);
  },
  methods: {
    initSelector() {
      let $this = this
      this.getSelector();
      BX.addCustomEvent('BX.Main.User.SelectorController:select', function (e) {
        if(e.selectorId===$this.selectorId){
          let dialog = BX('bx-selector-dialog-'+$this.selectorId)
          if(!!dialog){
            let closeButton = dialog.querySelector('.popup-window-close-icon')
            if(!!closeButton){
              closeButton.click()
            }
          }
          $this.setDelegate($this.cardId, e.item.entityId)
        }
      })
    },
    deleteDelegate() {
      let $this = this
      new BX.PopupWindow('delete-delegate-popup', window.body, {
        autoHide: true,
        offsetTop: 1,
        offsetLeft: 0,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: {
          backgroundColor: 'white', opacity: '80'
        },
        titleBar: {
          content: BX.create("p", {html: $this.localize.DELETE_DELEGATE_POPUP_TITLE})
        },
        content: $this.localize.DELETE_DELEGATE_POPUP_CONTENT,
        buttons: [
          new BX.PopupWindowButton({
            text: $this.localize.DELETE_DELEGATE_POPUP_YES,
            className: "ui-btn ui-btn-primary",
            events: {
              click: function () {
                this.popupWindow.destroy();
                this.popupWindow.close();
                BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'deleteDelegate', {
                  mode: 'class',
                  data: {
                    params: {
                      cardId: $this.cardId,
                      userId: $this.delegateInfo.ID,
                    }
                  }
                })
                    .then((response) => {
                      $this.reloadCard()
                    })
                    .catch(error => {
                      console.log(error);
                    });
              }
            }
          }),
          new BX.PopupWindowButton({
            text: $this.localize.DELETE_DELEGATE_POPUP_CANCEL,
            className: "ui-btn ui-btn-light",
            events: {
              click: function () {
                this.popupWindow.destroy();
                this.popupWindow.close();

              }
            }
          })
        ]
      }).show();
    },
    setDelegate(cardId, userId) {
      let $this = this
      new BX.PopupWindow('set-delegate-popup', window.body, {
        autoHide: true,
        offsetTop: 1,
        offsetLeft: 0,
        lightShadow: true,
        closeIcon: true,
        closeByEsc: true,
        overlay: {
          backgroundColor: 'white', opacity: '80'
        },
        titleBar: {
          content: BX.create("p", {html: $this.localize.SET_DELEGATE_POPUP_TITLE})
        },
        content: $this.localize.SET_DELEGATE_POPUP_CONTENT,
        buttons: [
          new BX.PopupWindowButton({
            text: $this.localize.SET_DELEGATE_POPUP_YES,
            className: "ui-btn ui-btn-primary",
            events: {
              click: function () {
                this.popupWindow.destroy();
                this.popupWindow.close();
                BX.ajax.runComponentAction('ithive.goalsbazhen:edit.card.goals', 'setDelegate', {
                  mode: 'class',
                  data: {
                    params: {
                      cardId: cardId,
                      userId: userId,
                    }
                  }
                })
                    .then((response) => {
                      $this.reloadCard()
                    })
                    .catch(error => {
                      console.log(error);
                    });
              }
            }
          }),
          new BX.PopupWindowButton({
            text: $this.localize.SET_DELEGATE_POPUP_CANCEL,
            className: "ui-btn ui-btn-light ",
            events: {
              click: function () {
                this.popupWindow.destroy();
                this.popupWindow.close();

              }
            }
          })
        ]
      }).show();
    },
    getSelector() {
      let $this = this
      BX.ajax({
        url: templateFolder + '/select.php',
        data: {
          id: $this.selectorId,
          depend: [],
          isDep: false,
        },
        dataType: 'html',
        method: 'POST',
        timeout: 30,
        async: false,
        processData: true,
        scriptsRunFirst: false,
        cache: false,
        onsuccess: data => {
          BX($this.selectorId).innerHTML = data;
        },
        onfailure: error => {
          console.log(error);
        }
      });
    },
    reloadCard: function (){
      let id = window.top.BX.Main.gridManager.data[0].id;
      window.top.BX.SidePanel.Instance.reload();
      window.top.BX.Main.gridManager.getById(id).instance
          .reloadTable('POST', { apply_filter: 'Y', clear_nav: 'Y' });
    }
  }
}
</script>