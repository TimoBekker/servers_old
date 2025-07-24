let Equipment = function () {
    this.dirtProps = $("#w0").serializeArray();
    this.dirtProps.shift();
    this.props = {};
    let $this = this;
    this.dirtProps.forEach(function(dirtProp){
        $this.props[dirtProp.name] = dirtProp.value;
    });
    console.dir(this);
};
Equipment.prototype.refreshWorkProps = function () {
    this.workProps = $.extend(true, {}, this.props);
};
Equipment.changedAttributes = [];
/*
* Сравнение с другим инстансом
* Если находим иденитичные значения, удаляем их из обоих наборов
* */
Equipment.prototype.compareAny = function (comparedInstance) {
    if (!comparedInstance) return;
    this.refreshWorkProps();
    comparedInstance.refreshWorkProps();
    for (let propName in this.workProps) {
        for (let ciPropName in comparedInstance.workProps) {
            if (propName === ciPropName
                && this.workProps[propName] === comparedInstance.workProps[ciPropName]) {
                delete this.workProps[propName];
                delete comparedInstance.workProps[ciPropName];
            }
        }
    }
    Equipment.changedAttributes = [];
    for (let propName in this.workProps) {
        Equipment.changedAttributes.push(propName);
    }
    for (let ciPropName in comparedInstance.workProps) {
        Equipment.changedAttributes.push(ciPropName);
    }
    Equipment.changedAttributes = $.unique(Equipment.changedAttributes);
    console.dir(this);
    console.dir(comparedInstance);
    console.dir(Equipment.changedAttributes.join(","));
};
// повесить на отправку формы сравнение
Equipment.prototype.onFormSubmit = function () {
    let $this = this;
    $("#w0").on("submit", function (event) {
        event.stopPropagation();
        event.stopImmediatePropagation();
        $this.compareAny(new Equipment());
        $(this).append("<input name='changed-attributes' type='hidden' value='"+Equipment.changedAttributes.join(",")+"'>");
        // event.preventDefault();
    });
};
(new Equipment()).onFormSubmit();