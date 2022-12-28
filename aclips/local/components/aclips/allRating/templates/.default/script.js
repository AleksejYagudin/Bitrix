BX.namespace('Aclips.BaseGrid.List')

BX.Aclips.BaseGrid.List = {
    gridId: null,
    pathTemplates: {},
    init: function(params) {
        // @TODO проверка существования и корректности значений
        this.gridId = params.gridId
        this.pathTemplates = params.pathTemplates
    },
    openProfile: function(userId) {
        let profileUrl = this.pathTemplates.userProfile.replace('#ID#', userId)
        BX.SidePanel.Instance.open(profileUrl);

    },
    showRemoveUserConfirmation: function (userId) {
        let self = this

        BX.PopupWindowManager.create("popup-remove-user-confirmation", null, {
            titleBar: 'Удаление пользователя',
            content: 'Вы точно хотите удалить пользователя?',
            autoHide: true,
            overlay: {
                backgroundColor: 'black',
                opacity: 500
            },
            buttons: [
                new BX.PopupWindowButton({
                    text: 'Да',
                    id: 'accept-btn',
                    className: 'ui-btn ui-btn-success',
                    events: {
                        click: function () {
                            let callback = () => {
                                self.reloadGrid()
                                this.popupWindow.close()
                            }

                            self.removeUser(userId, callback)
                        }
                    }
                }),
                new BX.PopupWindowButton({
                    text: 'Отмена',
                    id: 'decline-btn',
                    className: 'ui-btn ui-btn-danger',
                    events: {
                        click: function () {
                            this.popupWindow.close()
                        }
                    }
                })
            ],
            events: {
                onPopupClose: function () {
                    this.destroy()
                }
            }
        }).show()
    },
    removeUser: function (userId, callback) {
        BX.ajax.runComponentAction('aclips:allRating', 'deleteUser', {
            mode: 'class',
            data: {
                userId: userId,
            }
        }).then(response => {
            // Выполняется при успешном ответе
            if (callback && {}.toString.call(callback) === '[object Function]') {
                callback()
            }
        }, reject => {
            // Выполняется при не успешном ответе
            let errorMessages = reject.errors.map(e => e.message).join('\n')
            alert(errorMessages)
        })
    },
    reloadGrid() {
        let grid = BX.Main.gridManager.getById(this.gridId);

        if (grid) {
            grid.instance.reload()
        }
    }

}
