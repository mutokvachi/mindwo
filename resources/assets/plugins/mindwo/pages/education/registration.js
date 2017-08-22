(function ($) {
    /**
     * Creates jQuery plugin for education course registration
     * @returns DxEduRegistration
     */
    $.fn.DxEduRegistration = function () {
        return this.each(function () {
            if ($(this).data('dx_is_init') == 1) {
                return;
            }

            this.registration = new $.DxEduRegistration($(this));
        });
    };

    /**
     * Class for education course registration
     * @type DxCryptoField 
     */
    $.DxEduRegistration = function (domObject) {
        /**
         * Field's DOM object which is related to this class
         */
        this.domObject = domObject;

        this.groupOptionStack = [];

        this.basket = [];

        // Initializes class
        this.init();
    };

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxEduRegistration.prototype, {
        /**
         * Initializes field
         * @returns {undefined}
         */
        init: function () {
            var self = this;

            if (self.domObject.data('dx_is_init') == 1) {
                return;
            }

            self.domObject.data('dx_is_init', 1);

            show_page_splash(1);

            self.domObject.find('.dx-edu-reg-btn-add-group').click(function () {
                $('.dx-edu-modal-group-select-course').val($(".dx-edu-modal-group-select-course option:first").val());
                $('#dx-edu-modal-group').data('group_id', 0);
                $('#dx-edu-modal-group').modal('show');
            });

            $('.dx-edu-modal-group-select-course').on('change', function () {
                self.loadGroupsBySubject(self, this.value);
            });

            self.initGroupModalGroupSelect(self);
            $('.dx-edu-modal-group-select-course').change();

            $('.dx-edu-modal-group-accept').click(function () {
                self.saveGroup(self);
            });

            $('.dx-edu-modal-group-decline').click(function () {
                self.closeGroupModal(self);
            });

            $('.dx-edu-modal-participant-accept').click(function () {
                self.saveParticipant(self);
            });

            $('.dx-edu-modal-participant-decline').click(function () {
                self.closeParticipantModal(self);
            });

            $('.dx-edu-reg-btn-save').click(function () {
                self.saveAll(self);
            });

            /* self.domObject.find('.dx-edu-datetime-field').each(function(){
                 $(this).val('');
             });
 
             self.domObject.find('.dx-edu-catalog-btn-search').on('click', function () {
                 self.search(self);
             });*/

            hide_page_splash(1);
        },
        saveAll: function (self) {
            show_page_splash(1);

            var data = {
                type: $('.dx-edu-reg-invoice-type').val(),
                address: $('.dx-edu-reg-invoice-address').val(),
                regnr: $('.dx-edu-reg-invoice-regnr').val(),
                bank: $('.dx-edu-reg-invoice-bank').val(),
                swift: $('.dx-edu-reg-invoice-swift').val(),
                account: $('.dx-edu-reg-invoice-account').val(),
                groups: self.basket
            }

            return false;

           /* $.ajax({
                url: DX_CORE.site_url + 'edu/registration/save',
                type: "post",
                data: data,
                success: function (res) {
                    hide_page_splash(1);

                    if (res && res.success && res.success == 1) {
                        self.loadGroupPanel(self, res);
                    } else if (res && res.msg) {
                        notify_err(res.msg);
                    } else {
                        notify_err("Kļūda saglabājot pieteikumu");
                    }
                },
                error: function (err) {
                    hide_page_splash(1);

                    notify_err("Kļūda saglabājot pieteikumu");
                }
            });*/
        },
        saveGroup: function (self) {
            var subjectId = $('.dx-edu-modal-group-select-course').val();
            var groupId = $('.dx-edu-modal-group-select-group').val();

            if (groupId == 0) {
                notify_err("Nav izvēlēta grupa, kurai pieteikt dalībniekus");
                return;
            }

            var oldGroupId = $('#dx-edu-modal-group').data('group_id');

            if (oldGroupId == 0 || oldGroupId != groupId) {
                if (oldGroupId != groupId) {
                    delete self.basket[oldGroupId];
                }

                self.basket[groupId] = {
                    subject_id: subjectId,
                    group_id: groupId
                };

                $.ajax({
                    url: DX_CORE.site_url + 'edu/registration/group/' + groupId,
                    type: "get",
                    success: function (res) {
                        hide_page_splash(1);

                        if (res && res.success && res.success == 1) {
                            self.loadGroupPanel(self, res);

                            $('#dx-edu-modal-group').modal('hide');
                        } else if (res && res.msg) {
                            notify_err(res.msg);
                        } else {
                            notify_err("Kļūda ielādējot datus");
                        }
                    },
                    error: function (err) {
                        hide_page_splash(1);

                        notify_err("Kļūda ielādējot datus");
                    }
                });
            } else {
                $('#dx-edu-modal-group').modal('hide');
            }
        },
        saveParticipant: function (self) {
            var is_coordinator = $('.dx-edu-reg-is-coordinator').bootstrapSwitch('state');

            var data;

            if (is_coordinator) {
                data = {
                    employee_id: 0
                };
            } else {
                data = {
                    name: $('.dx-edu-modal-participant-input-name').val(),
                    lastname: $('.dx-edu-modal-participant-input-lastname').val(),
                    job: $('.dx-edu-modal-participant-input-job').val(),
                    position: $('.dx-edu-modal-participant-input-position').val(),
                    telephone: $('.dx-edu-modal-participant-input-telephone').val(),
                    email: $('.dx-edu-modal-participant-input-email').val()
                };
            }

            var participantId = $('#dx-edu-modal-participant').data('participant_id');
            var groupId = $('#dx-edu-modal-participant').data('group_id');
            var groupTemplate = $('#dx-edu-modal-participant').data('group_template');

            if (participantId == -1) {
                if (typeof self.basket[groupId].participants === 'undefined') {
                    self.basket[groupId].participants = [];
                }

                if (is_coordinator) {

                } else {
                    data.participant_id = self.basket[groupId].participants.length;
                    self.basket[groupId].participants.push(data);
                    participantId = data.participant_id;
                }

                // Enables save button for form
                $('.dx-edu-reg-label-save').hide();
                $('.dx-edu-reg-btn-save').removeClass('disabled');
            } else {
                data.participant_id = participantId;
                self.basket[groupId].participants[participantId] = data;
            }

            self.loadParticipantPanel(self, data, participantId, groupId, groupTemplate);

            $('#dx-edu-modal-participant').modal('hide');
        },
        editParticipant: function (self, template, groupId, participantId) {
            var data = self.basket[groupId].participants[participantId];

            $('.dx-edu-modal-participant-input-name').val(data.name);
            $('.dx-edu-modal-participant-input-lastname').val(data.lastname);
            $('.dx-edu-modal-participant-input-job').val(data.job);
            $('.dx-edu-modal-participant-input-position').val(data.position);
            $('.dx-edu-modal-participant-input-telephone').val(data.telephone);
            $('.dx-edu-modal-participant-input-email').val(data.email);

            $('#dx-edu-modal-participant').data('participant_id', data.participant_id);
            $('#dx-edu-modal-participant').data('group_id', data.group_id);
            $('#dx-edu-modal-participant').data('template', template);

            $('#dx-edu-modal-participant').modal('show');
        },
        editGroup: function (self, template) {
            var data = self.basket[template.data('group_id')];

            $('.dx-edu-modal-group-select-course').val(data.subject_id);
            $('.dx-edu-modal-group-select-course').change();
            $('.dx-edu-modal-group-select-group').val(data.group_id);

            $('#dx-edu-modal-group').data('group_id', data.group_id);
            $('#dx-edu-modal-group').data('template', template);

            $('#dx-edu-modal-group').modal('show');
        },
        deleteGroup: function (self, template) {
            delete self.basket[template.data('group_id')];

            template.remove();

            if ($('.dx-edu-reg-group-container').find('dx-edu-reg-group-panel').length <= 0) {
                $('.dx-edu-reg-group-container').find('.dx-edu-reg-group-panel-empty').show();

                $('.dx-edu-reg-label-save').show();
                $('.dx-edu-reg-btn-save').addClass('disabled');
            }
        },
        deleteParticipant: function (self, template, groupId, participantId, groupTemplate) {
            delete self.basket[groupId].participants[participantId];

            template.remove();

            if (groupTemplate.find('.dx-edu-reg-participant-panel').length <= 0) {
                groupTemplate.find('.dx-edu-reg-participants-panel-empty').show();
            }

            if ($('.dx-edu-reg-participant-panel').length <= 1) {
                $('.dx-edu-reg-label-save').show();
                $('.dx-edu-reg-btn-save').addClass('disabled');
            }
        },
        addParticipant: function (self, groupTemplate, groupId) {
            var is_coordinator = $('.dx-edu-reg-is-coordinator').bootstrapSwitch('state');

            self.closeParticipantModal();

            if (is_coordinator) {
                $('.dx-edu-modal-participant-regular').hide();
                $('.dx-edu-modal-participant-organization').show();
            } else {
                $('.dx-edu-modal-participant-organization').hide();
                $('.dx-edu-modal-participant-regular').show();
            }

            $('#dx-edu-modal-participant').data('participant_id', -1);
            $('#dx-edu-modal-participant').data('group_id', groupId);
            $('#dx-edu-modal-participant').data('group_template', groupTemplate);
            $('#dx-edu-modal-participant').modal('show');
        },
        loadParticipantPanel: function (self, data, participantId, groupId, groupTemplate) {
            if ($('#dx-edu-modal-participant').data('participant_id') == -1) {
                var $template = $($('#dx-edu-reg-participant-panel-temp').html());

                $template.find('.dx-edu-reg-btn-edit-participant').on('click', function () {
                    self.editParticipant(self, $template, groupId, participantId);
                });

                $template.find('.dx-edu-reg-btn-del-participant').on('click', function () {
                    self.deleteParticipant(self, $template, groupId, participantId, groupTemplate);
                });

                groupTemplate.find('.dx-edu-reg-group-participants').append($template);
            } else {
                var $template = $('#dx-edu-modal-participant').data('template');
            }

            $template.data('participant_id', participantId);
            $template.data('group_id', groupId);

            $template.find('.dx-edu-reg-participant').html(data.name + ' ' + data.lastname + ' (' + data.email + ')');

            groupTemplate.find('.dx-edu-reg-participants-panel-empty').hide();
        },
        loadGroupPanel: function (self, res) {
            if ($('#dx-edu-modal-group').data('group_id') == 0) {
                var $template = $($('#dx-edu-reg-group-panel-temp').html());

                $template.find('.dx-edu-reg-btn-edit-group').on('click', function () {
                    self.editGroup(self, $template);
                });

                $template.find('.dx-edu-reg-btn-del-group').on('click', function () {
                    self.deleteGroup(self, $template);
                });

                $template.find('.dx-edu-reg-btn-add-participant').on('click', function () {
                    self.addParticipant(self, $template, res.group.id);
                });

                $('.dx-edu-reg-group-container').append($template);
            } else {
                var $template = $('#dx-edu-modal-group').data('template');
            }

            $template.data('group_id', res.group.id);

            $template.find('.dx-edu-reg-group-title').html(res.group.title);
            $template.find('.dx-edu-reg-group-desc').html(res.group.subject.title);

            if (res.group_start) {
                $template.find('.dx-edu-reg-group-date').html(res.group_start);

                if (res.group_start != res.group_end) {
                    $template.find('.dx-edu-reg-group-date').append(' - ' + res.group_end);
                }
            } else {
                $template.find('.dx-edu-reg-group-date').html('');
            }

            $('.dx-edu-reg-group-container').find('.dx-edu-reg-group-panel-empty').hide();
        },
        closeGroupModal: function (self) {
            $('.dx-edu-modal-group-select-course').val($(".dx-edu-modal-group-select-course option:first").val());
        },
        closeParticipantModal: function (self) {
            $('.dx-edu-modal-participant-input-name').val('');
            $('.dx-edu-modal-participant-input-lastname').val('');
            $('.dx-edu-modal-participant-input-job').val('');
            $('.dx-edu-modal-participant-input-position').val('');
            $('.dx-edu-modal-participant-input-telephone').val('');
            $('.dx-edu-modal-participant-input-email').val('');
        },
        /**
         * Initializes group select box in group modal window
         */
        initGroupModalGroupSelect: function (self) {
            var options = $('.dx-edu-modal-group-select-group option');

            for (var i = 0; i < options.length; i++) {
                var subject_id = $(options[i]).data('subject-id');

                if (!(subject_id in self.groupOptionStack)) {
                    self.groupOptionStack[subject_id] = [];
                }

                self.groupOptionStack[subject_id].push(options[i]);
                $(options[i]).remove();
            }
        },
        /**
         * Loads groups in group modal
         */
        loadGroupsBySubject: function (self, value) {
            var groupSelect = $('.dx-edu-modal-group-select-group');
            var options = groupSelect.find('option').remove();

            if (value in self.groupOptionStack) {
                var groupOptions = self.groupOptionStack[value];

                for (var i = 0; i < groupOptions.length; i++) {
                    groupSelect.append(groupOptions[i]);
                }
            } else {
                groupSelect.append('<option value="0">Kursam nav pieejamu grupu</option>');
            }
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes chat window
    $('.dx-edu-registration-page').DxEduRegistration();
});
