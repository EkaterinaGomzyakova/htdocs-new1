// noinspection JSAnnotator

var phpconsole;

class Phpconsole {
    // private
    #component;
    #filesList;
    #discardedBxceIds;
    #editorPanels;
    #descriptionPanels;
    #BXCodeEditors;
    #detailContentIdToBxceId;
    #exceptionCodes;

    // public
    logErrorsToConsole = true;

    constructor() {
        this.#component = 'wl:phpconsole';
        this.#discardedBxceIds = [];
        this.#exceptionCodes = {
            'FILE_ID_NOT_FOUND': 1,
            'FILE_CONTENTS_NOT_FOUND': 2,
            'REQUIRED_REQUEST_ARGUMENTS_MISSING': 3
        };
        $('.adm-info-message-wrap').hide();

        this.afterTabUpdate(false);

        let idRegex = /^tab_cont_(tab\d+)$/;
        let $detailContent = $('#editTab_layout .adm-detail-tab.adm-detail-tab-active');
        if (!$detailContent)
            return;
        let tabId = $detailContent.attr('id');
        if (!tabId)
            return;
        tabId = tabId.match(idRegex);
        if (!tabId)
            return;
        tabId = tabId[1];
        $detailContent = $('#' + tabId);

        $(document).on('click', '.adm-detail-files-content__index_list .adm-detail-files-content__index_item a', (e) => {
            this.loadFile($(e.target).attr('data-filekey'));
        });

        $(document).on('click', '.adm-detail-tab', (e) => {
            setTimeout(() => {
                this.afterTabChange(e);
            }, 150);
        });

        $(document).on('click', '.adm-detail-tab-close', () => {
            setTimeout(() => {
                this.afterTabUpdate(false);
            }, 80);
        });

        $(document).on('click', '.adm-detail-tab-plus', () => {
            setTimeout(() => {
                this.afterTabUpdate(true);
            }, 350);
        });

        if ($detailContent)
            setTimeout(() => this.#tabAlter($detailContent), 400);
    }

    logErr (e) {
        if (this.logErrorsToConsole) console.error(e);
    }

    #getEditorContent(bxceId) {
        return this.#BXCodeEditors[bxceId].GetValue();
    }

    #setEditorContent(bxceId, content) {
        this.#BXCodeEditors[bxceId].SetValue(content);
        this.#BXCodeEditors[bxceId].UpdateDisplay(true);
    }

    #getContentVars(content) {
        let title = '';
        let fileId = '';
        let arContent = content.split("\n");
        let titleRegex = /^\s*\/\/title:\s*(.*)\s*$/;
        let arTitle = arContent[0].match(titleRegex);
        if (arTitle) {
            title = arTitle[1].trim();
            arContent.shift();
        }
        let fileIdRegex = /^\s*\/\/fileId:\s*(.*)\s*$/;
        let arFileId = arContent[0].match(fileIdRegex);
        if (arFileId) {
            fileId = arFileId[1].trim();
            arContent.shift();
        }
        return {
            'TITLE': title,
            'FILE_ID': fileId,
            'CONTENT': arContent.join('\n')
        };
    }

    #setContentVars(content, title, fileId) {
        let obContent = this.#getContentVars(content);
        let arParts = [];
        if (title.trim())
            arParts.push('//title: ' + title.trim());
        if (fileId.trim())
            arParts.push('//fileId: ' + fileId.trim());
        arParts.push(obContent.CONTENT);
        return arParts.join('\n');
    }

    async #showFilesPanel() {
        try {
            let response = await BX.ajax.runComponentAction(
                this.#component,
                'loadIndex', {
                    mode: 'class',
                }
            );
            if (response.data)
                $(this.#filesList).html(response.data.html);
            else
                alert(BX.message('AUTH_FAIL'));
        } catch (e) {
            this.logErr(e);
            alert(e.errors[0].message);
        } finally {
            $('#tab_files').show();
        }
    }

    #hideFilesPanel() {
        $('#tab_files').hide();
    }

    async loadFile(fileId) {
        try {
            let newContent = await this.#loadFile(fileId);
            $('#tab_cont_tab_plus').click();
            setTimeout(() => {
                this.afterTabUpdate(true);
                let keys = Object.keys(this.#detailContentIdToBxceId);
                let key = keys[keys.length - 1];
                let bxceId = this.#detailContentIdToBxceId[key];
                this.#setEditorContent(bxceId, newContent);
                let $detailContent = $('#' + key);
                this.#tabAlter($detailContent);
            }, 250);
        } catch (e) {
            this.logErr(e);
            alert(e.errors[0].message);
        } finally {
            $('#tab_files').hide();
        }
    }

    async #loadFile(fileId) {
        try {
            let response = await BX.ajax.runComponentAction(
                this.#component,
                'loadFile', {
                    mode: 'class',
                    data: {
                        FILE_ID: fileId
                    },
                }
            );
            if (response.data)
                return response.data;
            else
                alert(BX.message('AUTH_FAIL'));
        } catch (e) {
            this.logErr(e);
            alert(e.errors[0].message);
        }
    }

    async #fileReload() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);
        if (obContent.FILE_ID) {
            try {
                let editorContent = await this.#loadFile(obContent.FILE_ID);
                this.#setEditorContent(bxceId, editorContent);
            } catch (e) {}
        }
        setTimeout(() => this.#tabAlterByBxceId(bxceId), 200);
    }

    async #fileSave() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);

        if (!obContent.TITLE) {
            let newTitle = prompt(BX.message("SET_NAME_PROMPT") + ":", obContent.TITLE);
            if (newTitle) {
                content = this.#setContentVars(obContent.CONTENT, newTitle, obContent.FILE_ID)
            } else {
                alert(BX.message('SAVE_FILE_ERROR_NO_FILE_NAME'));
                setTimeout(() => this.#tabAlterByBxceId(bxceId), 200);
                return;
            }
        }
        try {
            let response = await BX.ajax.runComponentAction(
                this.#component,
                'saveFile', {
                    mode: 'class',
                    data: {
                        FILE_DATA: content
                    },
                }
            );
            if (response.data)
                content = response.data;
            else
                alert(BX.message('AUTH_FAIL'));
        } catch (e) {
            this.logErr(e);
            let code = e.errors[0].code;
            if (code === this.#exceptionCodes['FILE_ID_NOT_FOUND']) {
                if (confirm(e.errors[0].message + '. ' + BX.message('SAVE_AS_NEW_FILE_PROMPT'))) {
                    obContent = this.#getContentVars(content);
                    content = this.#setContentVars(obContent.CONTENT, obContent.TITLE, '');
                    try {
                        let response = await BX.ajax.runComponentAction(
                            this.#component,
                            'saveFile', {
                                mode: 'class',
                                data: {
                                    FILE_DATA: content
                                },
                            }
                        );
                        if (response.data)
                            content = response.data;
                        else
                            alert(BX.message('AUTH_FAIL'));
                    } catch (e) {
                        this.logErr(e);
                        alert(e.errors[0].message);
                    } finally {
                        this.#setEditorContent(bxceId, content);
                        setTimeout(() => this.#tabAlterByBxceId(bxceId), 200);
                    }
                }
            } else {
                alert(e.errors[0].message);
            }
        } finally {
            this.#setEditorContent(bxceId, content);
            setTimeout(() => this.#tabAlterByBxceId(bxceId), 200);
        }
    }

    async #fileDelete() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);

        if (obContent.FILE_ID) {
            try {
                let response = await BX.ajax.runComponentAction(
                    this.#component,
                    'unlink', {
                        mode: 'class',
                        data: {
                            FILE_ID: obContent.FILE_ID
                        },
                    }
                );
                if (!response.data)
                    alert(BX.message('AUTH_FAIL'));
            } catch (e) {
                this.logErr(e);
                alert(e.errors[0].message);
            } finally {
                let content = this.#setContentVars(obContent.CONTENT, obContent.TITLE, '');
                this.#setEditorContent(bxceId, content);
                setTimeout(() => this.#tabAlterByBxceId(bxceId), 200);
            }
        }
    }

    async #descriptionSave() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);
        try {
            let response = await BX.ajax.runComponentAction(
                this.#component,
                'saveMeta', {
                    mode: 'class',
                    data: {
                        FILE_ID: obContent.FILE_ID,
                        DESCRIPTION: $(this.#descriptionPanels[bxceId]).find('.adm-detail-file-description-content').val(),
                    },
                }
            );
            if (!response.data)
                alert(BX.message('AUTH_FAIL'));
        } catch (e) {
            this.logErr(e);
            alert(e.errors[0].message);
        } finally {
            $(this.#descriptionPanels[bxceId]).hide();
            setTimeout(() => this.#tabAlterByBxceId(bxceId), 200);
        }
    }

    #descriptionCancel() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        $(this.#descriptionPanels[bxceId]).hide();
    }


    afterTabUpdate(insertTab) {
        let codeEditorFound = false;
        let codeEditors = {};
        let detailContentIdToBxceId = {};
        let idRegex = /query(\d+)/;
        let discarded = [];
        let bxceId = '';
        for (bxceId in top.BXCodeEditors) {
            if (!this.#discardedBxceIds.includes(bxceId)) {
                codeEditors[bxceId] = top.BXCodeEditors[bxceId];
                codeEditorFound = true;
                let tabId = 'tab' + codeEditors[bxceId].arConfig.textareaId.match(idRegex)[1];
                detailContentIdToBxceId[tabId] = bxceId;
            }
            discarded.push(bxceId);
        }
        this.#discardedBxceIds = discarded;
        if (codeEditorFound) {
            this.#BXCodeEditors = codeEditors;
            this.#detailContentIdToBxceId = detailContentIdToBxceId;
        }


        this.#editorPanels = {};
        this.#descriptionPanels = {};

        $('.adm-detail-content-wrap .adm-detail-content:not(#tab_plus) .adm-detail-content-item-block').each((e, elm) => {
            this.#createItemBlockToolbar(elm);
        });

        if (insertTab) {
            let keys = Object.keys(this.#detailContentIdToBxceId);
            let key = keys[keys.length - 1];
            let $detailContent = $('#' + key);
            this.#tabAlter($detailContent);
        }

        if (codeEditorFound) {
            this.#filesList = BX.create({
                tag: 'div',
                props: {
                    className: "adm-detail-files-content",
                },
            });

            $('#editTab_tabs').prepend(
                BX.create('div', {
                    props: {
                        className: "adm-detail-tab",
                    },
                    children: [
                        BX.create('div', {
                            props: {
                                className: "adm-detail-files",
                            },
                            events: {
                                click: BX.proxy(this.#showFilesPanel, this),
                            },
                            children: [
                                BX.create({
                                    tag: 'i',
                                    props: {
                                        className: "fa fa-files-o",
                                    },
                                }),
                            ]
                        }),
                        BX.create('div', {
                            props: {
                                className: "adm-detail-files-wrapper",
                                id: "tab_files",
                                style: "display:none;",
                            },
                            children: [
                                BX.create({
                                    tag: 'div',
                                    props: {
                                        className: "files-panel-title ui-ctl ui-ctl-after-icon",
                                    },
                                    children: [
                                        BX.create({
                                            tag: 'h3',
                                            text: BX.message('SELECT_FILE_HEADER')
                                        }),
                                        BX.create({
                                            tag: 'a',
                                            props: {
                                                className: 'files-panel-icon-close ui-ctl-after ui-ctl-icon-clear',
                                            },
                                            events: {
                                                click: BX.proxy(this.#hideFilesPanel, this),
                                            },
                                        }),
                                    ],
                                }),
                                this.#filesList,
                            ],
                        }),
                    ]
                })
            );
        }
    }

    afterTabChange(e) {
        let tabId = $(e.currentTarget).attr('id');
        if (!tabId)
            return;
        let idRegex = /^tab_cont_(tab\d+)$/;
        tabId = tabId.match(idRegex);
        if (!tabId)
            return;
        let $detailContent = $('#' + tabId[1]);

        if (!$detailContent[0]) {
            $detailContent = $('#editTab_layout .adm-detail-tab.adm-detail-tab-active');
            if (!$detailContent)
                return;
            tabId = $detailContent.attr('id');
            if (!tabId)
                return;
            tabId = tabId.match(idRegex);
            if (!tabId)
                return;
            $detailContent = $('#' + tabId[1]);
        }

        if ($detailContent)
            this.#tabAlter($detailContent);
    }

    async #tabAlterByBxceId (bxceId) {
        const bxceIdTodetailContent = Object.entries(this.#detailContentIdToBxceId)
            .reduce((obj, [key, value]) => ({ ...obj, [value]: key }), {});
        let $detailContent = $('#' + bxceIdTodetailContent[bxceId]);
        await this.#tabAlter($detailContent);
    }

    async #tabAlter($detailContent) {
        let tabId = $detailContent.attr('id');
        let $itemBlock = $detailContent.find('.adm-detail-content-item-block');
        let $fileActions = $itemBlock.find('.adm-detail-file-actions');

        let bxceId = this.#detailContentIdToBxceId[tabId];
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);

        let hasFileEntry = false;

        if (obContent.FILE_ID) {
            try {
                let response = await BX.ajax.runComponentAction(
                    this.#component,
                    'loadMeta', {
                        mode: 'class',
                        data: {
                            FILE_ID: obContent.FILE_ID
                        },
                    }
                );
                if (response.data)
                    hasFileEntry = true;
                else
                    alert(BX.message('AUTH_FAIL'));
            } catch (e) {
                hasFileEntry = false;
            }
        }

        let actionReload = BX.create('a', {
            props: {
                className: "adm-detail-file-action adm-detail-file-action-reload",

            },
            attrs: {
                href: "#",
            },
            events: {
                click: BX.proxy(this.#fileReload, this),
            },
            children: [
                BX.create({
                    tag: 'i',
                    props: {
                        className: "fa fa-repeat",
                    },
                }),
                BX.create({
                    tag: 'span',
                    text: ' ' + BX.message("CONTENT_LOAD"),
                }),
            ]
        });

        let actionSave = BX.create('a', {
            props: {
                className: "adm-detail-file-action adm-detail-file-action-save",
            },
            events: {
                click: BX.proxy(this.#fileSave, this),
            },
            children: [
                BX.create({
                    tag: 'i',
                    props: {
                        className: "fa fa-upload",
                    },
                }),
                BX.create({
                    tag: 'span',
                    text: ' ' + BX.message("CONTENT_SAVE"),
                }),
            ]
        });

        let actionRename = BX.create('a', {
            props: {
                className: "adm-detail-file-action adm-detail-file-action-rename",
            },
            events: {
                click: BX.proxy(this.#fileRename, this),
            },
            children: [
                BX.create({
                    tag: 'i',
                    props: {
                        className: "fa fa-edit",
                    },
                }),
                BX.create({
                    tag: 'span',
                    text: ' ' + BX.message("FILE_NAME"),
                }),
            ]
        });

        let actionDescription = BX.create('a', {
            props: {
                className: "adm-detail-file-action adm-detail-file-action-description",
            },
            events: {
                click: BX.proxy(this.#fileDescription, this),
            },
            children: [
                BX.create({
                    tag: 'i',
                    props: {
                        className: "fa fa-file-text-o",
                    },
                }),
                BX.create({
                    tag: 'span',
                    text: ' ' + BX.message("FILE_DESCRIPTION"),
                }),
            ]
        });

        let actionDelete = BX.create('a', {
            props: {
                className: "adm-detail-file-action adm-detail-file-action-delete",
            },
            events: {
                click: BX.proxy(this.#fileDelete, this),
            },
            children: [
                BX.create({
                    tag: 'i',
                    props: {
                        className: "fa fa-unlink",
                    },
                }),
                BX.create({
                    tag: 'span',
                    text: ' ' + BX.message("FILE_DELETE"),
                }),
            ]
        });

        let actions;
        if (hasFileEntry) {
            actions = BX.create('p', {
                props: {
                    className: "adm-detail-file-actions",
                },
                children: [
                    actionReload,
                    actionSave,
                    actionRename,
                    actionDescription,
                    actionDelete,
                ],
            });
        } else {
            actions = BX.create('p', {
                props: {
                    className: "adm-detail-file-actions",
                },
                children: [
                    actionSave,
                    actionRename,
                ],
            });
        }

        if ($fileActions) {
            $fileActions.replaceWith(actions);
        } else
            $itemBlock.prepend(actions);

        return actions;
    }

    #getEditorId(element) {
        let $parentElement = $(element);
        if (!$parentElement.hasClass('adm-detail-content'))
            $parentElement = $parentElement.parents('.adm-detail-content');
        let tabId = $parentElement.attr('id');
        return this.#detailContentIdToBxceId[tabId];
    }


    #fileRename() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);
        let newTitle = prompt(BX.message("SET_NAME_PROMPT") + ":", obContent.TITLE);
        if (newTitle) {
            content = this.#setContentVars(obContent.CONTENT, newTitle, obContent.FILE_ID);
            this.#setEditorContent(bxceId, content);
        }
    }

    async #fileDescription() {
        let bxceId = this.#getEditorId(BX.proxy_context);
        let content = this.#getEditorContent(bxceId);
        let obContent = this.#getContentVars(content);
        if ($(this.#descriptionPanels[bxceId]).css('display') === 'none') {
            try {
                let response = await BX.ajax.runComponentAction(
                    this.#component,
                    'loadMeta', {
                        mode: 'class',
                        data: {
                            FILE_ID: obContent.FILE_ID
                        },
                    }
                );
                if (response.data) {
                    let meta = JSON.parse(response.data);
                    $(this.#descriptionPanels[bxceId]).find('.adm-detail-file-description-content').val(meta.DESCRIPTION);
                    $(this.#descriptionPanels[bxceId]).show();
                } else
                    alert(BX.message('AUTH_FAIL'));
            } catch (e) {
                this.logErr(e);
                alert(e.errors[0].message);
            }
        } else {
            $(this.#descriptionPanels[bxceId]).hide();
        }
    }

    #createItemBlockToolbar(itemBlock) {
        let actions = BX.create('p', {
            props: {
                className: "adm-detail-file-actions",
            },
            children: [
                BX.create('a', {
                    props: {
                        className: "adm-detail-file-action adm-detail-file-action-reload",

                    },
                    attrs: {
                        href: "#",
                    },
                    events: {
                        click: BX.proxy(this.#fileReload, this),
                    },
                    children: [
                        BX.create({
                            tag: 'i',
                            props: {
                                className: "fa fa-repeat",
                            },
                        }),
                        BX.create({
                            tag: 'span',
                            text: ' ' + BX.message("CONTENT_LOAD"),
                        }),
                    ]
                }),
                BX.create('a', {
                    props: {
                        className: "adm-detail-file-action adm-detail-file-action-save",
                    },
                    events: {
                        click: BX.proxy(this.#fileSave, this),
                    },
                    children: [
                        BX.create({
                            tag: 'i',
                            props: {
                                className: "fa fa-upload",
                            },
                        }),
                        BX.create({
                            tag: 'span',
                            text: ' ' + BX.message("CONTENT_SAVE"),
                        }),
                    ]
                }),
                BX.create('a', {
                    props: {
                        className: "adm-detail-file-action adm-detail-file-action-rename",
                    },
                    events: {
                        click: BX.proxy(this.#fileRename, this),
                    },
                    children: [
                        BX.create({
                            tag: 'i',
                            props: {
                                className: "fa fa-edit",
                            },
                        }),
                        BX.create({
                            tag: 'span',
                            text: ' ' + BX.message("FILE_NAME"),
                        }),
                    ]
                }),
                BX.create('a', {
                    props: {
                        className: "adm-detail-file-action adm-detail-file-action-description",
                    },
                    events: {
                        click: BX.proxy(this.#fileDescription, this),
                    },
                    children: [
                        BX.create({
                            tag: 'i',
                            props: {
                                className: "fa fa-file-text-o",
                            },
                        }),
                        BX.create({
                            tag: 'span',
                            text: ' ' + BX.message("FILE_DESCRIPTION"),
                        }),
                    ]
                }),
                BX.create('a', {
                    props: {
                        className: "adm-detail-file-action adm-detail-file-action-delete",
                    },
                    events: {
                        click: BX.proxy(this.#fileDelete, this),
                    },
                    children: [
                        BX.create({
                            tag: 'i',
                            props: {
                                className: "fa fa-unlink",
                            },
                        }),
                        BX.create({
                            tag: 'span',
                            text: ' ' + BX.message("FILE_DELETE"),
                        }),
                    ]
                }),
            ]
        });
        itemBlock.prepend(actions);

        let descriptionPanel = BX.create('div', {
            props: {
                className: "adm-detail-file-description",
                style: "display: none;",
            },
            children: [
                BX.create('textarea', {
                    props: {
                        className: "adm-detail-file-description-content",
                    },
                }),
                BX.create('p', {
                    props: {
                        className: "adm-detail-file-description-panel",
                    },
                    children: [
                        BX.create('a', {
                            props: {
                                className: "adm-detail-file-description-action adm-detail-file-description-action-save",
                            },
                            events: {
                                click: BX.proxy(this.#descriptionSave, this),
                            },
                            children: [
                                BX.create({
                                    tag: 'i',
                                    props: {
                                        className: "fa fa-check",
                                    },
                                }),
                                BX.create({
                                    tag: 'span',
                                    text: ' ' + BX.message('DESCRIPTION_SAVE'),
                                }),
                            ]
                        }),
                        BX.create('a', {
                            props: {
                                className: "adm-detail-file-description-action adm-detail-file-description-action-cancel",
                            },
                            events: {
                                click: BX.proxy(this.#descriptionCancel, this),
                            },
                            children: [
                                BX.create({
                                    tag: 'i',
                                    props: {
                                        className: "fa fa-ban",
                                    },
                                }),
                                BX.create({
                                    tag: 'span',
                                    text: ' ' + BX.message('DESCRIPTION_CANCEL'),
                                }),
                            ]
                        }),
                    ],
                }),
            ]
        });
        itemBlock.prepend(descriptionPanel);

        let editorId = this.#getEditorId(itemBlock);
        this.#editorPanels[editorId] = actions;
        this.#descriptionPanels[editorId] = descriptionPanel;
    }
}

$(() => {
    phpconsole = new Phpconsole();
});
