<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$APPLICATION->ShowHead();

use Bitrix\Main\UI\Extension;

Extension::load(['ui.forms', 'ui.buttons.icons', 'ui.vue', 'sidepanel']);

?>

<div id="prof_construct_list_construct_result_vue">

    <div class="ithive-prof-pclre">

        <h6 class="ithive-prof-pclre-main-title"><?= GetMessage('TITLE') ?></h6>

        <div class="ithive-prof-pclre-main-content">

            <input-select
                    label="<?= GetMessage('PROJECT_DECISION') ?>"
                    v-model="result.decision"
                    :options="list.decision"
            ></input-select>



            <div v-if="result.decision === additionalData.win || result.decision === additionalData.failure">

                <div v-if="additionalData.solutionType === 'OUR_OFFER' || additionalData.solutionType === 'TOTAL_SOLUTION'">

                    <input-file
                            label="<?= GetMessage('TKP') ?>"
                            :files="result.tkp"
                            logic="TKP"
                            v-model="result.tkp"
                    ></input-file>

                    <input-file
                            label="<?= GetMessage('TO') ?>"
                            :files="result.to"
                            logic="TO"
                            v-model="result.to"
                    ></input-file>

                </div>

                <div v-if="additionalData.solutionType === 'ACTUAL_SALE'">

                    <input-select
                            label="<?= GetMessage('SPECIFICATION') ?>"
                            v-model="result.specification"
                            :options="list.specification"
                    ></input-select>

                </div>

            </div>

            <div v-if="result.decision === additionalData.failure">

                <input-select
                        label="<?= GetMessage('LPR') ?>"
                        v-model="result.lpr"
                        :options="list.lpr"
                ></input-select>

                <input-select
                        label="<?= GetMessage('REASON') ?>"
                        v-model="result.reason"
                        :options="list.reason"
                ></input-select>

            </div>

        </div>

    </div>

    <!-- SUB MENU -->
    <sub-menu></sub-menu>

</div>

<script>

    BX.Vue.component('input-select', {
        template: '' +
            '<div class="ithive-prof-pclre-input-box">' +
            '<label class="ithive-prof-pclre-input-label">{{label}}</label>' +
            '<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ithive-prof-pclre-input-field">' +
            '<div class="ui-ctl-after ui-ctl-icon-angle"></div>' +
            '<select class="ui-ctl-element" v-model="getValue">' +
            '<option v-for="option in options" :value="option.id">{{option.title}}</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '',
        props: [
            'label',
            'options',
            'value',
        ],
        computed: {
            getValue: {
                get() {
                    return this.value;
                },
                set(value) {
                    this.$emit('input', value);
                }
            }
        },
    });

    BX.Vue.component('input-file', {
        template: '' +
            '<div class="ithive-prof-pclre-input-box">' +
            '<label class="ithive-prof-pclre-input-label">{{label}}</label>' +
            '<p class="ithive-prof-pclre-row-file" v-for="file in files">' +
            '<i class="fa fa-file-text-o ithive-prof-pclre-file ithive-prof-pclre-space-right"></i>' +
            '<a href="#" class="ithive-prof-pclre-space-right" data-type="link">' +
            '{{file.name}}' +
            '</a>' +
            '<span class="ithive-prof-pclre-input-label ithive-prof-pclre-space-right">' +
            '{{(file.size/1024/1024).toFixed(2)}}<?= GetMessage('MEGABYTE') ?>' +
            '</span>' +
            '<a href="#" class="ithive-prof-pclre-space-right" @click="removeFile(event.target)">' +
            '<?= GetMessage('DELETE') ?>' +
            '</a>' +
            '</p>' +
            '<br>' +
            '<label class="ui-ctl ui-ctl-file-btn">' +
            '<input type="file" class="ui-ctl-element" multiple @change="loadFile()" name="test">' +
            '<div class="ui-ctl-label-text ithive-prof-pclre-file-button"><?= GetMessage('ADD_FILE') ?></div>' +
            '</label>' +
            '</div>' +
            '',
        props: [
            'label',
            'files',
            'logic'
        ],
        methods: {
            loadFile() {
                let root = this.$root;
                let inputFile = this.$el.lastChild.firstChild.files;
                for (let i = 0; i < inputFile.length; i++) {
                    switch (this.$options.propsData.logic) {
                        case 'TKP':
                            root.result.tkp.push({
                                id: i + '_runtime',
                                lastModified: inputFile[i].lastModified,
                                name: inputFile[i].name,
                                size: inputFile[i].size,
                                type: inputFile[i].type,
                                link: '#',
                            });
                            break;
                        case 'TO':
                            root.result.to.push({
                                id: i + '_runtime',
                                lastModified: inputFile[i].lastModified,
                                name: inputFile[i].name,
                                size: inputFile[i].size,
                                type: inputFile[i].type,
                                link: '#',
                            });
                            break;
                    }
                }
            },
            removeFile(target) {
                let root = this.$root;
                let app = this;
                let targetFileName = target.previousSibling.previousSibling.innerText;
                this.files.forEach(function (data, index) {
                    if (data.name === targetFileName) {
                        switch (app.$options.propsData.logic) {
                            case 'TKP':
                                root.result.tkp.splice(index, 1);
                                break;
                            case 'TO':
                                root.result.to.splice(index, 1);
                                break;
                        }
                    }
                });
            },
        },
        mounted() {
            let root = this.$root;
            let app = this;
            let arLink = app.$el.querySelectorAll('a[data-type="link"]');
            for (let i = 0; i < arLink.length; i++) {
                switch (app.$options.propsData.logic) {
                    case 'TKP':
                        arLink[i].setAttribute('href', root.result.tkp[i].link);
                        break;
                    case 'TO':
                        arLink[i].setAttribute('href', root.result.to[i].link);
                        break;
                }
            }
        }
    });

    BX.Vue.component('sub-menu', {
        template: `
        <div class="ithive-prof-pclre-sub-menu">
            <div class="ithive-prof-pclre-row">
                <button class="ui-btn ui-btn-primary ithive-prof-pcl-standard-button" @click="save(event)" disabled="disabled"><?= GetMessage('SAVE') ?></button>
                <a href="#" class="ui-btn ui-btn-link" @click="close"><?= GetMessage('CANCEL') ?></a>
                <div v-if="this.canDelete">
                <button class="ui-btn ui-btn-secondary ithive-prof-pcl-standard-button ithive-prof-pclre-sub-menu-item-end" @click="deleteResult()"><?= GetMessage('DELETE') ?></button>
                </div>
            </div>
        </div>
        `,
        methods: {
            close() {
                this.$root.closeSlider();
            },
            save(event) {
                this.$root.saveResult();
            },
            deleteResult() {
                this.$root.deleteResult();
            },
            getButton(logic) {
                let $this = this;
                let buttonNode = null;
                switch (logic)
                {
                    case 'save':
                        buttonNode = $this.$el.firstChild.firstChild;
                        break;
                }
                return buttonNode;
            },
            synchronizeSaveButton() {
                let root = this.$root;
                let abilityToSave = false;

                // Если выбрано решение по проекту и $arParams['canSave'] === 'Y'
                if (this.$data.canSave === 'Y' && root.result.decision > 0)
                {
                    abilityToSave = true;
                }

                // Если выбрано решение по проекту и добавлен хотя бы один файл
                if (
                    (
                        this.$data.canSave === 'N'
                        &&
                        root.result.decision > 0
                    )
                    &&
                    (
                        root.result.tkp.length > 0
                        ||
                        root.result.to.length > 0
                    )
                ) {
                    abilityToSave = true;
                }

                // Если выбрано решение по проекту === 'Проигрыш'
                if (root.result.decision === root.additionalData.failure) {

                    if (this.$data.canSave === 'Y') {
                        if (root.result.reason > 0 && root.result.lpr > 0)
                        {
                           abilityToSave = true;
                        } else {
                           abilityToSave = false;
                        }
                    }

                }

                // Если выбрано решение по проекту === 'Не наше'
                if (root.result.decision === root.additionalData.notOur)
                {
                    abilityToSave = true;
                }

                // На основании переменной устанавливаем или удаляем аттрибут disabled
                if (abilityToSave) {
                    this.getButton('save').removeAttribute('disabled');
                } else {
                    this.getButton('save').setAttribute('disabled', 'disabled');
                }
            }
        },
        data() {
            return {
                canSave: '<?=$arParams['canSave']?>',
                canDelete: false
            }
        },
        mounted() {
            let root = this.$root;
            root.subMenu = this;
        }
    });

    var application = BX.Vue.create({
        el: '#prof_construct_list_construct_result_vue',
        data() {
            return {
                component: '<?=$this->getComponent()->getName()?>',
                error: 'error has been occurred - ajax action: ',
                slider: null,
                subMenu: null,
                list: {
                    decision: [],
                    lpr: [],
                    reason: [],
                    specification: [],
                },
                result: {
                    material: <?=intval($arParams['materialId'])?>,
                    decision: null,
                    lpr: null,
                    reason: null,
                    tkp: [],
                    to: [],
                    specification: null,
                    resultDateCreate: null,
                },
                additionalData: {
                    solutionType: '<?=$arParams['solutionType']?>',
                    dealId: <?=intval($arParams['dealId'])?>,
                    win: 0,
                    failure: 0,
                    notOur: 0,
                }
            }
        },
        mounted() {
            this.getSlider();
            this.getResultProjectWorks();
            this.getLPRs();
            this.getReasons();
            if (this.additionalData.solutionType === 'ACTUAL_SALE') {
                this.getSpecifications();
            }
            this.getResult();
        },
        methods: {
            setAdditionalData() {
                let root = this;
                this.list.decision.forEach(function (data, index) {
                    switch (data.code) {
                        case 'WIN':
                            root.additionalData.win = data.id;
                            break;
                        case 'FAILURE':
                            root.additionalData.failure = data.id;
                            break;
                        case 'NOT_OUR':
                            root.additionalData.notOur = data.id;
                            break;
                    }
                });
            },
            getResultProjectWorks() {
                let root = this;
                let action = 'getResultProjectWorks';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {},
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.decision = response.data;
                        root.setAdditionalData();
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getLPRs() {
                let root = this;
                let action = 'getLPRs';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {},
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.lpr = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getReasons() {
                let root = this;
                let action = 'getReasons';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {},
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.reason = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getSpecifications() {
                let root = this;
                let action = 'getSpecifications';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            dealId: root.additionalData.dealId,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.specification = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            async saveResult() {

                /*
                * Из-за того что мы работаем с передачей файлов в компоненте написанном на VUE,
                * Мы не можем использовать BX.runComponentAction - через него невозможно передать файлы,
                * В виду этого в исключении используем нативный fetch от javascript с запросом ajax.php файла компонента
                 */

                let root = this;
                let action = 'saveResult';

                let formData = new FormData();

                formData.append('result', JSON.stringify(this.result));

                let arFileInput = document.querySelectorAll('input[type="file"]');

                if (arFileInput.length > 0) {

                    switch (root.additionalData.solutionType) {

                        case 'OUR_OFFER':

                            // TKP
                            if (arFileInput[0].files.length > 0) {
                                for (let i = 0; i < arFileInput[0].files.length; i++) {
                                    formData.append('tkp_' + i, arFileInput[0].files[i]);
                                }
                            }

                            // TO
                            if (arFileInput[1].files.length > 0) {
                                for (let i = 0; i < arFileInput[1].files.length; i++) {
                                    formData.append('to_' + i, arFileInput[1].files[i]);
                                }
                            }
                            break;

                        case 'TOTAL_SOLUTION':

                            // TKP
                            if (arFileInput[0].files.length > 0) {
                                for (let i = 0; i < arFileInput[0].files.length; i++) {
                                    formData.append('tkp_' + i, arFileInput[0].files[i]);
                                }
                            }

                            // TO
                            if (arFileInput[1].files.length > 0) {
                                for (let i = 0; i < arFileInput[1].files.length; i++) {
                                    formData.append('to_' + i, arFileInput[1].files[i]);
                                }
                            }
                            break;

                        case 'ACTUAL_SALE':

                            /**/
                            break;

                    }

                }

                // Удаляем данные которые более не потребуются
                switch (root.result.decision) {
                    case root.additionalData.win:
                        this.result.lpr = null;
                        this.result.reason = null;
                        break;
                    case root.additionalData.failure:
                        // this.result.tkp = [];
                        // this.result.to = [];
                        this.result.specification = null;
                        break;
                    case root.additionalData.notOur:
                        this.result.lpr = null;
                        this.result.reason = null;
                        this.result.tkp = [];
                        this.result.to = [];
                        this.result.specification = null;
                        break;
                }

                let response = await fetch('<?=$componentPath?>/ajax.php', {method: 'post', body: formData,});
                let result = await response.text();

                if (result.includes('save')) {
                    root.slider.data.data.status = 'save';
                    root.slider.data.data.constructType = '<?=$arParams['constructType']?>';
                    root.closeSlider();
                } else {
                    console.error(root.error + action);
                }

            },
            getResult() {
                let root = this;
                let action = 'getResult';

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            materialId: root.result.material,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.result = response.data;
                        if (root.result.resultDateCreate !== '') {
                            root.subMenu.canDelete = true;
                        }
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            deleteResult() {
                let root = this;
                let action = 'deleteResult';

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            materialId: root.result.material,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.result = response.data;
                        root.slider.data.data.status = 'save';
                        root.closeSlider();
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getSlider() {
                let root = this;
                BX.SidePanel.Instance.openSliders.forEach(function (data, index) {
                    if (data.url === root.component) {
                        root.slider = BX.SidePanel.Instance.openSliders[index];
                    }
                });
            },
            closeSlider() {
                if (this.slider.data.data.status !== 'save') {
                    this.slider.data.data.status = 'cancel'
                }
                this.slider.close();
            },
        },
        watch: {
            'result.decision': {
                handler: function (after, before) {
                    this.subMenu.synchronizeSaveButton();
                },
                deep: true
            },
            'result.tkp': {
                handler: function (after, before) {
                    this.subMenu.synchronizeSaveButton();
                },
                deep: true
            },
            'result.to': {
                handler: function (after, before) {
                    this.subMenu.synchronizeSaveButton();
                },
                deep: true
            },
            'result.lpr': {
                handler: function (after, before) {
                    this.subMenu.synchronizeSaveButton();
                },
                deep: true
            },
            'result.reason': {
                handler: function (after, before) {
                    this.subMenu.synchronizeSaveButton();
                },
                deep: true
            },
        },
    });

</script>
