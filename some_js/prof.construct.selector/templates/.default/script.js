'use strict';

// ithive:prof.construct.selector
BX.namespace('Ithive.Prof.Pcs');

BX.Ithive.Prof.Pcs = {

    /* region: Инициализация */
    // Инициализация
    init: function (params) {
        this.params = params;

        this.dealId = params.dealId;
        this.selector = params.selector;
        this.createConstructButton = params.createConstructButton;
        this.angle = params.angle;
        this.menu = params.menu;
        this.menuItems = document.getElementsByClassName('ithive-prof-pcs-menu-item');
        this.selectedConstructType = this.selector.getElementsByClassName('ithive-prof-pcs-menu-item')[0].dataset.constructType;
        this.selectedConstructTypeId = this.selector.getElementsByClassName('ithive-prof-pcs-menu-item')[0].dataset.constructTypeId;
        // Скрытие и отображение меню
        BX.bind(this.selector, 'click', BX.proxy(this.toggleMenuVisibility, this));
        BX.bind(window, 'keyup', BX.proxy(this.closeMenu, this));
        BX.bind(window, 'click', BX.proxy(this.closeMenu, this));

        // Обработка клика на пункты меню
        for (let i = 1; i < this.menuItems.length; i++) {
            BX.bind(this.menuItems[i], 'click', BX.proxy(this.selectMenuItem, this));
        }

        // Обработка нажатия на кнопку добавления нового конструктива
        BX.bind(this.createConstructButton, 'click', BX.proxy(this.createConstruct, this));

        // Пересчет при удалении конструктива
        if (!!params.isFirstShow) {
            BX.addCustomEvent('IthiveProf::needRefreshSelector', this.refreshSelector.bind(this))
        }

        // Стартовая задача минимальной высоты контентной области
        let contentContainer = document.querySelector('div.workarea-content-paddings');
        if (contentContainer) {
            contentContainer.style.minHeight = '999px';
        }

        // Для доступа из других контекстов к selector
        if (!window.BX.Ithive.Prof.Pcs) {
            window.BX.Ithive.Prof.Pcs = this;
        }

        // Стартовая загрузка таблицы конструктивов
        this.refreshConstructTable();
    },
    /* endregion */

    /* region: Обработка меню */
    // Скрытие и отображение меню - click
    toggleMenuVisibility: function (event) {
        if (this.menu.style.display === 'none') {
            this.menu.style.display = 'block';
            this.angle.classList.remove('fa-angle-down');
            this.angle.classList.add('fa-angle-up');
        } else {
            this.menu.style.display = 'none';
            this.angle.classList.remove('fa-angle-up');
            this.angle.classList.add('fa-angle-down');
        }
    },

    // Скрытие и отображение меню - ESC
    closeMenu: function (event) {
        if (event.type === 'keyup' && event.code === 'Escape') {
            if (this.menu.style.display === 'block') {
                this.menu.style.display = 'none';
                this.angle.classList.remove('fa-angle-up');
                this.angle.classList.add('fa-angle-down');
            }
        }
        if (event.type === 'click') {
            if (!event.target.classList.value.includes('pcs')) {
                if (this.menu.style.display === 'block') {
                    this.menu.style.display = 'none';
                    this.angle.classList.remove('fa-angle-up');
                    this.angle.classList.add('fa-angle-down');
                }
            }
        }
        // console.log('SLADCOVICH - ERROR - END');
    },
    /* endregion */

    /* region: Обработка кликов */
    // Обработка клика на пункты меню
    /**
     * Выбор элемента меню в selector
     *
     * Стандартно: работает с выбором пунктов меню в selector меню в списке конструктивов
     * Нестандартно: позволяет перезагрузить список конструктивов и меню на основании действий в других компонентах
     *
     * @param eventOrNode - передается event если выбор из текущего компонента, node если из внешних компонентов
     * @param fromEvent - boolean флаг, если передается в eventOrNode - нода, тогда ставится false, иначе нечего
     */
    selectMenuItem: function (eventOrNode, fromEvent) {

        let newSelectedItem;

        if (fromEvent !== false) {
            for (let i = 0; i < eventOrNode.composedPath().length; i++) {
                if (eventOrNode.composedPath()[i].tagName === 'DIV') {
                    if (eventOrNode.composedPath()[i].classList.contains('ithive-prof-pcs-menu-item')) {
                        newSelectedItem = eventOrNode.composedPath()[i];
                    }
                }
            }
        } else {
            newSelectedItem = eventOrNode;
        }

        let oldSelectedItem = BX('ithive_prof_pcs_selector');
        let oldSelectedItemClone = oldSelectedItem.cloneNode(true);
        let newSelectedItemClone = newSelectedItem.cloneNode(true);

        this.selectedConstructType = newSelectedItemClone.dataset.constructType;
        this.selectedConstructTypeId = newSelectedItemClone.dataset.constructTypeId;

        // Проверяем не эмулировано ли событие программным способом (например: при закрытие слайдера)
        if (eventOrNode.isTrusted === true) {
            this.toggleMenuVisibility();
        }

        BX.findChild(newSelectedItemClone, {class: 'ithive-prof-pcs-indicators'}, true, false).appendChild(this.angle);

        this.refreshConstructTable(oldSelectedItem, newSelectedItemClone);
    },
    /* endregion */

    /* region: AJAX вызовы компонентов */
    // Перерисовка таблицы конструктивов - prof.construct.list
    refreshConstructTable: function (oldSelectedItem, newSelectedItemClone, event) {
        BX.ajax.runComponentAction(
            'ithive:prof.construct.selector',
            'getComponent',
            {
                mode: 'class',
                data: {
                    componentName: 'ithive:prof.construct.list',
                    componentTemplate: '',
                    componentParams: {
                        dealId: this.dealId,
                        constructType: this.selectedConstructType,
                    },
                }
            },
        ).then(function (response) {
            if (oldSelectedItem && oldSelectedItem) {
                oldSelectedItem.innerHTML = '';
                oldSelectedItem.appendChild(newSelectedItemClone);
            }
            let constructList = BX('ithive_prof_construct');
            constructList.innerHTML = '';
            constructList.insertAdjacentHTML('afterbegin', response.data['html']);
        });
    },

    // Обработка нажатия кнопки добавления нового конструктива - prof.construct.list.construct.edit
    createConstruct: function (event) {
        let selectorData = this;
        BX.SidePanel.Instance.open('ithive:prof.construct.list.construct.edit', {
            contentCallback: function (slider) {
                return new Promise(function (resolve, reject) {
                    BX.ajax.runComponentAction(
                        'ithive:prof.construct.selector',
                        'getComponent',
                        {
                            mode: 'class',
                            data: {
                                componentName: 'ithive:prof.construct.list.construct.edit',
                                componentTemplate: '',
                                componentParams: {
                                    dealId: selectorData.dealId,
                                    constructType: selectorData.selectedConstructType,
                                },
                            }
                        },
                    ).then(function (response) {
                        let content = '';
                        let css = response.data.assets.css;
                        let js = response.data.assets.js;
                        css.forEach(function (data) {
                            content = content + '<link href="' + data + '" type="text/css" rel="stylesheet">'
                        });
                        js.forEach(function (data) {
                            content = content + '<script type="text/javascript" src="' + data + '"></script>'
                        });
                        content = content + response.data.html;
                        resolve({
                            html: content,
                        });
                    });
                });
            },
            events: {
                onClose: function (event) {
                    //(слайдер закрыт с сохранением данных)
                    if (event.slider.data.data.status === 'save') {
                        reloadConstructDealInfo(selectorData.dealId);
                        if (event.slider.data.data.constructType) {
                            BX('ithive_prof_pcs_menu').querySelector('div[data-construct-type="' + event.slider.data.data.constructType + '"]').click();
                        }
                        BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
                    }
                }
            },
            cacheable: false,
            animationDuration: 100,
            width: 600,
            label: {
                bgColor: '#cd4c0b',
                opacity: 100
            },
        });
    },

    // Обновление selector
    refreshSelector() {
        let $this = this
        BX.ajax.runComponentAction(
            'ithive:prof.construct.selector',
            'getComponent',
            {
                mode: 'class',
                data: {
                    componentName: 'ithive:prof.construct.selector',
                    componentTemplate: '',
                    componentParams: {
                        DEAL_ID: this.dealId,
                        CONSTRUCT_TYPE_ID: this.selectedConstructTypeId,
                        CONSTRUCT_TYPE: this.selectedConstructType,
                    },
                }
            },
        ).then(function (response) {
            let selector = BX('ithive_prof_selector');
            selector.innerHTML = '';
            selector.insertAdjacentHTML('afterbegin', response.data['html']);
            $this.init({
                dealId: $this.dealId,
                selector: BX('ithive_prof_pcs_selector'),
                createConstructButton: BX('ithive_prof_pcs_create_construct'),
                angle: BX('ithive_prof_pcs_angle'),
                menu: BX('ithive_prof_pcs_menu'),
                isFirstShow: false,
            })
        })
    }
    /* endregion */
}
