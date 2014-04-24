(function ($) {
    var manager = window.arcana_content_manager = {};

    manager.openSeparateEditor = function () {
        var editing = false;
        $(document).find('[data-content-id]').each(function () {
            if (editing) return;

            var $raptor = $(this).data('ui-raptor');
            if ($raptor.enabled) {
                alert('Please save or cancel your modifications, before editing separately editable contents.');
                editing = true;
            }
        });

        if (editing) return;

        $('#arcana_separate_contents').dialog({
            modal: true,
            width: $(window).width()-180,
            height: $(window).height()-180,
            closeText: 'Cancel',
            position: {
                my: 'center top',
                at: 'center top+70',
                collision: 'none none'
            },
            beforeClose: function () {
                var editing = false;
                $(this).find('[data-content-id]').each(function () {
                    if (editing) return;

                    var $raptor = $(this).data('ui-raptor');
                    if ($raptor.enabled) {
                        alert('Please save or cancel your modifications, before closing popup.');
                        editing = true;
                    }
                });

                if (editing) return false;
            }
        });
    };

    $(function() {
        var plugins = {
            save: {
                plugin: 'saveJson'
            },
            saveJson: {
                url: manager.contentSaveUrl,
                postName: 'contents',
                type: 'put',
                id: function() {
                    return this.raptor.getElement().data('content-id');
                }
            }
        };

        var options = {
            block: {
                plugins: plugins,
                disabledPlugins: ['logo', 'dockToElement', 'statistics', 'clickButtonToEdit']
            },
            inline: {
                plugins: plugins,
                disabledPlugins: ['logo', 'dockToElement', 'statistics',
                    'clickButtonToEdit', 'guides', 'alignCenter', 'alignJustify',
                    'alignLeft', 'alignRight', 'hrCreate', 'clearFormatting',
                    'embed', 'insertFile', 'tagMenu', 'tableCreate',
                    'tableDeleteColumn', 'tableDeleteRow', 'tableInsertColumn',
                    'tableInsertRow', 'tableMergeCells', 'tableSplitCells',
                    'floatLeft', 'floatRight', 'floatNone'
                ]
            },
            anchor: {
                plugins: plugins,
                disabledPlugins: ['logo', 'dockToElement', 'statistics',
                    'clickButtonToEdit', 'guides', 'alignCenter', 'alignJustify',
                    'alignLeft', 'alignRight', 'hrCreate', 'clearFormatting',
                    'embed', 'insertFile', 'tagMenu', 'tableCreate',
                    'tableDeleteColumn', 'tableDeleteRow', 'tableInsertColumn',
                    'tableInsertRow', 'tableMergeCells', 'tableSplitCells',
                    'floatLeft', 'floatRight', 'floatNone', 'linkCreate',
                    'linkRemove'
                ]
            },
            plaintext: {
                plugins: plugins,
                disabledPlugins: ['logo', 'dockToElement', 'statistics',
                    'clickButtonToEdit', 'guides', 'alignCenter', 'alignJustify',
                    'alignLeft', 'alignRight', 'hrCreate', 'clearFormatting',
                    'embed', 'insertFile', 'tagMenu', 'tableCreate',
                    'tableDeleteColumn', 'tableDeleteRow', 'tableInsertColumn',
                    'tableInsertRow', 'tableMergeCells', 'tableSplitCells',
                    'floatLeft', 'floatRight', 'floatNone', 'linkCreate',
                    'linkRemove', 'textBold', 'textItalic', 'textStrike',
                    'textSizeDecrease', 'textSizeIncrease', 'textSub', 'textSuper',
                    'textUnderline', 'fontFamilyMenu', 'colorMenuBasic',
                    'textBlockQuote', 'listOrdered', 'listUnordered'
                ]
            }
        };

        $('[data-content=block]').raptor(options.block);
        $('[data-content=inline]').raptor(options.inline);
        $('[data-content=anchor]').raptor(options.anchor);
        $('[data-content=plaintext]').raptor(options.plaintext);
        
        $(document).on('contextmenu', '[data-content]:not(.raptor-editing)', function () {
            $(this).data('ui-raptor').enableEditing();

            return false;
        }).on('click', 'a, button', function () {
            if ($(this).find('[contenteditable=true]').length !== 0) {
                return false;
            }
        });

        $('.arcana-separate-contents-btn').click(function () {
            manager.openSeparateEditor();
        });
    });
})(jQuery);
