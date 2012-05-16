window.EmployeeListView = Backbone.View.extend({

    initialize:function () {
        var self = this;
        this.model.on("add", function (employee) {
            $(self.el).append(new EmployeeListItemView({model:employee}).render().el);
        });
        this.render();
    },

    render:function () {
        $(this.el).empty();
        _.each(this.model.models, function (employee) {
            $(this.el).append(new EmployeeListItemView({model:employee}).render().el);
        }, this);
        return this;
    }
});

window.EmployeeListItemView = Backbone.View.extend({

    tagName:"li",

    initialize:function () {
        this.model.on("change", this.render, this);
        this.model.on("destroy", this.destroyHandler, this);
    },

    render:function () {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    },

    destroyHandler: function() {
        $(this.el).remove();
    }

});