/*
 * The MIT License (MIT)
 
 Copyright (c) 2014 Lucas Drapeau
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
 */


(function($) {
//plugin name
    var pluginName = "inputNumber";
    //default plugins settings here
    var defaultSettings = {
        inputClass: 'input-number',
        containerClass: 'input-number-container',
        arrowUpClass: 'input-number-arrow arrow arrow-up',
        arrowDownClass: 'input-number-arrow arrow arrow-down',
        integerOnly: true,
        defaultMin: -Number.MAX_VALUE,
        defaultMax: Number.MAX_VALUE,
        defaultStep: 1
    };
//private properties and methods    
    var members = {
        _$this: null,
        _$newInputContainer: null,
        _$newInput: null,
        _$arrowUp: null,
        _$arrowDown: null,
        _max: 1,
        _min: -1,
        _currentValue: 0,
        _step: 1,
        _isActive: false,
        _dispatchEvent: function(event, data) {
            this._$this.trigger(pluginName + '.' + event, data);
        },
        _call: function(name, args) {
            this._$this[pluginName](name, args);
        },
        _generate: function() {
            var that = this;
            //init some variables
            that._max = that._settings.defaultMax;
            that._min = that._settings.defaultMin;
            that._step = that._settings.defaultStep;
            //create the container span for the new input
            this._$newInputContainer = $('<span class="' + this._settings.containerClass + '"></span>');
            this._$this.after(this._$newInputContainer);
            //insert the new input
            this._$newInput = $('<input type="text">');
            this._$newInputContainer.append(this._$newInput)
            //insert the two arrows used for increasing / decreasing the value
            this._$arrowUp = $('<span class="' + this._settings.arrowUpClass + '"></span>')
            this._$newInputContainer.append(this._$arrowUp)
            this._$arrowDown = $('<span class="' + this._settings.arrowDownClass + '"></span>')
            this._$newInputContainer.append(this._$arrowDown)

            //copy attributes
            this._copyAttributes();

            //add some classes to the new input
            this._$newInput.addClass(this._settings.inputClass);
            //hide the original input
            this._$this.hide();
            //tells the plugin that it can't generate the fake input since it's already been done
            this._isActive = true;
            //events extravaganza
            this._$arrowUp.on('click.' + pluginName, function(e) {
                that._onIncrease(e);
            });
            this._$arrowDown.on('click.' + pluginName, function(e) {
                that._onDecrease(e);
            });
            this._$newInput.on('keyup.' + pluginName, function(e) {
                //if we remove this condition, the onchange event will be sent on every key press. This would be a interesting behavior, but it's not the standard one for html5 inputs
                if (e.keyCode == 38 || e.keyCode == 40) {
                    e.preventDefault();
                    that._onVisibleChange(e);
                    return false;
                }
            });
            this._$newInput.on('keydown.' + pluginName, function(e) {
                //arrow up and down update the false input, but hold on triggering anything else until the keyup event
                if (e.keyCode == 38) {
                    e.preventDefault();
                    that._onIncreaseVisible(e);
                    return false;
                }
                if (e.keyCode == 40) {
                    e.preventDefault();
                    that._onDecreaseVisible(e);
                    return false;
                }
            });
            this._$newInput.on('change.' + pluginName, function(e) {
                that._onChangeNew(e);
                that._$this.trigger('change');
            });
            this._$this.on('change.' + pluginName, function(e) {
                that._onChangeOld(e);
            });

        },
        //delete the goddamn plugin
        _delete: function() {
            this._$newInputContainer.remove();
            this._$this.off('change.' + pluginName);
            this._$this.show();
            this._isActive = false;
        },
        //change the value if it goes beyond min or max value
        _checkMaxMin: function(val) {
            if (val > this._max) {
                return this._max;
            }
            if (val < this._min) {
                return this._min;
            }
            return val;
        },
        //copy the value of the old input to the new
        _copyValueToNew: function() {
            this._currentValue = this._$this.val();
            this._$newInput.val(this._$this.val());
        },
        //copy the value of the new input back to the old
        _copyValueToOld: function() {
            this._currentValue = this._$newInput.val();
            this._$this.val(this._$newInput.val());
        },
        //sanitize inputs
        _parseNumber: function(val) {
            var retVal = 0;
            if (this._settings.integerOnly)
                retVal = parseInt(val);
            else
                retVal = parseFloat(val);
            if (isNaN(retVal))
                return false;
            return retVal;
        },
        //copy every attributes from the old input to the new one
        _copyAttributes: function() {
            var that = this;
            //parse every attributes of the original input
            $.each(this._$this[0].attributes, function(i, attrib) {
                var name = attrib.name;
                var value = attrib.value;
                switch (name) {
                    case('max'):
                        that._max = that._parseNumber(value) || that._max;
                        break;
                    case('min'):
                        that._min = that._parseNumber(value) || that._min;
                        break;
                    case('step'):
                        that._step = that._parseNumber(value) || that._step;
                        break;
                    case('type'):
                    case('value'):
                    case('name'):
                    case('id'):
                        break;
                    default:
                        that._$newInput.attr(name, value);
                        break;
                }
            });
            //add a value if none exist
            if (this._$this.val() == false)
                this._$this.val(this._currentValue);
            else
                this._currentValue = this._parseNumber(this._$this.val()) || this._currentValue;
            this._$newInput.val(this._$this.val());
        },
        //constructor, sort of
        init: function(obj) {
            //get the original input
            this._$this = $(obj);
            if (!this._isActive)
                this._generate();
        },
        _onIncrease: function(e) {
            this._$this.val(this._parseNumber(this._$this.val()) + this._step);
            this._$this.trigger('change');
        },
        _onDecrease: function(e) {
            this._$this.val(this._parseNumber(this._$this.val()) - this._step);
            this._$this.trigger('change');
        },
        _onIncreaseVisible: function(e) {
            this._$newInput.val(this._parseNumber(this._$newInput.val()) + this._step);
            this._$newInput.val(this._checkMaxMin(this._$newInput.val()));
        },
        _onDecreaseVisible: function(e) {
            this._$newInput.val(this._parseNumber(this._$newInput.val()) - this._step);
            this._$newInput.val(this._checkMaxMin(this._$newInput.val()));
        },
        _onVisibleChange: function(e) {
            this._$this.val(this._parseNumber(this._$newInput.val()));
            this._$this.trigger('change');
        },
        _onChangeNew: function(e) {
            this._$newInput.val(this._parseNumber((this._$newInput.val())) || this._currentValue);
            this._copyValueToOld();
        },
        _onChangeOld: function(e) {
            var parsed = this._parseNumber(this._$this.val());
            if (parsed === false)
                parsed = this._currentValue;
            this._$this.val(parsed);
            this._$this.val(this._checkMaxMin(this._$this.val()));
            this._copyValueToNew();
        }
    }
//public methods here
    members.methods = {
        refresh: function(that) {
            that._copyAttributes();
        },
        rebuild: function(that) {
            that._delete();
            that._generate();
        },
        clear: function(that) {
            that._delete();
        }
    };
    //everything below should be left as it is
    $.fn[pluginName] = function(options, args) {
        if (typeof options === "string") {
            var valToRet = null;
            var ret = this.each(function() {
                var data = $(this).data('data' + pluginName);
                if (typeof data === 'object') {
                    if (data.methods[options]) {
                        valToRet = data.methods[options](data, args);
                    }
                }
            });
            return valToRet || ret;
        }
        else {
            return this.each(function() {
                var settings = $.extend({}, defaultSettings, options);
                if (typeof $(this).data('data' + pluginName) === 'undefined') {
                    var dataToSave = Object.create(members);
                    dataToSave._settings = Object.create(settings);
                    $(this).data('data' + pluginName, dataToSave);
                    dataToSave.init(this);
                }
            });
        }
    };
}(jQuery));
if (!Object.create) {
    Object.create = (function() {
        function F() {
        }

        return function(o) {
            if (arguments.length != 1) {
                throw new Error('Object.create implementation only accepts one parameter.');
            }
            F.prototype = o;
            return new F()
        }
    })()
}