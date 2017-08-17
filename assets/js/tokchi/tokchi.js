/**
 * Tokchi v 1.1.0
 * 
 * Cross-browser input field with MacOS-style "token" / Android-style "chip"
 * support.
 *
 * Copyright (c) 2015, 2016 Thomas Iguchi - https://nobu-games.com/
 * Licensed under MIT, see https://github.com/tiguchi/tokchi/LICENSE.md
 */ 
(function ($) {
    var defaultOptions = {
        allowLineBreaks : false,
        
        autoFocus : true,
        
        /**
         * Callback that gets invoked when the Tokchi instance is ready.
         * 
         * @param tokchi Tokchi instance.
         */ 
        onReady : function (tokchi) {
        },
        
        /**
         * Callback for a change (addition / removal) of tokens in input field.
         * 
         * @param tokchi Tokchi instance.
         */ 
        onChange : function (tokchi) {
        },
        
        /**
         * Callback for text editing event (key up / key down / key press).
         * 
         * @param tokchi Tokchi instance.
         * @param event Key event.
         * @return If <code>false</code> is returned the event will be
         *     suppressed and the typed character is ignored.
         */
        onEdit : function (tokchi, event) {
        },
        
        /**
         * Callback for return button press.
         * 
         * @param tokchi Tokchi instance.
         * @return True in order to suppress return press event propagation
         *      to input field.
         */ 
        onPressReturn : function (tokchi) {
            return true;
        },
        
        /**
         * Keyword search handler.
         *
         * Must call `tokchi.setSearchResult(result)` when search result is available.
         * 
         * @param tokchi Tokchi instance.
         * @param token Keyword to search.
         */ 
        onSearchKeyword : function (tokchi, keyword) {
            tokchi.setSearchResult([{label : keyword}]);
        },
        
        /**
         * Adds a label and optional other content to a token that is about to be
         * added to the input field.
         * 
         * Override this option if your search result object properties differ from
         * the default, i.e. the presence of `tokenObj.label` is expected for
         * the label text. You can also customize the appearance and add more
         * elements to the `tokenHTMLNode`, such as a small profile picture or
         * your own custom close button to remove the token.
         * 
         * @param tokchi Tokchi instance.
         * @param tokenHTMLNode Token node to add label to.
         * @param tokenObj Token data object from search result.
         */ 
        onCreateToken : function (tokchi, tokenHTMLNode, tokenObj) {
            $(tokenHTMLNode).text(tokenObj.label).append(
                $('<span>')
                    .text('×')
                    .addClass(tokchi._options.cssClasses['token-close-button'])
                    .click(function () {
                        tokchi.removeToken(tokenHTMLNode);
                    })
            );
        },
        
        /**
         * Called when a token has been added to the input field.
         * 
         * @param tokchi Tokchi instance.
         * @param tokenObj Token data object from search result.
         */
        onTokenAdded : function (tokchi, tokenObj) {  
        },
        
        /**
         * Handler that get called when a token is edited and needs to be reverted into
         * its text representation. This function is responsible for providing the text
         * representation of the token.
         * 
         * @param tokchi Tokchi instance.
         * @param tokenHTMLNode Token node that is about to be unwrapped.
         * @param tokenObj Token data object from search result.
         * 
         * @return Unwrapped token text.
         */ 
        onUnwrapToken : function (tokchi, tokenHTMLNode, tokenObj) {
            return tokenObj.label;
        },
        
        /**
         * Adds a label and other optional content to the dropdown item of each
         * search result object.
         * 
         * Override this function to create your own customizations for the
         * appearance of each dropdown item.
         * 
         * @param tokchi Tokchi instance.
         * @param itemHTMLNode Dropdown item node to add label to.
         * @param resultItem Token search result object.
         */ 
        onCreateDropdownItem : function (tokchi, itemHTMLNode, resultItem) {
            $(itemHTMLNode).text(resultItem.label);
        },
        
        cssClasses : {
            'dropdown' : 'tokchi-dropdown',
            'dropdown-item' : 'tokchi-dropdown-item',
            'dropdown-item-selected' : 'tokchi-dropdown-item-selected',
            'token' : 'tokchi-token',
            'token-close-button' : 'tokchi-token-close-button'
        },
        
        /**
         * `follows` = Dropdown box will open up under current cursor position
         * `fixed` = Dropdown box will appear under input field
         */ 
        dropdownStyle : 'follows',
        
        /**
         * Regular expression for finding boundary between search keywords.
         * 
         * Can be set to `null` in order to ignore word boundaries. In this
         * case the whole text input at the current cursor position is
         * taken into account, while only previously inserted tokens act
         * as boundaries.
         */ 
        searchKeywordDelimiter : /[\s\u00A0,.:;\?!\)\(\[\]\{\}"`<>+]/
    };
    
    var KEY = {
        ESC : 27,
        BACKSPACE : 8,
        DEL : 46,
        UP : 38,
        DOWN : 40,
        LEFT : 37,
        RIGHT : 39,
        RETURN : 13,
        PG_UP : 33,
        PG_DOWN : 34,
        END : 35,
        HOME : 36
    };

    var selection = {
        get : function () {
            if (window.getSelection) return window.getSelection();
            return document.getSelection();
        },
        
        createRange : function () {
            return document.createRange();
        },
        
        setRange : function (range) {
            var sel = selection.get();
            sel.removeAllRanges();
            sel.addRange(range);

            return sel.getRangeAt(0);
        },
        
        select : function (node) {
            var range = selection.createRange();
            range.selectNode($(node).get(0));
            return selection.setRange(range);
        },
        
        setRangeAfter : function (obj) {
            var sel = selection.get();
            sel.removeAllRanges();
            var range = selection.createRange();
            var el = $(obj).get(0);
            range.setStartAfter(el);
            range.setEndAfter(el);
            sel.addRange(range);
        },
        
        setRangeBefore : function (obj) {
            var sel = selection.get();
            sel.removeAllRanges();
            var range = selection.createRange();
            var el = $(obj).get(0);
            range.setStartBefore(el);
            range.setEndBefore(el);
            sel.addRange(range);
        }
    };

    function forEach (array, callback) {
        for (var i = 0; i < array.length; ++i) {
            callback(i, array[i]);
        }
    }
    
    function calculatePosition (el) {
        console.debug(el);
        var pos = {
            top : 0,
            left : 0
        };

        do {
            $el = $(el);
            pos.top += el.offsetTop + parseInt($el.css('margin-top'))
                + parseInt($el.css('padding-top'))
                + parseInt($el.css('border-top-width'));
            pos.left += el.offsetLeft + parseInt($el.css('margin-left'))
                + parseInt($el.css('padding-left'))
                + parseInt($el.css('border-left-width'));
        } while (el = el.offsetParent);
        
        return pos;
    }
    
    var $window = $(window);

    /**
     * Creates a new Tokchi UI widget.
     * 
     * @param {DOMElement | jQuery result set} input Input field to turn into a Tokchi.
     * @param {object} options Configuration object (see defaultOptions above). Optional.
     */ 
    function Tokchi(input, options) {
        var self = this;
        this._input = $('<div>')
            .attr('contentEditable', true)
            .attr('autocomplete', false)
            .attr('autocorrect', false)
            .attr('spellcheck', false)
            .on('cut paste keydown keyup', function (e) {
                self._onInput(e);
            }).blur(function () {
                self._hideDropdown();
                self._cleanInputMarkup();
                
                // If there's only whitespace left, clear the input field
                // so the optional hint text can reappear
                if (self._input.text().trim().length == 0) {
                    self._input.empty();
                }
            });
        $.each(input.attributes, function (i, attr) {
            self._input.attr(attr.nodeName, attr.nodeValue);
        });
        $(input).replaceWith(this._input);

        this._inputNode = this._input.get(0);
        this._options = $.extend({}, defaultOptions, options);
        this._dropdown = $(this._options.dropdownElement || '<ul/>')
        	.addClass(this._options.cssClasses['dropdown']).hide();

        if (!this._options.dropdownElement)
        	this._input.after(this._dropdown);
        
        var initVal = this._input.attr('data-value');
        
        if (initVal) {
            var optionsBackup = this._options.onChange;
            this._options.onChange = $.noop;
            this.setValue(JSON.parse(initVal));
            this._options.onChange = optionsBackup;
        }

        if (window.MutationObserver) {
            this._mutationObserver = new MutationObserver(function (mutations) {
                var didChange = false;

                forEach(mutations, function (index, mutation) {
                    if (!mutation.removedNodes) return;

                    forEach(mutation.addedNodes, function (index, item) {
                        if ($(item).attr('data-token')) {
                            didChange = true;
                        }
                    });

                    forEach(mutation.removedNodes, function (index, item) {
                        if ($(item).attr('data-token')) {
                            didChange = true;
                        }
                    });
                });
                
                if (didChange) {
                    self._options.onChange(self);
                }
            });
            
            this._mutationObserver.observe(this._inputNode, {
                childList : true
            });
        }

        if (this._options.autoFocus) this._input.focus();
        this._options.onReady(this);
    }
    
    /**
     * Shows the dropdown selection box.
     */ 
    Tokchi.prototype._showDropdown = function () {
        if (!this._dropdownShowing) {            
            var offset = this._input.position();
            var top = offset.top + this._input.outerHeight();
            
            if (this._options.dropdownStyle == 'follows') {
                var rect = selection.get().getRangeAt(0).getClientRects()[0];
                this._dropdown.css({
                    position : 'absolute',
                    width : '',
                    top : top,
                    left : rect.left + $window.scrollLeft()
                });
            } else if (this._options.dropdownStyle == 'fixed') {
                this._dropdown.css({
                    position : 'absolute',
                    top : top,
                    left : offset.left,
                    width : this._input.width()
                });
            }

            this._dropdown.show();
            this._dropdownShowing = true;
        }

        var self = this;

        if (this._options.dropdownStyle == 'follows') {
            // Delay to wait for DOM tree layout update
            window.setTimeout(function () {
                var inputOffset = self._input.offset();
                var inputWidth = self._input.width();
                var ddOffset = self._dropdown.offset();
                var ddWidth = self._dropdown.width();

                if ((ddOffset.left + ddWidth) > (inputOffset.left + inputWidth)) {
                    self._dropdown.css({
                        left : inputOffset.left + (inputWidth - ddWidth)
                    });
                }
            }, 0);
        }
        
        this._dropdownIndex = 0;
        this._blurSafeGuard = 0;
        this._dropdown.scrollTop(0);
    };
    
    /**
     * Hides the dropdown.
     * 
     * @param force (optional) If set to true closing the dropdown is enforced even
     *        when the mouse hovers over an item.
     */ 
    Tokchi.prototype._hideDropdown = function (force) {
        if (!force && this._blurSafeGuard > 0) return;
        this._dropdown.hide();
        this._dropdownShowing = false;
        delete this._currentSearchKey;
    };
    
    /**
     * Moves the dropdown selection by a specific amount of positions.
     * 
     * @param amount Positive value to move down, negative value to move up.
     */ 
    Tokchi.prototype._moveDropdownSelection = function (amount) {
        var newIndex = this._dropdownIndex + amount;
        var max = $(this._dropdown).children().length;
        if (newIndex >= max) newIndex = max - 1;
        if (newIndex < 0) newIndex = 0;
        var self = this;
        
        $(this._dropdown).children().each(function (i, child) {
            var jthis = $(this);
            
            if (i == newIndex) {
                if (jthis.attr('data-disabled')) return;
                jthis.addClass(self._options.cssClasses["dropdown-item-selected"]);                
                var ddHeight = self._dropdown.outerHeight(false);
                var ddPaddingTop = parseInt(self._dropdown.css('padding-top'));
                var ddTop = self._dropdown.scrollTop();
                var ddBottom = ddHeight + ddTop;
                var elTop = jthis.position().top;
                var elHeight = jthis.outerHeight(true);

                if (ddBottom < elTop + elHeight) {
                    self._dropdown.scrollTop(elTop - ddHeight + elHeight + ddPaddingTop);
                } else if (ddTop > elTop) {
                    self._dropdown.scrollTop(elTop - ddPaddingTop);
                }
            } else {
                jthis.removeClass(self._options.cssClasses["dropdown-item-selected"]);
            }
        });

        this._dropdownIndex = newIndex;
    };
    
    /**
     * Selects a dropdown item by its index number.
     * 
     * @param index Dropdown item index number.
     */ 
    Tokchi.prototype._setDropdownSelection = function (index) {
        var child = this._dropdown.children().get(this._dropdownIndex);
        
        if (child)
            $(child).removeClass(this._options.cssClasses["dropdown-item-selected"]);

        child = $(this._dropdown.children().get(index));
        
        if (!child.attr('data-disabled'))
            child.addClass(this._options.cssClasses["dropdown-item-selected"]);

        this._dropdownIndex = index;
    };
    
    /**
     * Inserts the currently selected dropdown item as a token
     * into the input field.
     */ 
    Tokchi.prototype._pickDropdownItem = function (index) {
        delete this._blurSafeGuard;
        var jitem = $(this._dropdown.children().get(index));
        if (jitem.attr('data-disabled')) return;
        var tokenObj = JSON.parse(jitem.attr('data-token'));
        var chip = this._createToken(tokenObj);
        
        if (this._currentSearchNodeStartOffset || this._currentSearchNodeEndOffset) {
            var toReplace = this._currentSearchNode.splitText(this._currentSearchNodeStartOffset);
            var offs = this._currentSearchNodeEndOffset - this._currentSearchNodeStartOffset;
            if (offs && offs < toReplace.nodeValue.length) toReplace.splitText(offs);
            $(toReplace).replaceWith(chip);
        } else {
            $(this._currentSearchNode).replaceWith(chip);
        }

        delete this._currentSearchNode;
        this._padAndSetCursorAfterToken(chip);
        this._options.onTokenAdded(this, tokenObj);
    };
    
    /**
     * Adds whitespace after an inserted token / chip and sets the
     * input cursor after that if the input has currently focus.
     * 
     * @param {DOMNodeElement} chip Token / chip that was inserted.
     */ 
    Tokchi.prototype._padAndSetCursorAfterToken = function (chip) {
        var space = document.createTextNode('\u00A0');
        chip.after(space);
        
        if (this._dropdownShowing) {
            this._input.focus();
            selection.setRangeAfter(space);
            this._hideDropdown();
        }

        this._cleanInputMarkup();

        if (!this._mutationObserver) {
            this._options.onChange(this);
        }
    }
    
    /**
     * Restores token click handlers and attributes
     * in case tokens have been pasted into the input field.
     */ 
    Tokchi.prototype._repairPastedTokens = function () {
        var self = this;

        $('*[data-token]', this._inputNode).each(function (i, token) {
            var jtoken = $(token);
            var tokenObj = JSON.parse(jtoken.attr('data-token'));
            jtoken.replaceWith(self._createToken(tokenObj));
        });

        this._cleanInputMarkup();
    };
    
    /**
     * Creates a token / chip based on a description object.
     *
     * @param tokenObj Token description object (will be automatically
     *      serialized as JSON string and added as `data-token` attribute).
     * @return Created token DOM object.
     */ 
    Tokchi.prototype._createToken = function (tokenObj) {
        var self = this;

        var chip = $('<div/>')
            .attr('data-token', JSON.stringify(tokenObj))
            .attr('contentEditable', false)
            .addClass(this._options.cssClasses.token)
            .each(function (i, token) {
                self._options.onCreateToken(self, token, tokenObj);
            });

        if (!this._mutationObserver) {
            chip.bind('DOMNodeRemoved', function () {
                self._options.onChange(self);
            });
        }

        return chip;
    };

    /**
     * Retrieves the closest token relative to the current input cursor position.
     * 
     * @param ltr Boolean that indicates whether to look from left to right or
     *      the other way.
     * @return Token DOM object on success or nothing.
     */ 
    Tokchi.prototype._getClosestToken = function (ltr) {
        var range = selection.get().getRangeAt(0);
        var offset = range.startOffset;
        if (!range.collapsed) return;
        var editedNode = range.startContainer;

        if (editedNode == this._inputNode) {
            editedNode = this._inputNode.childNodes[offset - 1];
            offset = 0;
        }

        if (!editedNode || (editedNode.nodeType == 3
            && editedNode.parentNode == this._inputNode
            // Allow editing text nodes of sufficient length
            && (offset > 1 || editedNode.nodeValue.match(/[^\s\u00A0]/)))) return;

        do {
            var jeditedNode = $(editedNode);
            var tokenObj = jeditedNode.attr('data-token');
            if (tokenObj) return jeditedNode;
            editedNode = (ltr ? editedNode.nextSibling : editedNode.previousSibling) || editedNode.parentNode;
        } while (editedNode && this._inputNode != editedNode 
                 && !(editedNode.nodeType == 3 && editedNode.nodeValue));
    };
    
    /**
     * Cleans the input field markup to deal with various cross-browser
     * quirks and also for removing any special formattings in case
     * rich content has been pasted.
     */ 
    Tokchi.prototype._cleanInputMarkup = function () {
        var self = this;

        $('> *:not([data-token])', this._input).each(function (i, node) {
            if (node.nodeType != 3) {
                var jnode = $(node);
                jnode.replaceWith(jnode.text() || '\u00A0');
            }
        });
        
        $('*[data-token]', this._input).each(function (i, node) {
            if (i == 0 && (!node.previousSibling || node.previousSibling.nodeType != 3)) {
                $(node).before(document.createTextNode('\u00A0'));
            }

            if (!node.nextSibling || node.nextSibling.nodeType != 3) {
                $(node).after(document.createTextNode('\u00A0'));
            }
        });
        
        forEach(this._inputNode.childNodes, function (i, node) {
            if (node.nodeType == 3 && (!node.nodeValue.length || node.nodeValue.match(/^[\s\u00A0]{2,}$/))) {
                node.nodeValue = '\u00A0';
            }
        });

        this._inputNode.normalize();
    };

    /**
     * Input / cut / paste handler method.
     *
     * @param e Event object.
     */ 
    Tokchi.prototype._onInput = function (e) {
        var self = this;

        if (e.type == 'paste') {
            setTimeout(function () {
                self._cleanInputMarkup();
                self._repairPastedTokens();
            }, 0);
            return;
        }
        
        switch (e.keyCode) {
            case KEY.ESC:
                if (this._dropdownShowing) {
                    e.preventDefault();
                    this._hideDropdown();
                }
                return;

            case KEY.HOME:
            case KEY.END:
                if (e.type == 'keydown') {
                    this._hideDropdown(true);
                }
                return;

            case KEY.LEFT:
                if (e.type == 'keydown') {
                    this._hideDropdown(true);
                    var token = this._getClosestToken(false);
                    
                    if (token) {
                        selection.setRangeBefore(token);
                    }
                }
                return;
                
            case KEY.RIGHT:
                if (e.type == 'keydown') {
                    this._hideDropdown(true);
                    var token = this._getClosestToken(true);
                    
                    if (token) {
                        selection.setRangeBefore(token);
                    }
                }
                return;

            case KEY.DEL:
                if (e.type == 'keydown') {
                    this._cleanInputMarkup();
                    var token = this._getClosestToken(true);

                    if (token) {
                        tokenObj = JSON.parse(token.attr('data-token'));
                        var label = document.createTextNode(this._options.onUnwrapToken(this, token, tokenObj));
                        token.replaceWith(label);
                        e.preventDefault();
                        this._hideDropdown(true);
                    }
                }
                break;
                
            case KEY.BACKSPACE:
                if (e.type == 'keydown') {
                    this._cleanInputMarkup();
                    var token = this._getClosestToken(false);

                    if (token) {
                        tokenObj = JSON.parse(token.attr('data-token'));
                        var label = document.createTextNode(this._options.onUnwrapToken(this, token, tokenObj));
                        var next = token.get(0).nextSibling;
                        token.replaceWith(label);

                        if (next) {
                            next.textContent = next.textContent.trim();
                            selection.setRangeBefore(next);
                        } else {
                            selection.setRangeAfter(label);
                        }

                        e.preventDefault();
                        this._hideDropdown(true);
                    }
                }
                break;
                
            case KEY.PG_DOWN:
            case KEY.DOWN:
                if (this._dropdownShowing) {
                    e.preventDefault();
                    if (e.type == 'keydown')
                        this._moveDropdownSelection(e.keyCode == KEY.DOWN ? 1 : this._dropdownItemsPerPage);
                    return;
                }
                break;
                
            case KEY.PG_UP:
            case KEY.UP:
                if (this._dropdownShowing) {
                    e.preventDefault();
                    if (e.type == 'keydown')
                        this._moveDropdownSelection(e.keyCode == KEY.UP ? -1 : -this._dropdownItemsPerPage);
                    return;
                }
                break;
                
            case KEY.RETURN:
                if (this._dropdownShowing) {
                    e.preventDefault();
                    if (e.type == 'keyup')
                        this._pickDropdownItem(this._dropdownIndex);
                    return;
                } else if (this._options.onPressReturn(this)) {
                    e.preventDefault();
                    return;
                }
                break;
        }
        
        if (this._input.text().trim().length == 0) {
            this._dropdown.empty();
            this._hideDropdown();
        } else {
            // User is currently typing something into the input field
            if (this._options.onEdit(this, e) === false) {
                e.preventDefault();
                return;
            }
            
            var range = selection.get().getRangeAt(0);
            var editedNode = range.startContainer;

            if (editedNode.nodeType == 3 && this._inputNode == editedNode.parentNode) {
                if (e.type == 'keydown') return;
                this._searchToken(range, editedNode);
            } else if (this._inputNode != editedNode) {
                // Avoid that label of token gets edited
                e.preventDefault();
            }
        }
    };
    
    /**
     * Triggers the search for a token based on the specified token text node.
     * 
     * @param token Token text node to search for. Will be automatically
     *      replaced with a token when user makes a selection from the dropdown list.
     */ 
    Tokchi.prototype._searchToken = function (range, node) {
        var searchKey;
        
        if (this._options.searchKeywordDelimiter) {
            this._currentSearchNodeStartOffset = Math.max(range.startOffset - 1, 0);
            this._currentSearchNodeEndOffset = Math.min(range.endOffset, node.textContent.length);

            for (; this._currentSearchNodeStartOffset > 0; --this._currentSearchNodeStartOffset) {
                if (node.textContent.charAt(this._currentSearchNodeStartOffset)
                    .match(this._options.searchKeywordDelimiter)) {
                    break;
                }
            }
            
            for (; this._currentSearchNodeEndOffset < node.textContent.length; ++this._currentSearchNodeEndOffset) {
                if (node.textContent.charAt(this._currentSearchNodeEndOffset)
                    .match(this._options.searchKeywordDelimiter)) {
                    break;
                }
            }

            searchKey = node.textContent
                .substring(this._currentSearchNodeStartOffset, this._currentSearchNodeEndOffset);

            if (this._options.debug) {
                console.debug('Start = ' + this._currentSearchNodeStartOffset 
                              + ', End = ' + this._currentSearchNodeEndOffset
                              + ', SearchKey = ' + searchKey);
            }
        } else {
            delete this._currentSearchNodeStartOffset;
            delete this._currentSearchNodeEndOffset;       
            searchKey = node.textContent;
        }
        
        this._currentSearchNode = node;
        searchKey = searchKey.replace('\u00A0', ' ').trim();
        if (this._currentSearchKey == searchKey) return;
        this._currentSearchKey = searchKey;
        this._options.onSearchKeyword(this, searchKey);
    };
    
    /**
     * Gives focus to the tokchi input field and positions the cursor
     * at the end.
     */
    Tokchi.prototype.focus = function () {
        this._input.focus();
        var last = this._input.children().last();
        selection.setRangeAfter(last);
    };

    /**
     * Programmatically adds a token to the input field.
     * 
     * @param {object} tokenObj Token description object.
     */ 
    Tokchi.prototype.addToken = function (tokenObj) {
        var chip = this._createToken(tokenObj);
        this._input.append(chip);
        this._padAndSetCursorAfterToken(chip);
        
        if (!this._mutationObserver) {
            this._options.onChange(this);
        }

        this._options.onTokenAdded(this, tokenObj);
    };
    
    /**
     * Removes a token node from the input field.
     * 
     * @param {DOMElementNode} tokenHTMLNode Token node to remove.
     */ 
    Tokchi.prototype.removeToken = function (tokenHTMLNode) {
        $(tokenHTMLNode).remove();
        this._cleanInputMarkup();
    };
    
    /**
     * Sets the dropdown style option.
     * 
     * @param {string} style Either `fixed` or `following` (see defaultOptions).
     */ 
    Tokchi.prototype.setDropdownStyle = function (style) {
        this._options.dropdownStyle = style;
        this._hideDropdown(true);
    }
    
    /**
     * Callback for `options.onSearchKeyword` handler function when
     * results are ready for populating the auto-completion dropdown box.
     *
     * @param items Array of result objects.
     */ 
    Tokchi.prototype.setSearchResult = function (items) {
        var dd = this._dropdown;
        dd.empty();

        if (!items || !items.length) {
            this._hideDropdown(true);
            return;
        }
        
        var self = this;
        var itemHeight;

        $.each(items, function(i, item) {
            var ddItem = $('<li/>')
                .addClass(self._options.cssClasses["dropdown-item"])
                .css({
                    'white-space' : 'nowrap' 
                })
                .mouseover(function () {
                    self._blurSafeGuard++;
                    self._setDropdownSelection(i);
                })
                .mouseout(function () {
                    self._blurSafeGuard--;
                })
                .click(function () {
                    self._pickDropdownItem(i);
                }).attr('data-token', JSON.stringify(item));
            
            if (item.disabled)
                ddItem.attr('data-disabled', true);
            self._options.onCreateDropdownItem(self, ddItem, item);
            if(!item.disabled && i == 0)
                ddItem.addClass(self._options.cssClasses["dropdown-item-selected"]);
            dd.append(ddItem);
            
            if (!itemHeight) {
                itemHeight = ddItem.outerHeight();
            }
        });

        this._dropdownItemsPerPage = Math.floor(this._dropdown.outerHeight() / itemHeight);
        this._showDropdown();
    };
    
    /**
     * Sets the value of the input field.
     * 
     * @param value Optional string or an array of strings and / or token objects (see getValue).
     * 		  May also be null to clear the input field.
     */
    Tokchi.prototype.setValue = function (value) {
        if (typeof value == 'string') {
            this._input.text(value);
            return;
        }

        this._input.empty();
        if (!value) return;
        var self = this;

        forEach(value, function (index, item) {
            if (typeof item == 'string') {
                self._input.append(document.createTextNode(item));
            } else {
                self.addToken(item);
            }
        });
    };

    /**
     * Gets a list of all token objects from the input field.
     * 
     * @return array of token objects.
     */ 
    Tokchi.prototype.getTokens = function () {
        var result = [];
        
        $('*[data-token]', this._inputNode).each(function (index, token) {
            result.push(JSON.parse($(token).attr('data-token')));
        });
        
        return result;
    };
    
    /**
     * Gets the current input text as an `array` of strings and
     * token `object`s in the order of appearance.
     * HTML formattings will be ignored.
     * 
     * @return array of strings and token objects.
     */ 
    Tokchi.prototype.getValue = function () {
        this._inputNode.normalize();
        var result = [];

        forEach(this._inputNode.childNodes, function (index, node) {
            var jnode = $(node);
            var token = jnode.attr('data-token');

            if (token) {
                result.push(JSON.parse(token));
            } else {
                var text = jnode.text().replace('\u0A00', ' ').trim();
                if (text) result.push(text);
            }
        });

        return result;
    };
    
    // Register new jQuery function
    $.fn.tokchify = function (options) {
        var func, args, result;
        var first = true;

        if (typeof options == 'string') {
            func = Tokchi.prototype[options];
            if (options[0] === '_' || typeof func != 'function')
                throw 'Unknown method ' + options;
            args = Array.prototype.slice.call(arguments, 1);
        }
        
        this.each(function () {
            var $this = $(this);
            var tokchi = $this.data('tokchi');

            if (!tokchi) {
                tokchi = new Tokchi(this, options);
                // Tokchi replaces the original DOM node, that's why we
                // need to store the instance there
                tokchi._input.data('tokchi', tokchi);
                // In case the tokchify method has been chained with
                // another tokchify call we also need to store the instance
                // as data for the original (replaced) DOM elements
                $this.data('tokchi', tokchi);
            }
            
            if (func) {
                var tmpResult = func.apply(tokchi, args);
                if (first) result = tmpResult;
                first = false;
            }
        });

        return func ? result : this;
    };
}(jQuery));
